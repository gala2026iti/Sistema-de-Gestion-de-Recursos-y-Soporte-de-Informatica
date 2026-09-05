const formUsuario = document.getElementById("formUsuario");

modoEdicion = null

const registrarUsuario = document.getElementById("btnRegistrarUsuario");
const cancelarUsuario = document.getElementById("btnCancelarUsuario");

const btnMostrarClave = document.getElementById("btnMostrarClave");

const cedula = document.getElementById("usuario")
const nombre = document.getElementById("nombre")
const correo = document.getElementById("correo")
const clave = document.getElementById("clave")
const confirmarClave = document.getElementById("confirmarClave")

const inputClave = document.getElementById("clave");
const inputConfirmarClave = document.getElementById("confirmarClave");

const checkboxDocente = document.getElementById("rolDocente");
const checkboxAdministrador = document.getElementById("rolAdministrador");
const checkboxAsistente = document.getElementById("rolTecnico");

const contra = document.getElementById("labelContra")
const confirmarContra = document.getElementById("labelConfirmarContra")

const btnGuardarUsuario = document.getElementById("btnGuardarUsuario")

const modalUsuario = document.getElementById("modalUsuario")
const tituloFormulario = document.getElementById("tituloFormulario")

const botonesModificar = document.querySelectorAll(".btnModificarUsuario")

function validarClavesEnEdicion() {
    if (!modoEdicion) {
        return;
    }

    const claveTieneValor = clave.value.trim() !== "";
    const confirmarClaveTieneValor = confirmarClave.value.trim() !== "";

    if (claveTieneValor !== confirmarClaveTieneValor) {
        clave.required = true;
        confirmarClave.required = true;
    } else {
        clave.required = false;
        confirmarClave.required = false;
    }
}

validarClavesEnEdicion()

registrarUsuario.addEventListener("click", () => { 
    modalUsuario.classList.remove("d-none");
    modalUsuario.classList.add("d-flex");

    cedula.readOnly = false

    tituloFormulario.innerText = "Registrar usuario";
    btnGuardarUsuario.innerText = "Guardar usuario";

    contra.innerText = "Cambiar contraseña"
    confirmarContra.innerText = "Confirmar nueva contraseña"

    formUsuario.action = "../../../../app/controlador/usuarios/procesarAltaUsuario.php"


});

cancelarUsuario.addEventListener("click", () => {
    formUsuario.reset();
    inputClave.type = "password";
    inputConfirmarClave.type = "password";
    btnMostrarClave.innerText = "Mostrar contraseña";

    modalUsuario.classList.add("d-none");
    modalUsuario.classList.remove("d-flex");
});

btnMostrarClave.addEventListener("click", () => {
    const mostrar = inputClave.type === "password";
    inputClave.type = mostrar ? "text" : "password";
    inputConfirmarClave.type = mostrar ? "text" : "password";
    btnMostrarClave.innerText = mostrar ? "Ocultar contraseña" : "Mostrar contraseña";
});

botonesModificar.forEach((boton) => {
    boton.addEventListener("click", () => {
        modoEdicion = true

        cedula.value = boton.dataset.cedula;
        cedula.readOnly = true
        nombre.value = boton.dataset.nombre;
        correo.value = boton.dataset.correo;

        checkboxAdministrador.checked = boton.dataset.administrador === "1";
        checkboxAsistente.checked = boton.dataset.tecnico === "1";
        checkboxDocente.checked = boton.dataset.docente === "1";

        tituloFormulario.innerText = "Modificar usuario";
        btnGuardarUsuario.innerText = "Guardar Cambios";

        formUsuario.action = "../../../app/controlador/usuarios/procesarModificarUsuario.php"

        contra.innerText = "Cambiar contraseña"
        confirmarContra.innerText = "Confirmar nueva contraseña"

        modalUsuario.classList.add("d-flex");
        modalUsuario.classList.remove("d-none");
    });
});

clave.addEventListener("input", validarClavesEnEdicion);
confirmarClave.addEventListener("input", validarClavesEnEdicion);