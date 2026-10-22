const API_TICKETS = "../index.php/tickets";

const tablaPendiente = document.querySelector("#tablaPendiente tbody");
const tablaEnProceso = document.querySelector("#tablaEnProceso tbody");
const tablaResuelto = document.querySelector("#tablaResuelto tbody");


document.addEventListener("DOMContentLoaded", () => {
    cargarTickets();
});


async function cargarTickets() {
    try {

        const respuesta = await fetch(API_TICKETS);

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(datos.mensaje ?? "No se pudieron cargar los tickets.");
        }
        mostrarTickets(datos.datos);

    } catch (error) {

        console.error("Error al cargar los tickets:", error);
    }
}


function mostrarTickets(tickets) {
    tablaPendiente.innerHTML = "";
    tablaEnProceso.innerHTML = "";
    tablaResuelto.innerHTML = "";

    tickets.forEach(ticket => {

        const fila = crearTicket(ticket);

        switch (ticket.estado) {

            case "pendiente":
                tablaPendiente.appendChild(fila);
                break;

            case "en proceso":
                tablaEnProceso.appendChild(fila);
                break;

            case "resuelto":
                tablaResuelto.appendChild(fila);
                break;
        }

    });
}


function crearTicket(ticket) {
    const fila = document.createElement("tr");
    const celda = document.createElement("td");
    celda.classList.add("p-3", "border-bottom");

    celda.innerHTML = `
        <article class="card shadow-sm border-0 p-3 text-start">

            <h3 class="h6 fw-bold">
                Ticket #${ticket.id}
            </h3>

            <p class="mb-1">
                <strong>Asunto:</strong>
                ${ticket.asunto}
            </p>

            <p class="mb-1">
                <strong>Tipo:</strong>
                ${ticket.tipo}
            </p>

            <p class="mb-1">
                <strong>Gravedad:</strong>
                ${ticket.gravedad}
            </p>

            <p class="mb-0">
                <strong>Fecha:</strong>
                ${ticket.fechaCreacion}
            </p>

        </article>
    `;

    fila.appendChild(celda);

    return fila;
}