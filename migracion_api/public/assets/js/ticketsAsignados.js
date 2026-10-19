const API_TICKETS = "../index.php?ruta=tickets";


// ELEMENTOS DEL DOM

const ticketsPendientes = document.getElementById("ticketsPendientes");
const ticketsEnProceso = document.getElementById("ticketsEnProceso");
const ticketsResueltos = document.getElementById("ticketsResueltos");

// RESPUESTAS DE LA API

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error(`La API respondió sin cuerpo (HTTP ${respuesta.status}).`);
    }

    let json;

    try {
        json = JSON.parse(texto);
    } catch {
        throw new Error(`HTTP ${respuesta.status}: La API no devolvió JSON.`);
    }

    if (!respuesta.ok) {
        throw new Error(
            `HTTP ${respuesta.status}: ${json.mensaje ??
            "La solicitud no se pudo completar."
            }`
        );
    }

    return json.datos;
}


// PETICIONES

async function obtenerTicketsAsignados() {
    const respuesta = await fetch(API_TICKETS + "&asignados=1");
    return leerRespuestaAPI(respuesta);
}


// TICKETS

function crearTarjetaTicket(ticket) {
    const articulo = document.createElement("article");
    articulo.classList.add("border", "rounded-3", "bg-white", "p-3", "mb-3");

    const enlace = document.createElement("a");

    enlace.href = "./detalleTicket.html?id=" + ticket.id;
    enlace.classList.add("text-decoration-none", "text-dark", "d-block");

    const identificador = document.createElement("span");

    identificador.classList.add("text-muted", "d-block", "mb-1");
    identificador.textContent = "Ticket #" + ticket.id;

    const asunto = document.createElement("strong");

    asunto.classList.add("d-block");
    asunto.textContent = ticket.asunto;

    enlace.appendChild(identificador);
    enlace.appendChild(asunto);

    articulo.appendChild(enlace);

    return articulo;
}


function mostrarSinTickets(contenedor) {
    const mensaje = document.createElement("p");

    mensaje.classList.add("text-center", "text-muted", "py-3");
    mensaje.textContent = "No hay tickets en este estado.";

    contenedor.appendChild(mensaje);
}


function mostrarTickets(tickets) {
    ticketsPendientes.replaceChildren();
    ticketsEnProceso.replaceChildren();
    ticketsResueltos.replaceChildren();

    let cantidadPendientes = 0;
    let cantidadEnProceso = 0;
    let cantidadResueltos = 0;

    for (const ticket of tickets) {

        if (ticket.estado === "pendiente") {
            ticketsPendientes.appendChild(crearTarjetaTicket(ticket));
            cantidadPendientes++;
        }

        if (ticket.estado === "en proceso") {
            ticketsEnProceso.appendChild(crearTarjetaTicket(ticket));
            cantidadEnProceso++;
        }

        if (ticket.estado === "resuelto") {
            ticketsResueltos.appendChild(crearTarjetaTicket(ticket));
            cantidadResueltos++;
        }
    }

    if (cantidadPendientes === 0) {
        mostrarSinTickets(ticketsPendientes);
    }

    if (cantidadEnProceso === 0) {
        mostrarSinTickets(ticketsEnProceso);
    }

    if (cantidadResueltos === 0) {
        mostrarSinTickets(ticketsResueltos);
    }
}


// INICIALIZACIÓN

async function cargarTicketsAsignados() {
    try {
        const tickets = await obtenerTicketsAsignados();
        mostrarTickets(tickets);
    } catch (error) {
        alert("No se pudieron cargar los tickets asignados: " + error.message);
    }
}


cargarTicketsAsignados();