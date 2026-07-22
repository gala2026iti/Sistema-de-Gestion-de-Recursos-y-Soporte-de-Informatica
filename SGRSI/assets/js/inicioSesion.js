// VARIABLES
let booleanMostrarContra = false
const form = document.getElementById('formInicio')
const btnMostrarContra = document.getElementById("btnMostrarContra")
const inputClave = document.getElementById("clave")

// FUNCIONES
const cargarUsuarios = () => {
    const datos = localStorage.getItem('usuarios')
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const mostrarContra = () => {
    booleanMostrarContra = !booleanMostrarContra
    if (booleanMostrarContra) {
        inputClave.type = "text"
        btnMostrarContra.innerText = "Ocultar Contraseña"
    } else {
        inputClave.type = "password"
        btnMostrarContra.innerText = "Mostrar Contraseña"
    }
}

const obtenerUsuario = (cedula) => {
    return cargarUsuarios().find(u => u.usuario === cedula)
}

const usuarioExistente = (cedula) => {
    return obtenerUsuario(cedula) !== undefined
}

const hayAdmins = () => {
    const usuariosLocales = localStorage.getItem("usuarios")
    const usuarios = JSON.parse(usuariosLocales) || []
    

    const hayAdmins = usuarios.some(u => u.rol === "administrador" && u.activo)
    return hayAdmins

}

const validarInicioSesion = (usuario) => {

    if (!/^\d{8}$/.test(usuario.usuario)) {
        alert("La cédula debe contener exactamente 8 números.")
        return false }

    if (usuario.clave.trim() === "") {
        alert("Debe ingresar una contraseña.")
        return false }
    return true
}

// EVENTOS
form.addEventListener("submit", function (e) {
    e.preventDefault()

    const inputCedula = document.getElementById('cedula').value.trim()
    const inputClave = document.getElementById('clave').value.trim()

    const usuarioLocal = {
        usuario: inputCedula,
        clave: inputClave,
        rol: ""
    }

    if (!validarInicioSesion(usuarioLocal)) {
    return
}

    // Debug de administrador
    if (inputCedula === "12345678" && inputClave === "adminITI" && !hayAdmins()) {
        usuarioLocal.rol = "administrador"
        localStorage.setItem("usuario", JSON.stringify(usuarioLocal))
        window.location.href = "homeAdmin.html"
    } else {

        //Logueo normal
        if (usuarioExistente(inputCedula)) {
            const usuarioLogueado = obtenerUsuario(inputCedula)

            if (usuarioLogueado.clave === inputClave) {

                if (usuarioLogueado.activo) {

                    usuarioLocal.rol = usuarioLogueado.rol
                    localStorage.setItem("usuario", JSON.stringify(usuarioLocal))

                    if (usuarioLogueado.rol === "administrador" || usuarioLogueado.rol === "tecnico") {
                        window.location.href = "homeAdmin.html"
                    } else if (usuarioLogueado.rol === "docente") {
                        window.location.href = "homeDocente.html"
                    } else if (usuarioLogueado.rol === "direccion") {
                        window.location.href = "homeDirector.html"
                    }
                } else {
                    alert("Error: Usuario no disponible")
                }
            } else {
                alert("Error: Usuario o Contraseña incorrectos")
            }
        } else {
            alert("Error: Usuario o Contraseña incorrectos")
        }
    }
})

btnMostrarContra.addEventListener("click", mostrarContra)