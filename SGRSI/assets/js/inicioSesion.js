const form = document.getElementById('formInicio')

// FUNCIONES
const cargarUsuarios = () => {
    const datos = localStorage.getItem('usuarios')
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const obtenerUsuario = (cedula) => {
    return cargarUsuarios().find(u => u.usuario === cedula)
}

const usuarioExistente = (cedula) => {
    return obtenerUsuario(cedula) !== undefined
}

// EVENTOS
form.addEventListener("submit", function(e){
    e.preventDefault()

    const inputCedula = document.getElementById('cedula').value.trim()
    const inputClave = document.getElementById('clave').value.trim()

    const usuarioLocal = {
        cedula: inputCedula,
        clave: inputClave,
        rol: ""
    }

    // debug administrdor
    if (inputCedula === "12345678" && inputClave === "adminITI") {
        usuarioLocal.rol = "administrador"
        localStorage.setItem("usuario", JSON.stringify(usuarioLocal))
        window.location.href = "homeAdmin.html"
    } else {
        // inicio de sesion por usuario
        if (usuarioExistente(inputCedula)) {
            const usuarioLogueado = obtenerUsuario(inputCedula)

            if (usuarioLogueado.clave === inputClave) {
                if (usuarioLogueado.activo) {
                    
                    usuarioLocal.rol = usuarioLogueado.rol
                    localStorage.setItem("usuario", JSON.stringify(usuarioLocal))

                    // redirecciones por rol
                    if (usuarioLogueado.rol === "administrador") {
                        window.location.href = "homeAdmin.html"
                    } else if (usuarioLogueado.rol === "docente") {
                        window.location.href = "homeDocente.html"
                    } else if (usuarioLogueado.rol === "tecnico") {
                        window.location.href = "homeAdmin.html"
                    } else if (usuarioLogueado.rol === "direccion") {
                        window.location.href = "homeDirector.html"
                    }
                } else {
                    alert("Usuario no disponible")
                }
            } else {
                alert("Usuario o Contraseña incorrectos")
            }
        } else {
            alert("Usuario o Contraseña incorrectos")
        }
    }
})