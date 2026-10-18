const API_EQUIPOS = "../index.php?ruta=equipos";
const API_UBICACIONES = "../index.php?ruta=ubicaciones";


// ELEMENTOS DEL DOM

const cuerpoTablaEquipos = document.getElementById("cuerpoTablaEquipos");
const formularioFiltroEquipos = document.getElementById("formFiltroEquipos");

const entradaEstado = document.getElementById("estado");
const entradaOrden = document.getElementById("orden");
const entradaIdEquipoFiltro = document.getElementById("idEquipoFiltro");
const filtroUbicacion = document.getElementById("filtroUbicacion");
const filtroTipoUbicacion = document.getElementById("filtroTipoUbicacion");

const listaLaboratorios = document.getElementById("listaLaboratorios");
const listaTalleres = document.getElementById("listaTalleres");
const grupoLaboratorios = document.getElementById("grupoLaboratorios");
const grupoTalleres = document.getElementById("grupoTalleres");
const filtroPrestamo = document.getElementById("filtroPrestamo");
const filtroTodosEquipos = document.getElementById("filtroTodosEquipos");

const botonAbrirUbicaciones = document.getElementById("btnAbrirUbicaciones");
const botonCerrarUbicaciones = document.getElementById("btnCerrarUbicaciones");
const barraLateralSalones = document.getElementById("barraLateralSalones");
const botonAgregarLaboratorio = document.getElementById("btnAgregarLaboratorio");
const botonAgregarTaller = document.getElementById("btnAgregarTaller");

const botonRegistrarEquipo = document.getElementById("btnRegistrarEquipo");
const modalEquipo = document.getElementById("modalEquipo");
const formularioEquipo = document.getElementById("formEquipo");
const tituloFormEquipo = document.getElementById("tituloFormEquipo");
const botonGuardarEquipo = document.getElementById("btnGuardarEquipo");
const botonCancelarEquipo = document.getElementById("btnCancelarEquipo");

const entradaIdEquipo = document.getElementById("idPC");
const entradaUbicacionEquipo = document.getElementById("ubicacionEquipo");
const contenedorLugar = document.getElementById("contenedorLugar");
const entradaPosicion = document.getElementById("posicionPC");

const mensajeExito = document.getElementById("mensajeExito");
const mensajeError = document.getElementById("mensajeError");


// ESTADO

let rolActualEquipos = "";
let modoFormularioEquipo = "registro";


// RESPUESTAS DE LA API

async function leerRespuestaAPI(respuesta)
{
    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error( `La API respondió sin cuerpo (HTTP ${respuesta.status}).` );
    }

    let json;

    try {
        json = JSON.parse(texto);

    } catch {
        throw new Error(`HTTP ${respuesta.status}: La API no devolvió JSON.` );
    }

    if (!respuesta.ok) {
        throw new Error( `HTTP ${respuesta.status}: ${json.mensaje ?? "La solicitud no se pudo completar."}`);
    }

    return json.datos;
}


// PETICIONES DE EQUIPOS

async function obtenerEquipos()
{
    let url = API_EQUIPOS;

    const parametros = new URLSearchParams();

    if (rolActualEquipos === "administrador" && entradaEstado.value !== "") {
        parametros.append("estado", entradaEstado.value);
    }

    if (entradaOrden.value !== "") {
        parametros.append("orden", entradaOrden.value);
    }

    if (entradaIdEquipoFiltro.value.trim() !== "") {
        parametros.append("id", entradaIdEquipoFiltro.value.trim());
    }

    if (filtroUbicacion.value !== "") {
        parametros.append("ubicacion", filtroUbicacion.value);
    }

    if (filtroTipoUbicacion.value !== "") {
        parametros.append("tipoUbicacion", filtroTipoUbicacion.value);
    }

    const consulta = parametros.toString();

    if (consulta !== "") {
        url += "&" + consulta;
    }

    const respuesta = await fetch(url);

    return leerRespuestaAPI(respuesta);
}

async function registrarEquipo(equipo)
{
    const respuesta = await fetch(
        API_EQUIPOS,{
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify(equipo)
        });

    return leerRespuestaAPI(respuesta);
}


async function modificarUbicacionEquipo(equipo)
{
    const respuesta = await fetch(
        API_EQUIPOS,{
            method: "PUT",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify(equipo)
        });

    return leerRespuestaAPI(respuesta);
}


async function cambiarEstadoEquipo(idEquipo, accion)
{
    const respuesta = await fetch(
        API_EQUIPOS,{
            method: "PATCH",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify({
                idEquipo,
                accion
            })
        });

    return leerRespuestaAPI(respuesta);
}

// PETICIONES DE UBICACIONES

async function obtenerUbicaciones()
{
    const respuesta = await fetch(API_UBICACIONES);

    return leerRespuestaAPI(respuesta);
}


