const API_TICKETS = "../index.php/tickets";


// ELEMENTOS DEL DOM

const idTicket = document.getElementById("idTicket");
const tituloTicket = document.getElementById("tituloTicket");

const infoDocente = document.getElementById("infoDocente");
const infoFecha = document.getElementById("infoFecha");
const infoSalon = document.getElementById("infoSalon");
const infoPC = document.getElementById("infoPC");
const infoPersona = document.getElementById("infoPersona");

const gravedadTicket = document.getElementById("gravedadTicket");
const estadoTicket = document.getElementById("estadoTicket");

const infoCategoria = document.getElementById("infoCategoria");
const infoAsunto = document.getElementById("infoAsunto");
const infoDescripcion = document.getElementById("infoDescripcion");
const infoEncargados = document.getElementById("infoEncargados");

const bloqueJustificacion = document.getElementById("bloqueJustificacion");
const infoJustificacion = document.getElementById("infoJustificacion");

const botonGuardarCambiosTicket = document.getElementById("btnGuardarCambiosTicket");

const contenedorComentarios = document.getElementById("contenedorComentarios");
const formComentario = document.getElementById("formComentario");
const textoComentario = document.getElementById("textoComentario");
const botonAgregarComentario = document.getElementById("btnAgregarComentario");


// RESPUESTAS DE LA API

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error(
            `La API respondió sin cuerpo (HTTP ${respuesta.status}).`);
    }

    let json;
    try {
        json = JSON.parse(texto);

    } catch {
        throw new Error(
            `HTTP ${respuesta.status}: La API no devolvió JSON.`);
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
async function obtenerTicket(id) {
    const respuesta = await fetch(API_TICKETS + "/" + encodeURIComponent(id));
    return leerRespuestaAPI(respuesta);
}

async function modificarTicket(ticket) {
    const respuesta = await fetch(
        API_TICKETS + "/" + ticket.idTicket, {
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        },
        body: JSON.stringify(ticket)
    });
    return leerRespuestaAPI(respuesta);
}


async function registrarComentario(comentario)
{
    const respuesta = await fetch(
        API_TICKETS +
        "/" +
        encodeURIComponent(comentario.idTicket) +
        "/comentarios",{
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify({
                texto: comentario.texto
            })
        }
    );

    return leerRespuestaAPI(respuesta);
}


// COMENTARIOS
function agregarComentario(comentario) {
    const articulo = document.createElement("article");
    articulo.classList.add("border", "rounded-3", "p-3", "mb-3");

    const encabezado = document.createElement("section");
    encabezado.classList.add("d-flex", "justify-content-between", "mb-2", "border-bottom");

    const autor = document.createElement("strong");
    autor.textContent = comentario.nombre ?? comentario.ciTecnico;

    const fecha = document.createElement("span");
    fecha.classList.add("text-muted");
    fecha.textContent = comentario.fecha + " - " + comentario.hora;

    const texto = document.createElement("p");
    texto.classList.add("mb-0");
    texto.textContent = comentario.texto;

    encabezado.appendChild(autor);
    encabezado.appendChild(fecha);

    articulo.appendChild(encabezado);
    articulo.appendChild(texto);

    contenedorComentarios.appendChild(articulo);
}


function mostrarComentarios(comentarios) {
    contenedorComentarios.replaceChildren();

    if (comentarios.length === 0) {
        const mensaje = document.createElement("p");

        mensaje.classList.add("text-muted");
        mensaje.textContent = "Este ticket todavía no tiene comentarios.";

        contenedorComentarios.appendChild(mensaje);
        return;
    }

    for (const comentario of comentarios) {
        agregarComentario(comentario);
    }
}


// DETALLE DEL TICKET

