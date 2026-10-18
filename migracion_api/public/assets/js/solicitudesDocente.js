const API_SOLICITUDES = "../index.php?ruta=solicitudes";


// ELEMENTOS DEL DOM

const formSolicitud = document.getElementById("formSolicitud");

const asunto = document.getElementById("asunto");
const descripcion = document.getElementById("descripcion");
const fecha = document.getElementById("fecha");


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
            }`);
    }

    return json.datos;
}


// PETICIONES

async function registrarSolicitud(solicitud)
{
    const respuesta = await fetch(
        API_SOLICITUDES,{
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify(solicitud)
        });
    return leerRespuestaAPI(respuesta);
}


// SOLICITUD

function separarFechaHora(fechaHora)
{
    const partes = fechaHora.split("T");

    return {
        fechaLimite: partes[0],
        horaLimite: partes[1]
    };
}


async function gestionarSolicitud(evento)
{
    evento.preventDefault();

    const asuntoIngresado = asunto.value.trim();
    const descripcionIngresada = descripcion.value.trim();
    const fechaHoraIngresada = fecha.value;

    if (asuntoIngresado.length < 10 || asuntoIngresado.length > 30) {
        alert("El asunto debe tener entre 10 y 30 caracteres.");
        return;
    }

    if (descripcionIngresada.length < 10 || descripcionIngresada.length > 200) {
        alert("La descripción debe tener entre 10 y 200 caracteres.");
        return;
    }

    if (fechaHoraIngresada === "") {
        alert("Debe ingresar una fecha y hora.");
        return;
    }

    const fechaSeleccionada = new Date(fechaHoraIngresada);

    if (fechaSeleccionada <= new Date()) {
        alert("La fecha y hora deben ser posteriores al momento actual.");
        return;
    }

    const fechaHora = separarFechaHora(fechaHoraIngresada);

    const solicitud = {
        asunto: asuntoIngresado,
        descripcion: descripcionIngresada,
        fechaLimite: fechaHora.fechaLimite,
        horaLimite: fechaHora.horaLimite
    };

    try {
        const resultado = await registrarSolicitud(solicitud);

        alert(resultado.mensaje);

        formSolicitud.reset();

    } catch (error) {
        alert(error.message);
    }
}


// EVENTOS

formSolicitud.addEventListener("submit",gestionarSolicitud);