async function registrarUbicacion(tipo)
{
    const respuesta = await fetch(
        API_UBICACIONES,{
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify({
                tipo
            })
        });

    return leerRespuestaAPI(respuesta);
}


async function eliminarUbicacion(ubicacion)
{
    const respuesta = await fetch(
        API_UBICACIONES,{
            method: "DELETE",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },
            body: JSON.stringify({
                id: ubicacion.id,
                tipo: ubicacion.tipo
            })
        });

    return leerRespuestaAPI(respuesta);
}


// MENSAJES

function ocultarMensajes()
{
    mensajeExito.textContent = "";
    mensajeError.textContent = "";

    mensajeExito.classList.add("d-none");
    mensajeError.classList.add("d-none");
}


function mostrarMensajeExito(mensaje)
{
    ocultarMensajes();

    mensajeExito.textContent = mensaje;
    mensajeExito.classList.remove("d-none");
}


function mostrarMensajeError(mensaje)
{
    ocultarMensajes();

    mensajeError.textContent = mensaje;
    mensajeError.classList.remove("d-none");
}


// BARRA LATERAL Y UBICACIONES

function abrirMenuUbicaciones()
{
    barraLateralSalones.classList.add("abierto");
}


function cerrarMenuUbicaciones()
{
    barraLateralSalones.classList.remove("abierto");
}


function crearEnlaceUbicacion(ubicacion)
{
    const item = document.createElement("li");
    const enlace = document.createElement("a");

    enlace.href = "#";
    enlace.textContent =
        ubicacion.tipo.charAt(0).toUpperCase() +
        ubicacion.tipo.slice(1) +
        " " +
        ubicacion.id;

    enlace.addEventListener("click", async function (evento) {
        evento.preventDefault();

        filtroTipoUbicacion.value = ubicacion.tipo;
        filtroUbicacion.value = ubicacion.id;

        cerrarMenuUbicaciones();
        await actualizarTablaEquipos();
    });

    item.appendChild(enlace);

    if (rolActualEquipos === "administrador") {
        const botonEliminar = document.createElement("button");

        botonEliminar.type = "button";
        botonEliminar.textContent = "Eliminar";
        botonEliminar.classList.add("btn-accion-salon");

        botonEliminar.addEventListener("click", function () {
            gestionarEliminarUbicacion(ubicacion);
        });

        item.appendChild(botonEliminar);
    }

    return item;
}


function crearOpcionUbicacion(ubicacion)
{
    const opcion = document.createElement("option");

    opcion.value = ubicacion.tipo + " " + ubicacion.id;

    opcion.textContent =
        ubicacion.tipo.charAt(0).toUpperCase() +
        ubicacion.tipo.slice(1) +
        " " +
        ubicacion.id;

    return opcion;
}


async function cargarUbicaciones()
{
    listaLaboratorios.replaceChildren();
    listaTalleres.replaceChildren();
    grupoLaboratorios.replaceChildren();
    grupoTalleres.replaceChildren();

    const ubicaciones = await obtenerUbicaciones();

    for (const ubicacion of ubicaciones) {

        if (ubicacion.tipo === "laboratorio") {
            listaLaboratorios.appendChild(crearEnlaceUbicacion(ubicacion));
            grupoLaboratorios.appendChild(crearOpcionUbicacion(ubicacion));
        }

        if (ubicacion.tipo === "taller") {
            listaTalleres.appendChild(crearEnlaceUbicacion(ubicacion));
            grupoTalleres.appendChild(crearOpcionUbicacion(ubicacion));
        }
    }
}


async function gestionarRegistroUbicacion(tipo)
{
    ocultarMensajes();

    try {
        const resultado = await registrarUbicacion(tipo);

        await cargarUbicaciones();

        mostrarMensajeExito(resultado.mensaje);

    } catch (error) {
        mostrarMensajeError(error.message);
    }
}


async function gestionarEliminarUbicacion(ubicacion)
{
    const confirmar = confirm(`¿Seguro que deseas eliminar ${ubicacion.tipo} ${ubicacion.id}?`);

    if (!confirmar) {
        return;
    }

    ocultarMensajes();

    try {
        const resultado = await eliminarUbicacion(ubicacion);
        if ( filtroTipoUbicacion.value === ubicacion.tipo && filtroUbicacion.value === String(ubicacion.id)) 
            {
            filtroTipoUbicacion.value = "";
            filtroUbicacion.value = "";
        }
        await cargarUbicaciones();
        await actualizarTablaEquipos();
        mostrarMensajeExito(resultado.mensaje);

    } catch (error) {
        mostrarMensajeError(error.message);
    }
}


// TABLA DE EQUIPOS

