const formulario = document.getElementById("formUsuario");
const modalUsuario = document.getElementById("modalUsuario");

const registrarUsuario = document.getElementById("btnRegistrarUsuario");
const cancelarUsuario = document.getElementById("btnCancelarUsuario");

const btnMostrarClave = document.getElementById("btnMostrarClave");
const inputClave = document.getElementById("clave");
const inputConfirmarClave = document.getElementById("confirmarClave");

const modalModificarUsuario =
    document.getElementById("modalModificarUsuario");
const formularioModificarUsuario =
    document.getElementById("formModificarUsuario");
const btnCancelarModificarUsuario =
    document.getElementById("btnCancelarModificarUsuario");
const modificarCedula =
    document.getElementById("modificarCedula");
const modificarNombre =
    document.getElementById("modificarNombre");
const modificarCorreo =
    document.getElementById("modificarCorreo");
const modificarRolDocente =
    document.getElementById("modificarRolDocente");
const modificarRolTecnico =
    document.getElementById("modificarRolTecnico");
const modificarRolAdministrador =
    document.getElementById("modificarRolAdministrador");
const botonesModificar =
    document.querySelectorAll(".btnModificarUsuario");
const modificarClave =
    document.getElementById("modificarClave");
const modificarConfirmarClave =
    document.getElementById("modificarConfirmarClave");


registrarUsuario.addEventListener("click", () => {
    modalUsuario.classList.replace("d-none", "d-flex");
});

cancelarUsuario.addEventListener("click", () => {
    formulario.reset();
    modalUsuario.classList.replace("d-flex", "d-none");
});

let mostrarClave = false;
btnMostrarClave.addEventListener("click", () => {

    mostrarClave = !mostrarClave;

    inputClave.type = mostrarClave ? "text" : "password";
    inputConfirmarClave.type = mostrarClave ? "text" : "password";

    btnMostrarClave.innerText = mostrarClave
        ? "Ocultar contraseña"
        : "Mostrar contraseña";
});

botonesModificar.forEach((boton) => {

    boton.addEventListener("click", () => {

        console.log("Cédula:", boton.dataset.cedula);
        console.log("Administrador:", boton.dataset.administrador);
        console.log("Técnico:", boton.dataset.tecnico);
        console.log("Docente:", boton.dataset.docente);

        modificarCedula.value = boton.dataset.cedula;
        modificarNombre.value = boton.dataset.nombre;
        modificarCorreo.value = boton.dataset.correo;

        modificarRolAdministrador.checked =
            boton.dataset.administrador === "1";

        modificarRolTecnico.checked =
            boton.dataset.tecnico === "1";

        modificarRolDocente.checked =
            boton.dataset.docente === "1";

        modalModificarUsuario.classList.replace(
            "d-none",
            "d-flex"
        );

    });

});

btnCancelarModificarUsuario.addEventListener("click", () => {

    formularioModificarUsuario.reset();

    modalModificarUsuario.classList.replace(
        "d-flex",
        "d-none"
    );

});

const btnMostrarClaveModificar =
    document.getElementById("btnMostrarClaveModificar");

let mostrarClaveModificar = false;

btnMostrarClaveModificar.addEventListener("click", () => {

    mostrarClaveModificar = !mostrarClaveModificar;

    modificarClave.type =
        mostrarClaveModificar ? "text" : "password";

    modificarConfirmarClave.type =
        mostrarClaveModificar ? "text" : "password";

    btnMostrarClaveModificar.innerText =
        mostrarClaveModificar
            ? "Ocultar contraseña"
            : "Mostrar contraseña";
});