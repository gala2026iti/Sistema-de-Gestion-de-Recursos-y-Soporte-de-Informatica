const API_TICKETS_PERSONALES = "../../api/ticket.php";

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();
    if (!texto.trim()) throw new Error("La API respondió sin cuerpo.");
    const json = JSON.parse(texto);
    if (!respuesta.ok) throw new Error(json.mensaje || "Error al obtener datos.");
    return json.datos;
}

async function actualizarKanban() {
    const cuerpoPendiente = document.getElementById("cuerpoPendiente");
    const cuerpoEnProceso = document.getElementById("cuerpoEnProceso");
    const cuerpoResuelto = document.getElementById("cuerpoResuelto");

    cuerpoPendiente.replaceChildren();
    cuerpoEnProceso.replaceChildren();
    cuerpoResuelto.replaceChildren();

    try {
        const respuesta = await fetch(API_TICKETS_PERSONALES);
        const tickets = await leerRespuestaAPI(respuesta);

        if (tickets.length === 0) return;

        tickets.forEach(ticket => {
            const fila = document.createElement("tr");
            fila.innerHTML = `<td class="ticket-marcado"><a href="detalleTicket.html?id=${ticket.id}" class="text-decoration-none text-dark d-block w-100 h-100 py-2">${ticket.asunto}</a></td>`;

            if (ticket.estado === "pendiente") {
                cuerpoPendiente.appendChild(fila);
            } else if (ticket.estado === "en proceso") {
                cuerpoEnProceso.appendChild(fila);
            } else if (ticket.estado === "resuelto") {
                cuerpoResuelto.appendChild(fila);
            }
        });
    } catch (error) {
        console.error("Error al cargar el tablero Kanban:", error.message);
    }
}

actualizarKanban();