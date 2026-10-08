const API_TICKETS = "../../api/ticket.php";
const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets");
const formularioFiltros = document.getElementById("formFiltrosTickets");

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();
    if (!texto.trim()) throw new Error(`La API respondió sin cuerpo (HTTP ${respuesta.status}).`);
    let json;
    try { json = JSON.parse(texto); } 
    catch { throw new Error(`HTTP ${respuesta.status}: La API no devolvió JSON.`); }
    if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}: ${json.mensaje ?? "Error en la solicitud."}`);
    return json.datos;
}

async function obtenerTickets(parametros = "") {
    let url = API_TICKETS;
    if (parametros) url += `?${parametros}`;
    const respuesta = await fetch(url);
    return await leerRespuestaAPI(respuesta);
}

function agregarFilaTicket(ticket) {
    const fila = document.createElement("tr");
    fila.innerHTML = `
        <td>${ticket.id}</td>
        <td>${ticket.asunto}</td>
        <td>${ticket.tipo}</td>
        <td>${ticket.gravedad}</td>
        <td>${ticket.estado}</td>
        <td>${ticket.fechaCreacion} - ${ticket.horaCreacion}</td>
        <td>
            <form class="form-estado" onsubmit="manejarAsignacion(event, ${ticket.id}, '${ticket.idEquipo}', ${ticket.esColaborador})">
                <input type="hidden" name="idReporte" value="${ticket.id}">
                <input type="hidden" name="idEquipo" value="${ticket.idEquipo}">
                ${ticket.esColaborador 
                    ? '<input type="hidden" name="accion" value="desasignarse"><button type="submit" class="btn btn-danger">Desasignarse</button>' 
                    : '<input type="hidden" name="accion" value="asignarse"><button type="submit" class="btn btn-success">Asignarse</button>'}
            </form>
        </td>
    `;
    cuerpoTablaTickets.appendChild(fila);
}

async function actualizarTabla() {
    cuerpoTablaTickets.replaceChildren();
    try {
        const parametros = new URLSearchParams(new FormData(formularioFiltros)).toString();
        const tickets = await obtenerTickets(parametros);

        if (tickets.length === 0) {
            cuerpoTablaTickets.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted text-bold">No se encontraron tickets.</td></tr>`;
            return;
        }
        tickets.forEach(agregarFilaTicket);
    } catch (error) {
        alert("No se pudieron cargar los tickets: " + error.message);
    }
}

async function manejarAsignacion(evento, idReporte, idEquipo, esColaborador) {
    evento.preventDefault();
    const accion = esColaborador ? "desasignarse" : "asignarse";
    
    try {
        const respuesta = await fetch("../../app/controlador/tickets/procesarModificarTicket.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ idReporte, idEquipo, accion })
        });
        if (respuesta.ok) actualizarTabla();
        else alert("No se pudo actualizar la asignación.");
    } catch (error) {
        alert("Error de red: " + error.message);
    }
}

formularioFiltros.addEventListener("submit", (e) => {
    e.preventDefault();
    actualizarTabla();
});

actualizarTabla();