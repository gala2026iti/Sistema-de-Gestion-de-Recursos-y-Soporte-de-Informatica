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

    // Debug de administrador
    if (inputCedula === "12345678" && inputClave === "adminITI") {
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