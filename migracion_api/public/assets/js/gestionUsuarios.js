const API_USUARIOS = "../index.php/usuarios";

// ELEMENTOS DEL DOM
const cuerpoTablaUsuarios = document.getElementById("cuerpoTablaUsuarios");
const formularioFiltroUsuarios = document.getElementById("formFiltroUsuarios");
const entradaRol = document.getElementById("rol");
const entradaEstado = document.getElementById("estado");

const botonRegistrarUsuario = document.getElementById("btnRegistrarUsuario");
const modalUsuario = document.getElementById("modalUsuario");
const formularioUsuario = document.getElementById("formUsuario");
const tituloFormulario = document.getElementById("tituloFormulario");

const entradaCedula = document.getElementById("usuario");
const entradaNombre = document.getElementById("nombre");
const entradaCorreo = document.getElementById("correo");
const entradaClave = document.getElementById("clave");
const entradaConfirmarClave = document.getElementById("confirmarClave");

const rolDocente = document.getElementById("rolDocente");
const rolTecnico = document.getElementById("rolTecnico");
const rolAdministrador = document.getElementById("rolAdministrador");

const botonCancelarUsuario = document.getElementById("btnCancelarUsuario");
const botonMostrarClave = document.getElementById("btnMostrarClave");

const mensajeExito = document.getElementById("mensajeExito");
const mensajeError = document.getElementById("mensajeError");

// ESTADO DEL FORMULARIO
let modoFormularioUsuario = "registro";



// RESPUESTAS DE LA API
async function leerRespuestaAPI(respuesta)
{
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
            `HTTP ${respuesta.status}: ${
                json.mensaje ??
                "La solicitud no se pudo completar." }`);
    }

    return json.datos;
}

// PETICIONES
async function obtenerUsuarios(rol = "", estado = "")
{
    let url = API_USUARIOS;
    const parametros = new URLSearchParams();

    if (rol !== "") {
        parametros.append("rol", rol);
    }

    if (estado !== "") {
        parametros.append("estado", estado);
    }

    const consulta = parametros.toString();

    if (consulta !== "") {
        url += "&" + consulta;
    }

    const respuesta = await fetch(url);
    return leerRespuestaAPI(respuesta);
}


async function registrarUsuario(usuario)
{
    const respuesta = await fetch(
        API_USUARIOS,{
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },
            body: JSON.stringify(usuario)
        }
    );
    return leerRespuestaAPI(respuesta);
}

async function modificarUsuario(usuario)
{
    const respuesta = await fetch(
    API_USUARIOS + "/" + encodeURIComponent(usuario.cedula),{
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        },
        body: JSON.stringify({
            nombre: usuario.nombre,
            correo: usuario.correo,
            clave: usuario.clave,
            confirmarClave: usuario.confirmarClave,
            roles: usuario.roles
        })
    }
);

    return leerRespuestaAPI(respuesta);
}


async function cambiarEstadoUsuario(cedula, accion)
{
    const respuesta = await fetch(
    API_USUARIOS + "/" + encodeURIComponent(cedula),{
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        },
        body: JSON.stringify({
            accion: accion
        })
    }
);

    return leerRespuestaAPI(respuesta);
}

// ROLES
function obtenerRolesUsuario(usuario)
{
    const roles = [];

    if (Number(usuario.administrador) === 1) {
        roles.push("Administrador");
    }

    if (Number(usuario.tecnico) === 1) {
        roles.push("Técnico");
    }

    if (Number(usuario.docente) === 1) {
        roles.push("Docente");
    }

    return roles.length === 0 ? "Sin rol" : roles.join(", ");
}