function obtenerTextoUbicacion(equipo)
{
    if (!equipo.tipoUbicacion) {
        return "Sin ubicación";
    }

    if (equipo.tipoUbicacion === "prestamo") {
        return "Equipo de Préstamo";
    }

    const tipo = equipo.tipoUbicacion.charAt(0).toUpperCase() + equipo.tipoUbicacion.slice(1);
    return tipo + " " + equipo.idUbicacion + " (PC-" + equipo.posicion + ")";
}


function agregarFilaEquipo(equipo)
{
    const fila = document.createElement("tr");

    const campoId = document.createElement("td");
    campoId.textContent = equipo.idEquipo;

    const campoUbicacion = document.createElement("td");
    campoUbicacion.textContent = obtenerTextoUbicacion(equipo);

    fila.appendChild(campoId);
    fila.appendChild(campoUbicacion);

    if (rolActualEquipos === "administrador") {
        const campoEstado = document.createElement("td");
        campoEstado.textContent = Number(equipo.activo) === 1 ? "Activo" : "Inactivo";
        const campoIncidencias = document.createElement("td");
        campoIncidencias.textContent = equipo.totalIncidencias ?? 0;

        fila.appendChild(campoEstado);
        fila.appendChild(campoIncidencias);
    }

    const campoIntervencion = document.createElement("td");
    campoIntervencion.textContent = equipo.ultimaIntervencion ?? "Sin intervenciones previas";
    const campoAcciones = document.createElement("td");

    if (rolActualEquipos === "administrador") {
        const botonUbicacion = document.createElement("button");

        botonUbicacion.type = "button";
        botonUbicacion.textContent = "Cambiar ubicación";
        botonUbicacion.classList.add("btn", "btn-warning", "text-bold", "me-2");
        botonUbicacion.addEventListener("click", function () {
            abrirFormularioModificarEquipo(equipo);
        });


        const botonEstado = document.createElement("button");
        botonEstado.type = "button";
        botonEstado.classList.add("btn", "text-bold");

        if (Number(equipo.activo) === 1) {
            botonEstado.textContent = "Desactivar";
            botonEstado.classList.add("btn-danger");

            botonEstado.addEventListener("click", function () {
                gestionarEstadoEquipo(equipo.idEquipo, "desactivar");
            });

        } else {
            botonEstado.textContent = "Activar";
            botonEstado.classList.add("btn-success");

            botonEstado.addEventListener("click", function () {
                gestionarEstadoEquipo(equipo.idEquipo, "activar");
            });
        }

        campoAcciones.appendChild(botonUbicacion);
        campoAcciones.appendChild(botonEstado);

    } else {
        campoAcciones.textContent = "—";
    }

    fila.appendChild(campoIntervencion);
    fila.appendChild(campoAcciones);

    cuerpoTablaEquipos.appendChild(fila);
}


function mostrarSinEquipos()
{
    const fila = document.createElement("tr");
    const campoMensaje = document.createElement("td");

    campoMensaje.colSpan = rolActualEquipos === "administrador" ? 6 : 4;
    campoMensaje.textContent = "No se encontraron equipos.";
    campoMensaje.classList.add("text-center", "py-4", "text-muted", "text-bold");

    fila.appendChild(campoMensaje);
    cuerpoTablaEquipos.appendChild(fila);
}


async function actualizarTablaEquipos()
{
    cuerpoTablaEquipos.replaceChildren();
    try {
        const equipos = await obtenerEquipos();

        if (equipos.length === 0) {
            mostrarSinEquipos();
            return;
        }

        for (const equipo of equipos) {
            agregarFilaEquipo(equipo);
        }

    } catch (error) {
        mostrarMensajeError(
            "No se pudieron cargar los equipos: " + error.message);
    }
}


// FILTROS

async function filtrarEquipos(evento)
{
    evento.preventDefault();
    await actualizarTablaEquipos();
}


async function mostrarEquiposPrestamo(evento)
{
    evento.preventDefault();

    filtroUbicacion.value = "";
    filtroTipoUbicacion.value = "prestamo";
    cerrarMenuUbicaciones();
    await actualizarTablaEquipos();
}


async function mostrarTodosEquipos(evento)
{
    evento.preventDefault();

    filtroUbicacion.value = "";
    filtroTipoUbicacion.value = "";
    cerrarMenuUbicaciones();
    await actualizarTablaEquipos();
}


// FORMULARIO DE EQUIPO

function mostrarModalEquipo()
{
    modalEquipo.classList.remove("d-none");
    modalEquipo.classList.add("d-flex");
}


function abrirFormularioEquipo()
{
    modoFormularioEquipo = "registro";

    formularioEquipo.reset();

    tituloFormEquipo.textContent = "Registro de PC";
    botonGuardarEquipo.textContent = "Guardar PC";
    entradaIdEquipo.readOnly = false;
    entradaPosicion.required = false;
    entradaPosicion.value = "";

    contenedorLugar.classList.add("d-none");

    mostrarModalEquipo();
}


