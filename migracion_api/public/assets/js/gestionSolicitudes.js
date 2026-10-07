/**
 * CONSTANTES NECESARIAS
 */

// Ruta de la API de solicitudes.
// La ruta es relativa al HTML desde donde se carga este archivo.
const API_SOLICITUDES = "../../api/solicitud.php";

// Cuerpo de la tabla donde se cargarán las solicitudes.
const cuerpoTablaSolicitudes = document.getElementById("cuerpoTablaSolicitudes");

// Formulario utilizado para filtrar las solicitudes.
const formularioFiltroSolicitudes = document.getElementById("formFiltroSolicitudes");

// Select utilizado para seleccionar el estado.
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
            `HTTP ${respuesta.status}: ${json.mensaje ?? "La solicitud no se pudo completar."}`
        );
    }

    // RespuestaJson devuelve los datos dentro de la clave "datos".
    return json.datos;
}


/**
 * GET - Obtiene las solicitudes registradas.
 *
 * Si se recibe un estado, se agrega a la URL
 * para que la API filtre las solicitudes.
 */
async function obtenerSolicitudes(estado = "") {
    let url = API_SOLICITUDES;

    if (estado !== "") {
        url += `?estado=${encodeURIComponent(estado)}`;
    }

    const respuesta = await fetch(url);

    return await leerRespuestaAPI(respuesta);
}

/**
 * Crea una fila de la tabla a partir de una solicitud.
 */
function agregarFilaSolicitud(solicitud) {
    const fila = document.createElement("tr");

    const campoId = document.createElement("td");
    campoId.textContent = solicitud.id;

    const campoAsunto = document.createElement("td");
    campoAsunto.textContent = solicitud.asunto;

    const campoFechaLimite = document.createElement("td");
    campoFechaLimite.textContent =
        solicitud.fechaLimite + " - " + solicitud.horaLimite;

    const campoDescripcion = document.createElement("td");
    campoDescripcion.textContent = solicitud.descripcion;

    const campoDocente = document.createElement("td");
    campoDocente.textContent =
        solicitud.nombre + " - ( " + solicitud.ciDocente + " )";

    const campoFinalizacion = document.createElement("td");

    if (Number(solicitud.finalizada) === 1) {
        campoFinalizacion.textContent = "Finalizada";
    } else {
        campoFinalizacion.textContent = "Pendiente";
    }

    fila.appendChild(campoId);
    fila.appendChild(campoAsunto);
    fila.appendChild(campoFechaLimite);
    fila.appendChild(campoDescripcion);
    fila.appendChild(campoDocente);
    fila.appendChild(campoFinalizacion);

    cuerpoTablaSolicitudes.appendChild(fila);
}


/**
 * Muestra un mensaje cuando la API no devuelve solicitudes.
 */
function mostrarSinSolicitudes() {
    const fila = document.createElement("tr");

    const campoMensaje = document.createElement("td");
    campoMensaje.colSpan = 6;
    campoMensaje.textContent = "No se encontraron solicitudes.";

    campoMensaje.classList.add(
        "text-center",
        "py-4",
        "text-muted",
        "text-bold"
    );

    fila.appendChild(campoMensaje);
    cuerpoTablaSolicitudes.appendChild(fila);
}


/**
 * Solicita nuevamente las solicitudes a la API
 * y genera las filas de la tabla.
 */
async function actualizarTabla() {
    // Elimina las filas que haya actualmente.
    cuerpoTablaSolicitudes.replaceChildren();

    try {
        const estado = entradaEstado.value;

        // GET /api/solicitud.php
        const solicitudes = await obtenerSolicitudes(estado);

        if (solicitudes.length === 0) {
            mostrarSinSolicitudes();
            return;
        }

        // Genera una fila por cada solicitud recibida.
        for (const solicitud of solicitudes) {
            agregarFilaSolicitud(solicitud);
        }

    } catch (error) {
        window.alert(
            "No se pudieron cargar las solicitudes: " + error.message
        );
    }
}


/**
 * FILTROS
 */

/**
 * Evita que el formulario recargue la página y vuelve
 * a consultar las solicitudes utilizando el estado seleccionado.
 */
async function filtrarSolicitudes(eventoFormulario) {
    eventoFormulario.preventDefault();

    await actualizarTabla();
}


/**
 * EVENTOS
 */

// Al enviar el formulario de filtros.
formularioFiltroSolicitudes.addEventListener(
    "submit",
    filtrarSolicitudes
);

// GET inicial de solicitudes.
actualizarTabla();