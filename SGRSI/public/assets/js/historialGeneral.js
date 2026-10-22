const API_HISTORIALES = "../index.php/historiales";

// ELEMENTOS DEL DOM

const tituloHistorial = document.getElementById("tituloHistorial");
const descripcionHistorial = document.getElementById("descripcionHistorial");

const filtroFecha = document.getElementById("filtroFecha");

const contenedorHistorial = document.getElementById("contenedorHistorial");
const botonVolverHistorial = document.getElementById("btnVolverHistorial");


// PARÁMETROS DE LA PÁGINA

const parametrosPagina = new URLSearchParams(window.location.search);
const tipoHistorial = parametrosPagina.get("tipo") ?? "";


// CONFIGURACIÓN

const configuracionHistoriales = {
    usuarios: {
        titulo: "Historial de usuarios",
        descripcion: "Cambios realizados sobre los usuarios del sistema."
    },

    equipos: {
        titulo: "Historial de equipos",
        descripcion: "Cambios realizados sobre los equipos del sistema."
    },

    ubicaciones: {
        titulo: "Historial de ubicaciones",
        descripcion: "Cambios realizados sobre laboratorios y talleres."
    },

    tickets: {
        titulo: "Historial de tickets",
        descripcion: "Interacciones realizadas sobre los tickets del sistema."
    },

    solicitudes: {
        titulo: "Historial de solicitudes",
        descripcion: "Registro de creación y finalización de solicitudes."
    },

    prestamos: {
        titulo: "Historial de préstamos",
        descripcion: "Registro de préstamos y devoluciones del sistema."
    }
};


// RESPUESTAS DE LA API

async function leerRespuestaAPI(respuesta)
{
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
            `HTTP ${respuesta.status}: ${
                json.mensaje ??
                "La solicitud no se pudo completar."
            }`
        );
    }

    return json.datos;
}


// PETICIONES

async function obtenerHistorial()
{
    let url = API_HISTORIALES + "/" + encodeURIComponent(tipoHistorial);

    const parametros = new URLSearchParams();

    if (filtroFecha.value !== "") {
        parametros.set("fecha", filtroFecha.value);
    }

    const consulta = parametros.toString();

    if (consulta !== "") {
        url += "?" + consulta;
    }

    const respuesta = await fetch(url);

    return leerRespuestaAPI(respuesta);
}


// VISTA

function configurarHistorial()
{
    const configuracion =
        configuracionHistoriales[tipoHistorial];

    if (!configuracion) {
        tituloHistorial.textContent = "Historial no disponible";
        descripcionHistorial.textContent = "El historial solicitado no es válido.";

        filtroFecha.disabled = true;

        return false;
    }

    tituloHistorial.textContent = configuracion.titulo;
    descripcionHistorial.textContent = configuracion.descripcion;

    return true;
}


function crearRegistroHistorial(registro)
{
    const articulo = document.createElement("article");
    articulo.classList.add( "card", "mb-3", "shadow-sm");

    const cuerpo = document.createElement("section");
    cuerpo.classList.add("card-body");

    const cabecera = document.createElement("section");
    cabecera.classList.add( "d-flex", "justify-content-between", "align-items-center", "mb-2");

    const actor = document.createElement("strong");
    actor.textContent = registro.nombreActor + " (" + registro.ciActor + ")";


    const fechaHora = document.createElement("span");
    fechaHora.classList.add("text-muted", "small");
    fechaHora.textContent =registro.fecha + " " + registro.hora;

    cabecera.appendChild(actor);
    cabecera.appendChild(fechaHora);


    const accion = document.createElement("p");

    accion.classList.add("mb-1");
    accion.textContent = registro.accion;


    cuerpo.appendChild(cabecera);
    cuerpo.appendChild(accion);


    if (registro.recurso) {
        const recurso = document.createElement("p");

        recurso.classList.add( "mb-1", "text-muted");
        recurso.textContent = "Recurso: " + registro.recurso;
        cuerpo.appendChild(recurso);
    }

    if (registro.detalle) {
        const detalle = document.createElement("p");

        detalle.classList.add( "mb-0", "small");
        detalle.textContent = registro.detalle;
        cuerpo.appendChild(detalle);
    }


    articulo.appendChild(cuerpo);

    return articulo;
}


function mostrarSinRegistros()
{
    const mensaje = document.createElement("p");

    mensaje.classList.add(
        "text-center",
        "text-muted",
        "py-4"
    );

    mensaje.textContent =
        "No se encontraron registros en el historial.";

    contenedorHistorial.appendChild(mensaje);
}


function mostrarHistorial(registros)
{
    contenedorHistorial.replaceChildren();

    if (registros.length === 0) {
        mostrarSinRegistros();
        return;
    }

    for (const registro of registros) {
        contenedorHistorial.appendChild(
            crearRegistroHistorial(registro)
        );
    }
}


// GESTIÓN DEL HISTORIAL

async function cargarHistorial()
{
    contenedorHistorial.replaceChildren();

    try {
        const registros = await obtenerHistorial();

        mostrarHistorial(registros);

    } catch (error) {
        window.alert( "No se pudo cargar el historial: " + error.message);
    }
}


async function filtrarPorFecha()
{
    await cargarHistorial();
}


function volver()
{
    if (window.history.length > 1) {
        window.history.back();
        return;
    }

    window.location.href = "./homeAdmin.html";
}


// EVENTOS

filtroFecha.addEventListener("change", filtrarPorFecha);

botonVolverHistorial.addEventListener(
    "click",
    function (evento)
    {
        evento.preventDefault();
        volver();
    }
);


// INICIALIZACIÓN

if (configurarHistorial()) {
    cargarHistorial();
}