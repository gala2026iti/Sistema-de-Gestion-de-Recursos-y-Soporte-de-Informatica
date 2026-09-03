const formulario = document.getElementById("formUsuario");
const dialogRegistrarUsuario = document.getElementById("dialogRegistrarUsuario");
const registrarUsuario = document.getElementById("btnRegistrarUsuario");
const cancelarUsuario = document.getElementById("btnCancelarUsuario");
const btnMostrarClave = document.getElementById("btnMostrarClave");
const inputClave = document.getElementById("clave");
const inputConfirmarClave = document.getElementById("confirmarClave");

const dialogModificarUsuario = document.getElementById("dialogModificarUsuario");
const formularioModificarUsuario = document.getElementById("formModificarUsuario");
const btnCancelarModificarUsuario = document.getElementById("btnCancelarModificarUsuario");
const modificarCedula = document.getElementById("modificarCedula");
const modificarNombre = document.getElementById("modificarNombre");
const modificarCorreo = document.getElementById("modificarCorreo");
const modificarRolDocente = document.getElementById("modificarRolDocente");
const modificarRolTecnico = document.getElementById("modificarRolTecnico");
const modificarRolAdministrador = document.getElementById("modificarRolAdministrador");
const modificarClave = document.getElementById("modificarClave");
const modificarConfirmarClave = document.getElementById("modificarConfirmarClave");
const btnMostrarClaveModificar = document.getElementById("btnMostrarClaveModificar");

const botonesModificar = document.querySelectorAll(".btnModificarUsuario");

/*
 * Abre y cierra el diálogo de registro.
 */
registrarUsuario.addEventListener("click", () => {
    dialogRegistrarUsuario.showModal();
});

cancelarUsuario.addEventListener("click", () => {
    formulario.reset();
    inputClave.type = "password";
    inputConfirmarClave.type = "password";
    btnMostrarClave.innerText = "Mostrar contraseña";
    dialogRegistrarUsuario.close();
});

/*
 * Muestra u oculta las contraseñas del registro.
 */
btnMostrarClave.addEventListener("click", () => {
    const mostrar = inputClave.type === "password";

    inputClave.type = mostrar ? "text" : "password";
    inputConfirmarClave.type = mostrar ? "text" : "password";
    btnMostrarClave.innerText = mostrar ? "Ocultar contraseña" : "Mostrar contraseña";
});

/*
 * Carga los datos del usuario y abre
 * el diálogo de modificación.
 */
botonesModificar.forEach((boton) => {
    boton.addEventListener("click", () => {
        modificarCedula.value = boton.dataset.cedula;
        modificarNombre.value = boton.dataset.nombre;
        modificarCorreo.value = boton.dataset.correo;

        modificarRolAdministrador.checked = boton.dataset.administrador === "1";
        modificarRolTecnico.checked = boton.dataset.tecnico === "1";
        modificarRolDocente.checked = boton.dataset.docente === "1";

        dialogModificarUsuario.showModal();
    });
});

/*
 * Cierra el diálogo de modificación.
 */
btnCancelarModificarUsuario.addEventListener("click", () => {
    formularioModificarUsuario.reset();
    modificarClave.type = "password";
    modificarConfirmarClave.type = "password";
    btnMostrarClaveModificar.innerText = "Mostrar contraseña";
    dialogModificarUsuario.close();
});

/*
 * Muestra u oculta las contraseñas de modificación.
 */
btnMostrarClaveModificar.addEventListener("click", () => {
    const mostrar = modificarClave.type === "password";

    modificarClave.type = mostrar ? "text" : "password";
    modificarConfirmarClave.type = mostrar ? "text" : "password";
    btnMostrarClaveModificar.innerText = mostrar ? "Ocultar contraseña" : "Mostrar contraseña";
});