const API_TICKETS = "../index.php?ruta=tickets";


// ==============================
// ELEMENTOS DEL DOM
// ==============================
const idTicket =
    document.getElementById("idTicket");
const tituloTicket =
    document.getElementById("tituloTicket");

const contenedorAsunto =
    document.getElementById("contenedorAsunto");

const infoDocente =
    document.getElementById("infoDocente");

const infoFecha =
    document.getElementById("infoFecha");

const infoSalon =
    document.getElementById("infoSalon");

const infoPC =
    document.getElementById("infoPC");

const infoPersona =
    document.getElementById("infoPersona");

const gravedadTicket =
    document.getElementById("gravedadTicket");

const estadoTicket =
    document.getElementById("estadoTicket");

const infoCategoria =
    document.getElementById("infoCategoria");

const infoAsunto =
    document.getElementById("infoAsunto");

const infoDescripcion =
    document.getElementById("infoDescripcion");

const infoEncargados =
    document.getElementById("infoEncargados");

const bloqueJustificacion =
    document.getElementById("bloqueJustificacion");

const infoJustificacion =
    document.getElementById("infoJustificacion");


// ==============================
// RESPUESTAS DE LA API
// ==============================

async function leerRespuestaAPI(respuesta)
{
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
                json.mensaje ??
                "La solicitud no se pudo completar."
            }`
        );
    }

    return json.datos;
}


// ==============================
// PETICIÓN
// ==============================

async function obtenerTicket(idTicket)
{
    const respuesta = await fetch(
        API_TICKETS +
        "&id=" +
        encodeURIComponent(idTicket)
    );

    return leerRespuestaAPI(respuesta);
}

function mostrarTicket(ticket)
{
    tituloTicket.textContent =
        "Ticket #" + ticket.id;


    idTicket.value =
        ticket.id;


    infoDocente.value =
        ticket.nombreDocente ??
        "Docente no disponible";


    infoFecha.value =
        ticket.fechaCreacion +
        " - " +
        ticket.horaCreacion;


    infoSalon.value =
        ticket.tipoUbicacion +
        " " +
        ticket.idUbicacion;


    infoPC.value =
        ticket.idEquipo;


    if (
        ticket.persona !== null &&
        ticket.persona !== ""
    ) {
        infoPersona.value =
            ticket.persona;

    } else {

        infoPersona.value =
            "No especificada";
    }


    infoCategoria.value =
        ticket.tipo;


    infoAsunto.value =
        ticket.asunto;


    infoDescripcion.value =
        ticket.descripcion;


    if (
        ticket.colaboradores !== null &&
        ticket.colaboradores !== ""
    ) {
        infoEncargados.value =
            ticket.colaboradores;

    } else {

        infoEncargados.value =
            "Sin técnicos asignados";
    }


    gravedadTicket.value =
        ticket.gravedad;


    estadoTicket.value =
        ticket.estado;


    if (
        ticket.justificacion !== null &&
        ticket.justificacion !== ""
    ) {
        infoJustificacion.value =
            ticket.justificacion;

        bloqueJustificacion.classList.remove(
            "d-none"
        );

    } else {

        bloqueJustificacion.classList.add(
            "d-none"
        );
    }
}

function obtenerIdTicket()
{
    const parametros =
        new URLSearchParams(
            window.location.search
        );

    return parametros.get("id");
}


async function cargarDetalleTicket()
{
    const idTicket =
        obtenerIdTicket();


    if (
        idTicket === null ||
        idTicket.trim() === ""
    ) {
        alert(
            "No se recibió el ID del ticket."
        );

        return;
    }


    try {

        const tickets =
            await obtenerTicket(idTicket);


        if (tickets.length === 0) {

            alert(
                "El ticket solicitado no existe."
            );

            return;
        }


        mostrarTicket(
            tickets[0]
        );


    } catch (error) {

        alert(
            "No se pudo cargar el ticket: " +
            error.message
        );
    }
}


cargarDetalleTicket();