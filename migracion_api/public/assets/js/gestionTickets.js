const API_TICKETS = "../index.php?ruta=tickets";


// ==============================
// ELEMENTOS DEL DOM
// ==============================

const formularioFiltroTickets =
    document.getElementById("formFiltroTickets");

const entradaOrden =
    document.getElementById("orden");

const entradaGravedad =
    document.getElementById("gravedad");

const entradaTipo =
    document.getElementById("tipo");

const entradaEstado =
    document.getElementById("estado");

const entradaIdTicket =
    document.getElementById("idTicket");

const botonFiltrarTickets =
    document.getElementById("btnFiltrarTickets");

const cuerpoTablaTickets =
    document.getElementById("cuerpoTablaTickets");


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
// PETICIONES
// ==============================

async function obtenerTickets()
{
    let url = API_TICKETS;

    const parametros =
        new URLSearchParams();


    if (entradaOrden.value !== "") {
        parametros.append(
            "orden",
            entradaOrden.value
        );
    }


    if (entradaGravedad.value !== "") {
        parametros.append(
            "gravedad",
            entradaGravedad.value
        );
    }


    if (entradaTipo.value !== "") {
        parametros.append(
            "tipo",
            entradaTipo.value
        );
    }


    if (entradaEstado.value !== "") {
        parametros.append(
            "estado",
            entradaEstado.value
        );
    }


    if (entradaIdTicket.value.trim() !== "") {
        parametros.append(
            "id",
            entradaIdTicket.value.trim()
        );
    }


    const consulta =
        parametros.toString();


    if (consulta !== "") {
        url += "&" + consulta;
    }


    const respuesta =
        await fetch(url);


    return leerRespuestaAPI(respuesta);
}


// ==============================
// TABLA
// ==============================

function agregarFilaTicket(ticket)
{
    const fila =
        document.createElement("tr");


    // ID
    const campoId =
        document.createElement("td");

    campoId.textContent =
        ticket.id;


    // Asunto
 const campoAsunto =
    document.createElement("td");

campoAsunto.textContent =
    ticket.asunto;

    // Tipo
    const campoTipo =
        document.createElement("td");

    campoTipo.textContent =
        ticket.tipo;


    // Gravedad
    const campoGravedad =
        document.createElement("td");

    campoGravedad.textContent =
        ticket.gravedad;


    // Estado
    const campoEstado =
        document.createElement("td");

    campoEstado.textContent =
        ticket.estado;


    // Información de creación
    const campoInformacion =
        document.createElement("td");


    const fecha =
        document.createElement("span");

    fecha.textContent =
        "Fecha: " +
        ticket.fechaCreacion;


    const hora =
        document.createElement("span");

    hora.textContent =
        "Hora: " +
        ticket.horaCreacion;


    const docente =
        document.createElement("span");

    docente.textContent =
        "Docente: " +
        ticket.nombreDocente;


    const equipo =
        document.createElement("span");

    equipo.textContent =
        "Equipo: " +
        ticket.idEquipo;


    const ubicacion =
        document.createElement("span");

    ubicacion.textContent =
        ticket.tipoUbicacion +
        " " +
        ticket.idUbicacion;


    campoInformacion.classList.add(
        "d-flex",
        "flex-column"
    );


    campoInformacion.appendChild(fecha);
    campoInformacion.appendChild(hora);
    campoInformacion.appendChild(docente);
    campoInformacion.appendChild(equipo);
    campoInformacion.appendChild(ubicacion);


    const campoAsignacion =
    document.createElement("td");


const botonAsignacion =
    document.createElement("button");

botonAsignacion.type = "button";

// Detalle
const campoDetalle =
    document.createElement("td");


const enlaceDetalle =
    document.createElement("a");

enlaceDetalle.href =
    "./detalleTicket.html?id=" +
    ticket.id;

enlaceDetalle.textContent =
    "Ver detalle";

enlaceDetalle.classList.add(
    "btn",
    "btn-primary",
    "btn-sm"
);


campoDetalle.appendChild(
    enlaceDetalle
);

if (Number(ticket.esColaborador) === 1) {

    botonAsignacion.textContent =
        "Desasignarme";

    botonAsignacion.classList.add(
        "btn",
        "btn-danger"
    );


    botonAsignacion.addEventListener(
        "click",
        function ()
        {
            gestionarAsignacion(
                ticket.id,
                "desasignarse"
            );
        }
    );

} else {

    botonAsignacion.textContent =
        "Asignarme";

    botonAsignacion.classList.add(
        "btn",
        "btn-success"
    );


    botonAsignacion.addEventListener(
        "click",
        function ()
        {
            gestionarAsignacion(
                ticket.id,
                "asignarse"
            );
        }
    );
}


campoAsignacion.appendChild(
    botonAsignacion
);


fila.appendChild(campoId);
fila.appendChild(campoAsunto);
fila.appendChild(campoTipo);
fila.appendChild(campoGravedad);
fila.appendChild(campoEstado);
fila.appendChild(campoInformacion);
fila.appendChild(campoDetalle);
fila.appendChild(campoAsignacion);


    cuerpoTablaTickets.appendChild(fila);
}


function mostrarSinTickets()
{
    const fila =
        document.createElement("tr");

    const campo =
        document.createElement("td");


    campo.colSpan = 8;

    campo.textContent =
        "No se encontraron tickets.";

    campo.classList.add(
        "text-center",
        "py-4"
    );


    fila.appendChild(campo);

    cuerpoTablaTickets.appendChild(fila);
}

async function modificarAsignacionTicket(
    idTicket,
    accion
) {
    const respuesta = await fetch(
        API_TICKETS,
        {
            method: "PUT",

            headers: {
                "Content-Type":
                    "application/json",

                "X-CSRF-Token":
                    sessionStorage.getItem(
                        "csrfToken"
                    ) ?? ""
            },

            body: JSON.stringify({
                idTicket,
                accion
            })
        }
    );

    return leerRespuestaAPI(respuesta);
}

async function gestionarAsignacion(
    idTicket,
    accion
) {
    try {

        const resultado =
            await modificarAsignacionTicket(
                idTicket,
                accion
            );


        alert(resultado.mensaje);


        /*
         * Recargamos la tabla para que
         * el botón cambie automáticamente.
         */
        await actualizarTablaTickets();


    } catch (error) {

        alert(
            "No se pudo modificar la asignación: " +
            error.message
        );
    }
}

async function actualizarTablaTickets()
{
    cuerpoTablaTickets.replaceChildren();


    try {

        const tickets =
            await obtenerTickets();


        if (tickets.length === 0) {
            mostrarSinTickets();
            return;
        }


        for (const ticket of tickets) {
            agregarFilaTicket(ticket);
        }


    } catch (error) {

        console.error(error);

        mostrarSinTickets();

        alert(
            "No se pudieron cargar los tickets: " +
            error.message
        );
    }
}


// ==============================
// FILTROS
// ==============================

async function filtrarTickets(evento)
{
    evento.preventDefault();

    await actualizarTablaTickets();
}


// ==============================
// EVENTOS
// ==============================

botonFiltrarTickets.addEventListener(
    "click",
    filtrarTickets
);


formularioFiltroTickets.addEventListener(
    "submit",
    filtrarTickets
);


// ==============================
// INICIALIZACIÓN
// ==============================

actualizarTablaTickets();