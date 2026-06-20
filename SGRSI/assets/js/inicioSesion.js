const form = document.getElementById('formInicio')

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

let usuarios = localStorage.getItem("usuarios")
if(usuarios === null || usuarios === undefined || usuarios === "") {
    usuarios= []
} else {
    usuarios = JSON.parse(usuarios)
}

// FUNCIONES
const usuarioExistente = (cedula) => {
    let existe = false
    usuarios.forEach(u => {
        if(u.usuario === cedula){
            existe = true
        }
    })
    return existe
}

const obtenerUsuario = (cedula) => {
    const usuarioObtenido = usuarios.find(u => u.usuario === cedula)
    return usuarioObtenido
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

    if (inputCedula === "12345678" && inputClave === "adminITI") {
        usuarioLocal.rol = "administrador"
        localStorage.setItem("usuario", JSON.stringify(usuarioLocal))
        window.location.href = "homeAdmin.html"
    } else {
        if (usuarioExistente(inputCedula)) {
            const usuarioLogueado = obtenerUsuario(inputCedula)

            if (usuarioLogueado.clave === inputClave) {
                if (usuarioLogueado.activo) {
                    
                    usuarioLocal.rol = usuarioLogueado.rol
                    localStorage.setItem("usuario", JSON.stringify(usuarioLocal))

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