function obtenerRolesSeleccionados()
{
    const roles = [];

    if (rolDocente.checked) {
        roles.push("docente");
    }

    if (rolTecnico.checked) {
        roles.push("tecnico");
    }

    if (rolAdministrador.checked) {
        roles.push("administrador");
    }

    return roles;
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

// FORMULARIO DE USUARIO
function abrirFormularioRegistro()
{
    modoFormularioUsuario = "registro";

    formularioUsuario.reset();

    tituloFormulario.textContent = "Registro de usuario";

    entradaCedula.readOnly = false;
    entradaClave.required = true;
    entradaConfirmarClave.required = true;

    entradaClave.placeholder = "Ingrese contraseña";
    entradaConfirmarClave.placeholder = "Repita la contraseña";

    modalUsuario.classList.remove("d-none");
    modalUsuario.classList.add("d-flex");
}


function abrirFormularioModificacion(usuario)
{
    modoFormularioUsuario = "modificacion";

    formularioUsuario.reset();

    tituloFormulario.textContent = "Modificación de usuario";

    entradaCedula.value = usuario.cedula;
    entradaNombre.value = usuario.nombre;
    entradaCorreo.value = usuario.correo;

    rolAdministrador.checked = Number(usuario.administrador) === 1;
    rolTecnico.checked = Number(usuario.tecnico) === 1;
    rolDocente.checked = Number(usuario.docente) === 1;

    entradaCedula.readOnly = true;

    entradaClave.required = false;
    entradaConfirmarClave.required = false;

    entradaClave.placeholder = "Dejar vacío para conservar la contraseña";
    entradaConfirmarClave.placeholder = "Dejar vacío para conservar la contraseña";

    modalUsuario.classList.remove("d-none");
    modalUsuario.classList.add("d-flex");
}


function cerrarFormularioUsuario()
{
    formularioUsuario.reset();

    modalUsuario.classList.add("d-none");
    modalUsuario.classList.remove("d-flex");
}


function mostrarClaveUsuario()
{
    if (entradaClave.type === "password") {
        entradaClave.type = "text";
        entradaConfirmarClave.type = "text";

        botonMostrarClave.textContent = "Ocultar contraseña";
    } else {
        entradaClave.type = "password";
        entradaConfirmarClave.type = "password";

        botonMostrarClave.textContent = "Mostrar contraseña";
    }
}

// TABLA DE USUARIOS

function agregarFilaUsuario(usuario)
{
    const fila = document.createElement("tr");

    const campoCedula = document.createElement("td");
    campoCedula.textContent = usuario.cedula;

    const campoNombre = document.createElement("td");
    campoNombre.textContent = usuario.nombre;

    const campoCorreo = document.createElement("td");
    campoCorreo.textContent = usuario.correo;

    const campoRol = document.createElement("td");
    campoRol.textContent = obtenerRolesUsuario(usuario);

    const campoEstado = document.createElement("td");
    campoEstado.textContent = Number(usuario.activo) === 1 ? "Activo": "De baja";

    const campoAcciones = document.createElement("td");
    const botonModificar = document.createElement("button");
    botonModificar.type = "button";
    botonModificar.textContent = "Modificar";
    botonModificar.classList.add("btn", "btn-primary", "btnModificarUsuario", "text-bold");
    botonModificar.addEventListener("click",
        function ()
        {
            abrirFormularioModificacion(usuario);
        });

    campoAcciones.appendChild(botonModificar);

    const botonEstado = document.createElement("button");

    botonEstado.type = "button";
    botonEstado.classList.add("btn", "ms-1","text-bold");

    if (Number(usuario.activo) === 1) {
        botonEstado.textContent = "Desactivar";
        botonEstado.classList.add("btn-danger");

        botonEstado.addEventListener("click",
            function (){
                gestionarEstadoUsuario( usuario.cedula,"desactivar");
            });
    } else {
        botonEstado.textContent = "Activar";
        botonEstado.classList.add("btn-success");

        botonEstado.addEventListener("click",
            function (){
                gestionarEstadoUsuario(usuario.cedula, "activar" );
            });
    }

    campoAcciones.appendChild(botonEstado);


    fila.appendChild(campoCedula);
    fila.appendChild(campoNombre);
    fila.appendChild(campoCorreo);
    fila.appendChild(campoRol);
    fila.appendChild(campoEstado);
    fila.appendChild(campoAcciones);

    cuerpoTablaUsuarios.appendChild(fila);
}


function mostrarSinUsuarios()
{
    const fila = document.createElement("tr");
    const campoMensaje = document.createElement("td");

    campoMensaje.colSpan = 6;
    campoMensaje.textContent = "No se encontraron usuarios.";
    campoMensaje.classList.add( "text-center", "py-4", "text-muted", "text-bold" );
    fila.appendChild(campoMensaje);
    cuerpoTablaUsuarios.appendChild(fila);
}


async function actualizarTablaUsuarios()
{
    cuerpoTablaUsuarios.replaceChildren();

    try {
        const rol = entradaRol.value;
        const estado = entradaEstado.value;

        const usuarios = await obtenerUsuarios(rol, estado);

        if (usuarios.length === 0) {
            mostrarSinUsuarios();
            return;
        }

        for (const usuario of usuarios) {
            agregarFilaUsuario(usuario);
        }

    } catch (error) {
        window.alert(
            "No se pudieron cargar los usuarios: " + error.message );
    }
}


// GESTIÓN DE USUARIOS
async function filtrarUsuarios(eventoFormulario)
{
    eventoFormulario.preventDefault();
    await actualizarTablaUsuarios();
}


async function gestionarFormularioUsuario(eventoFormulario)
{
    eventoFormulario.preventDefault();

    ocultarMensajes();

    const usuario = {
        cedula: entradaCedula.value.trim(),
        nombre: entradaNombre.value.trim(),
        correo: entradaCorreo.value.trim(),
        clave: entradaClave.value,
        confirmarClave: entradaConfirmarClave.value,
        roles: obtenerRolesSeleccionados()
    };

    try {
        let resultado;

        if (modoFormularioUsuario === "registro") {
            resultado = await registrarUsuario(usuario);
        } else {
            resultado = await modificarUsuario(usuario);
        }

        cerrarFormularioUsuario();
        mostrarMensajeExito(resultado.mensaje);
        await actualizarTablaUsuarios();

    } catch (error) {
        mostrarMensajeError(error.message);
    }
}


async function gestionarEstadoUsuario(cedula, accion)
{
    ocultarMensajes();

    try {
        const resultado = await cambiarEstadoUsuario(cedula,accion);
        mostrarMensajeExito(resultado.mensaje);
        await actualizarTablaUsuarios();

    } catch (error) {
        mostrarMensajeError(error.message);
    }
}


formularioFiltroUsuarios.addEventListener("submit",filtrarUsuarios);
botonRegistrarUsuario.addEventListener("click",abrirFormularioRegistro);
botonCancelarUsuario.addEventListener("click",cerrarFormularioUsuario);
botonMostrarClave.addEventListener("click",mostrarClaveUsuario);
formularioUsuario.addEventListener("submit",gestionarFormularioUsuario);

actualizarTablaUsuarios();