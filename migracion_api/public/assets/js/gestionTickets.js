const API_TICKETS = "../api/ticket.php";

const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets");

const formularioFiltroTickets = document.getElementById("formFiltroTickets");

const entradaTiempo = document.getElementById("orden");
const entradaGravedad = document.getElementById("gravedad");
const entradaClasificacion = document.getElementById("tipo");
const entradaEstado = document.getElementById("estado");

/**
 * Lee la respuesta recibida desde la API.
 */
async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error(
            `La API respondió sin cuerpo (HTTP ${respuesta.status}).`
        );
    }

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(
            `HTTP ${respuesta.status}: La API no devolvió JSON.`
        );
    }

    if (!respuesta.ok) {
        throw new Error(
            `HTTP ${respuesta.status}: ${
                json.mensaje ?? "La solicitud no se pudo completar."
            }`
        );
    }

    return json.datos;
}

async function obtenerTickets(orden = "", gravedad = "", clasificacion = "", estado = "") {
    let url = API_TICKETS;

    const parametros = new URLSearchParams();

    if (orden !== "") {
        parametros.append("tiempo", orden);
        parametros.append("orden", orden);
    }
    if (gravedad !== "") {
        parametros.append("gravedad", gravedad);
    }
    if (clasificacion !== "") {
        parametros.append("clasificacion", clasificacion);
    }
    if (estado !== "") {
        parametros.append("estado", estado);
    }

    if (parametros.toString()) {
        url += `?${parametros.toString()}`;
    }

    const respuesta = await fetch(url);

    return await leerRespuestaAPI(respuesta);
}

const agregarFilaTicket = (ticket) => {
    const fila = document.createElement("tr");

    const id = document.createElement("td");
    id.textContent = ticket.id ?? "";

    const asunto = document.createElement("td");
    asunto.textContent = ticket.asunto ?? "";

    const tipo = document.createElement("td");
    tipo.textContent = ticket.tipo ?? "";

    const gravedad = document.createElement("td");
    gravedad.textContent = ticket.gravedad ?? "";

    const estado = document.createElement("td");
    estado.textContent = ticket.estado ?? "";

    const fechaCreacion = document.createElement("td");
    const fecha = [ticket.fechaCreacion, ticket.horaCreacion]
        .filter(Boolean)
        .join(" - ");
    fechaCreacion.textContent = fecha || "-";

    const asignacion = document.createElement("td");
    asignacion.textContent = ticket.asignadoA ?? "Sin asignar";

    fila.appendChild(id);
    fila.appendChild(asunto);
    fila.appendChild(tipo);
    fila.appendChild(gravedad);
    fila.appendChild(estado);
    fila.appendChild(fechaCreacion);
    fila.appendChild(asignacion);

    cuerpoTablaTickets.appendChild(fila);
};

const mostrarSinTickets = () => {
    const fila = document.createElement("tr");

    const mensaje = document.createElement("td");
    mensaje.setAttribute("colspan", "7");
    mensaje.textContent = "No se encontraron tickets.";

    fila.appendChild(mensaje);
    cuerpoTablaTickets.appendChild(fila);
}

async function actualizarTabla() {
    cuerpoTablaTickets.innerHTML = "";

    const tiempo = entradaTiempo.value;
    const gravedad = entradaGravedad.value;
    const clasificacion = entradaClasificacion.value;
    const estado = entradaEstado.value;

    try {
        const tickets = await obtenerTickets(tiempo, gravedad, clasificacion, estado);
        const listaTickets = Array.isArray(tickets) ? tickets : [];

        if (listaTickets.length === 0) {
            mostrarSinTickets();
        } else {
            listaTickets.forEach(agregarFilaTicket);
        }
    } catch (error) {
        console.error("Error al obtener los tickets:", error);
        mostrarSinTickets();
    }
}

async function filtrarTickets(eventoFormulario) {
    eventoFormulario.preventDefault();
    await actualizarTabla();
}

formularioFiltroTickets.addEventListener("submit", filtrarTickets);

// Carga inicial de la tabla al abrir la página.
actualizarTabla();