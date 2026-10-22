const API_SOLICITUDES = "../index.php/solicitudes";

// ELEMENTOS DEL DOM

const formFiltros = document.getElementById("formFiltros");

const filtroEstado = document.getElementById("estado");
const filtroId = document.getElementById("idSolicitud");

const btnLimpiarFiltros = document.getElementById("btnLimpiarFiltros");
const cuerpoTablaSolicitudes = document.getElementById("cuerpoTablaSolicitudes");


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
            }`);
    }
    return json.datos;
}


// PETICIONES

async function obtenerSolicitudes() {
    let url = API_SOLICITUDES;
    const idSolicitud = filtroId.value.trim();
    
    if (idSolicitud !== "") {
        url += "/" + encodeURIComponent(idSolicitud);
    } else if (filtroEstado.value !== "") {
        url += "/filtros/estado/" + encodeURIComponent(filtroEstado.value);
    }

    const respuesta = await fetch(url);
    return leerRespuestaAPI(respuesta);
}


async function finalizarSolicitud(idSolicitud) {
    const respuesta = await fetch(
        API_SOLICITUDES + "/" + encodeURIComponent(idSolicitud), {
        method: "PATCH",
        headers: {
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        }
    });
    return leerRespuestaAPI(respuesta);
}
// TABLA

function crearCelda(texto) {
    const celda = document.createElement("td");
    celda.textContent = texto;
    return celda;
}


function crearBotonFinalizar(solicitud) {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.classList.add("btn", "btn-primary", "ms-1", "text-bold");
    boton.textContent = "Finalizar";
    boton.addEventListener("click", async function () {
        const confirmar = confirm("¿Desea marcar esta solicitud como finalizada?");
        if (!confirmar) {
            return;
        }

        try {
            const resultado = await finalizarSolicitud(solicitud.id);
            alert(resultado.mensaje);
            await cargarSolicitudes();

        } catch (error) {
            alert(error.message);
        }
    });

    return boton;
}


function crearFilaSolicitud(solicitud) {
    const fila = document.createElement("tr");

    fila.appendChild(crearCelda(solicitud.id));
    fila.appendChild(crearCelda(solicitud.asunto));
    fila.appendChild(crearCelda(solicitud.fechaLimite + " - " + solicitud.horaLimite));
    fila.appendChild(crearCelda(solicitud.descripcion));
    fila.appendChild(crearCelda(solicitud.nombreDocente + " - (" + solicitud.ciDocente + ")"));
    fila.appendChild(crearCelda(solicitud.finalizada == 1 ? "Finalizada" : "Pendiente"));

    const celdaFinalizacion = document.createElement("td");

    if (solicitud.finalizada == 1) {
        celdaFinalizacion.textContent = solicitud.nombreTecnico + " - " + solicitud.fechaFinalizacion + " " + solicitud.horaFinalizacion;
    } else {
        celdaFinalizacion.textContent = "Sin finalizar";
    }

    fila.appendChild(celdaFinalizacion);
    const celdaAcciones = document.createElement("td");

    if (solicitud.finalizada != 1) {
        celdaAcciones.appendChild(crearBotonFinalizar(solicitud));
    }

    fila.appendChild(celdaAcciones);
    return fila;
}


function mostrarSolicitudes(solicitudes) {
    cuerpoTablaSolicitudes.replaceChildren();

    if (solicitudes.length === 0) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");

        celda.colSpan = 8;
        celda.classList.add("text-center", "py-4", "text-muted", "text-bold");
        celda.textContent = "No se encontraron solicitudes.";

        fila.appendChild(celda);
        cuerpoTablaSolicitudes.appendChild(fila);
        return;
    }

    for (const solicitud of solicitudes) {
        cuerpoTablaSolicitudes.appendChild(crearFilaSolicitud(solicitud));
    }
}


// GESTIÓN DE SOLICITUDES

async function cargarSolicitudes() {
    try {
        const solicitudes = await obtenerSolicitudes();
        mostrarSolicitudes(solicitudes);

    } catch (error) {
        alert("No se pudieron cargar las solicitudes: " + error.message);
    }
}


function aplicarFiltros(evento) {
    evento.preventDefault();
    const id = filtroId.value.trim();

    if (id !== "" && !/^\d+$/.test(id)) {
        alert("El ID de la solicitud no es válido.");
        return;
    }

    cargarSolicitudes();
}

function limpiarFiltros() {
    filtroEstado.value = "";
    filtroId.value = "";
    cargarSolicitudes();
}


// EVENTOS

formFiltros.addEventListener("submit", aplicarFiltros);
btnLimpiarFiltros.addEventListener("click", limpiarFiltros);
// INICIALIZACIÓN

cargarSolicitudes();