const API_TICKETS = "../index.php/tickets";
const API_SOLICITUDES = "../index.php/solicitudes";

const tablaTickets = document.getElementById("tablaTickets");
const tablaSolicitudes = document.getElementById("tablaSolicitudes");
const filtroEstadoTickets = document.getElementById("filtroEstadoTickets");
const filtroEstadoSolicitudes = document.getElementById("filtroEstadoSolicitudes");

document.addEventListener("DOMContentLoaded", () => {
    cargarTickets();
    cargarSolicitudes();
});


filtroEstadoTickets.addEventListener("change", cargarTickets);
filtroEstadoSolicitudes.addEventListener("change", cargarSolicitudes);


async function cargarTickets() {
    try {
        let url = API_TICKETS + "/propios";
        const estado = filtroEstadoTickets.value;

        if (estado !== "") {
            url += "/filtros/estado/" + encodeURIComponent(estado);
        }


        const respuesta = await fetch(url);

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(datos.mensaje ?? "No se pudieron cargar los tickets.");
        }

        mostrarTickets(datos.datos);

    } catch (error) {
        console.error("Error al cargar los tickets:", error);
    }
}


async function cargarSolicitudes() {
    try {
        let url = API_SOLICITUDES + "/propias";
        const estado = filtroEstadoSolicitudes.value;

        if (estado !== "") {
            url += "/filtros/estado/" + encodeURIComponent(estado);
        }

        const respuesta = await fetch(url);
        const datos = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(datos.mensaje ?? "No se pudieron cargar las solicitudes.");
        }

        mostrarSolicitudes(datos.datos);

    } catch (error) {
        console.error("Error al cargar las solicitudes:", error);
    }
}


function mostrarTickets(tickets) {
    tablaTickets.innerHTML = "";

    if (tickets.length === 0) {
        mostrarFilaVacia(tablaTickets, 8, "No tenés tickets registrados.");
        return;
    }

    tickets.forEach(ticket => {
        const fila = document.createElement("tr");

        agregarCelda(fila, ticket.id);
        agregarCelda(fila, ticket.asunto);
        agregarCelda(fila, ticket.idEquipo !== null ? `Equipo #${ticket.idEquipo}` : "-");
        agregarCelda(fila, capitalizar(ticket.tipo));
        agregarCelda(fila, capitalizar(ticket.gravedad));
        agregarCeldaEstadoTicket(fila, ticket.estado);
        agregarCelda(fila, formatearFechaHora(ticket.fechaCreacion, ticket.horaCreacion));
        agregarCelda(fila, ticket.justificacion ?? "-");

        tablaTickets.appendChild(fila);
    });
}


function mostrarSolicitudes(solicitudes) {
    tablaSolicitudes.innerHTML = "";

    if (solicitudes.length === 0) {
        mostrarFilaVacia(tablaSolicitudes, 7, "No tenés solicitudes registradas.");
        return;
    }


    solicitudes.forEach(solicitud => {

        const fila = document.createElement("tr");

        agregarCelda(fila, solicitud.id);
        agregarCelda(fila, solicitud.asunto);
        agregarCelda(fila, formatearFechaHora(solicitud.fechaIngreso, solicitud.horaIngreso));
        agregarCelda(fila, formatearFechaHora(solicitud.fechaLimite, solicitud.horaLimite));
        agregarCeldaEstadoSolicitud(fila, solicitud.finalizada);
        agregarCelda(fila, solicitud.nombreTecnico ?? "-");

        if (esVerdadero(solicitud.finalizada)) {
            agregarCelda(fila, formatearFechaHora(solicitud.fechaFinalizacion, solicitud.horaFinalizacion));

        } else {
            agregarCelda(fila, "-");
        }
        tablaSolicitudes.appendChild(fila);
    });
}

function agregarCelda(fila, texto) {
    const celda = document.createElement("td");
    celda.textContent = texto ?? "-";
    fila.appendChild(celda);
}

function agregarCeldaEstadoTicket(fila, estado) {
    const celda = document.createElement("td");

    const etiqueta = document.createElement("span");

    etiqueta.textContent = capitalizar(estado);
    etiqueta.classList.add("badge");

    switch (estado) {
        case "pendiente":
            etiqueta.classList.add("bg-danger");
            break;
        case "en proceso":
            etiqueta.classList.add("bg-warning", "text-dark");
            break;

        case "resuelto":
            etiqueta.classList.add("bg-success");
            break;
        default: etiqueta.classList.add("bg-secondary");
    }

    celda.appendChild(etiqueta);
    fila.appendChild(celda);
}

function agregarCeldaEstadoSolicitud(fila, finalizada) {
    const celda = document.createElement("td");
    const etiqueta = document.createElement("span");
    etiqueta.classList.add("badge");

    if (esVerdadero(finalizada)) {
        etiqueta.textContent = "Finalizada";
        etiqueta.classList.add("bg-success");
    } else {

        etiqueta.textContent = "Pendiente";
        etiqueta.classList.add("bg-warning", "text-dark");
    }

    celda.appendChild(etiqueta);
    fila.appendChild(celda);
}

function mostrarFilaVacia(tabla, columnas, mensaje) {
    const fila = document.createElement("tr");
    const celda = document.createElement("td");

    celda.colSpan = columnas;
    celda.textContent = mensaje;
    celda.classList.add("text-center", "text-muted", "py-4");


    fila.appendChild(celda);
    tabla.appendChild(fila);
}


function formatearFechaHora(fecha, hora) {
    if (!fecha) {
        return "-";
    }

    const partes = fecha.split("-");

    if (partes.length !== 3) {
        return fecha;
    }

    const fechaFormateada = `${partes[2]}/${partes[1]}/${partes[0]}`;


    if (!hora) {
        return fechaFormateada;
    }
    return (fechaFormateada + " " + hora.substring(0, 5));
}


function esVerdadero(valor) {
    return (valor === true || valor === 1 || valor === "1");
}


function capitalizar(texto) {
    if (!texto) {
        return "-";
    }
    return (texto.charAt(0).toUpperCase() + texto.slice(1));
}