function mostrarTicket(ticket) {
    tituloTicket.textContent = "Ticket #" + ticket.id;

    idTicket.value = ticket.id;

    infoDocente.value = ticket.nombreDocente ?? "Docente no disponible";
    infoFecha.value = ticket.fechaCreacion + " - " + ticket.horaCreacion;
    infoSalon.value = ticket.tipoUbicacion + " " + ticket.idUbicacion;
    infoPC.value = ticket.idEquipo;

    if (ticket.persona !== null && ticket.persona !== "") {
        infoPersona.value = ticket.persona;

    } else {
        infoPersona.value = "No especificada";
    }

    infoCategoria.value = ticket.tipo;
    infoAsunto.value = ticket.asunto;
    infoDescripcion.value = ticket.descripcion;

    if (ticket.colaboradores !== null && ticket.colaboradores !== "") {
        infoEncargados.value = ticket.colaboradores;

    } else {
        infoEncargados.value = "Sin técnicos asignados";
    }

    if (ticket.estado === "resuelto") {
        gravedadTicket.disabled = true;
        estadoTicket.disabled = true;

        botonGuardarCambiosTicket.classList.add("d-none");

        textoComentario.disabled = true;
        botonAgregarComentario.classList.add("d-none");

    } else {
        gravedadTicket.disabled = false;
        estadoTicket.disabled = false;

        botonGuardarCambiosTicket.classList.remove("d-none");

        textoComentario.disabled = false;
        botonAgregarComentario.classList.remove("d-none");
    }

    gravedadTicket.value = ticket.gravedad;
    estadoTicket.value = ticket.estado;

    infoJustificacion.value = ticket.justificacion ?? "";
    actualizarCampoJustificacion();

    mostrarComentarios(ticket.comentarios ?? []);
}


function obtenerIdTicket() {
    const parametros = new URLSearchParams(window.location.search);
    return parametros.get("id");
}


async function cargarDetalleTicket() {
    const id = obtenerIdTicket();

    if (id === null || id.trim() === "") {
        alert("No se recibió el ID del ticket.");
        return;
    }

    try {
        const tickets = await obtenerTicket(id);

        if (tickets.length === 0) {
            alert("El ticket solicitado no existe.");
            return;
        }

        mostrarTicket(tickets[0]);

    } catch (error) {
        alert("No se pudo cargar el ticket: " + error.message);
    }
}


// GESTIÓN DEL TICKET

async function gestionarModificacionTicket() {
    const justificacion = infoJustificacion.value.trim();

    if (estadoTicket.value === "resuelto" && justificacion === "") {
        alert("Debe ingresar una justificación para resolver el ticket.");
        return;
    }

    const ticket = {
        idTicket: idTicket.value,
        gravedad: gravedadTicket.value,
        estado: estadoTicket.value,
        justificacion: justificacion
    };

    try {
        const resultado = await modificarTicket(ticket);

        alert(resultado.mensaje);

        await cargarDetalleTicket();

    } catch (error) {
        alert(error.message);
    }
}


async function gestionarComentario() {
    const texto = textoComentario.value.trim();

    if (texto === "") {
        alert("Debe ingresar un comentario.");
        return;
    }

    const comentario = {
        idTicket: idTicket.value,
        texto: texto
    };

    try {
        const resultado = await registrarComentario(comentario);

        formComentario.reset();

        alert(resultado.mensaje);

        await cargarDetalleTicket();

    } catch (error) {
        alert(error.message);
    }
}

function actualizarCampoJustificacion() {
    if (estadoTicket.value === "resuelto") {
        bloqueJustificacion.classList.remove("d-none");
        infoJustificacion.required = true;

    } else {
        bloqueJustificacion.classList.add("d-none");
        infoJustificacion.required = false;
        infoJustificacion.value = "";
    }
}

// EVENTOS

estadoTicket.addEventListener("change", actualizarCampoJustificacion);

botonGuardarCambiosTicket.addEventListener("click", gestionarModificacionTicket);
botonAgregarComentario.addEventListener("click", gestionarComentario);

cargarDetalleTicket();