function abrirFormularioModificarEquipo(equipo)
{
    modoFormularioEquipo = "modificacion";

    formularioEquipo.reset();

    tituloFormEquipo.textContent = "Cambiar ubicación";
    botonGuardarEquipo.textContent = "Guardar cambios";

    entradaIdEquipo.value = equipo.idEquipo;
    entradaIdEquipo.readOnly = true;

    if (!equipo.tipoUbicacion) {
        entradaUbicacionEquipo.value = "ninguna";

    } else if (equipo.tipoUbicacion === "prestamo") {
        entradaUbicacionEquipo.value = "prestamo";

    } else {
        entradaUbicacionEquipo.value =
            equipo.tipoUbicacion + " " + equipo.idUbicacion;
    }

    actualizarCampoPosicion();

    if (equipo.tipoUbicacion === "laboratorio" || equipo.tipoUbicacion === "taller") {
        entradaPosicion.value = equipo.posicion ?? "";
    }

    mostrarModalEquipo();
}


function cerrarFormularioEquipo()
{
    formularioEquipo.reset();

    modoFormularioEquipo = "registro";
    entradaIdEquipo.readOnly = false;
    entradaPosicion.required = false;

    contenedorLugar.classList.add("d-none");

    modalEquipo.classList.add("d-none");
    modalEquipo.classList.remove("d-flex");
}


function actualizarCampoPosicion()
{
    const tipoUbicacion = entradaUbicacionEquipo.value.trim().split(" ")[0];

    if (tipoUbicacion === "laboratorio" || tipoUbicacion === "taller") {
        contenedorLugar.classList.remove("d-none");
        entradaPosicion.required = true;

    } else {
        contenedorLugar.classList.add("d-none");

        entradaPosicion.required = false;
        entradaPosicion.value = "";
    }
}


function obtenerDatosUbicacion()
{
    const valor = entradaUbicacionEquipo.value.trim();

    if (valor === "prestamo" || valor === "ninguna") {
        return {
            tipoUbicacion: valor,
            idUbicacion: ""
        };
    }

    const partes = valor.split(" ");

    return {
        tipoUbicacion: partes[0] ?? "",
        idUbicacion: partes[1] ?? ""
    };
}


// ACCIONES DE EQUIPOS

async function gestionarFormularioEquipo(evento)
{
    evento.preventDefault();

    ocultarMensajes();

    const ubicacion = obtenerDatosUbicacion();

    const equipo = {  
        idEquipo: entradaIdEquipo.value.trim(),
        tipoUbicacion: ubicacion.tipoUbicacion,
        idUbicacion: ubicacion.idUbicacion,
        posicion: entradaPosicion.value.trim()
    };

    try {
        let resultado;

        if (modoFormularioEquipo === "registro") {
            resultado = await registrarEquipo(equipo);

        } else {
            resultado = await modificarUbicacionEquipo(equipo);
        }

        cerrarFormularioEquipo();

        await actualizarTablaEquipos();

        mostrarMensajeExito(resultado.mensaje);

    } catch (error) {
        mostrarMensajeError(error.message);
    }
}


async function gestionarEstadoEquipo(idEquipo, accion)
{
    ocultarMensajes();

    try {
        const resultado = await cambiarEstadoEquipo(idEquipo, accion);

        await actualizarTablaEquipos();

        mostrarMensajeExito(resultado.mensaje);

    } catch (error) {
        mostrarMensajeError(error.message);
    }
}


// INICIALIZACIÓN

async function inicializarGestionEquipos()
{
    try {
        const sesion = await obtenerSesion();

        rolActualEquipos = sesion.rolActual;

        await cargarUbicaciones();
        await actualizarTablaEquipos();

    } catch (error) {
        mostrarMensajeError( "No se pudo cargar el inventario: " + error.message);
    }
}


// EVENTOS

botonAgregarLaboratorio.addEventListener("click", function () {
    gestionarRegistroUbicacion("laboratorio");
});

botonAgregarTaller.addEventListener("click", function () {
    gestionarRegistroUbicacion("taller");
});

formularioFiltroEquipos.addEventListener("submit",filtrarEquipos);

filtroPrestamo.addEventListener("click",mostrarEquiposPrestamo);
filtroTodosEquipos.addEventListener("click",mostrarTodosEquipos);

botonAbrirUbicaciones.addEventListener("click",abrirMenuUbicaciones);
botonCerrarUbicaciones.addEventListener("click",cerrarMenuUbicaciones);

botonRegistrarEquipo.addEventListener("click",abrirFormularioEquipo);
botonCancelarEquipo.addEventListener("click",cerrarFormularioEquipo);

entradaUbicacionEquipo.addEventListener("change",actualizarCampoPosicion);
formularioEquipo.addEventListener("submit",gestionarFormularioEquipo);


inicializarGestionEquipos();