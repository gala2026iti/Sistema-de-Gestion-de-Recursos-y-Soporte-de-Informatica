// FUNCIONES
const obtenerUsuariosLocal = () => {
    const datos = localStorage.getItem('usuarios')
    if (datos === null || datos === undefined || datos === "") {
        return []
    } else {
        return JSON.parse(datos)
    }
}

const buscarUsuarioStorage = (cedula) => {
    return obtenerUsuariosLocal().find(u => u.usuario === cedula)
}

const cierreSesion = (mensaje) => {
    alert(mensaje)

    const urlActual = window.location.pathname
    let rutaSalida = "index.html"

    if (urlActual.includes("administracion-tecnico") || urlActual.includes("direccion")) {
        rutaSalida = "../index.html"
    }

    localStorage.setItem("usuario", "")
    window.location.href = rutaSalida
}

const hayAdmins = () => {
    const usuariosLocales = localStorage.getItem("usuarios")
    if (!usuariosLocales) return false

    const usuarios = JSON.parse(usuariosLocales)
    if (!Array.isArray(usuarios)) return false

    return usuarios.some(u => u.rol === "administrador" && u.activo)
}

const verificarAcceso = () => {
    const usuarioLocal = localStorage.getItem("usuario")

    if (usuarioLocal === null || usuarioLocal === undefined || usuarioLocal === "") {
        cierreSesion("Error: No hay usuario logueado")
        return ""
    }

    const usuarioLocalJSON = JSON.parse(usuarioLocal)
    let usuarioReal
 
    if (usuarioLocalJSON.usuario === "12345678" && usuarioLocalJSON.clave === "adminITI" && !hayAdmins()) {
        usuarioReal = {
            usuario: "12345678",
            clave: "adminITI",
            rol: "administrador",
            activo: true
        }
    } else {
        usuarioReal = buscarUsuarioStorage(usuarioLocalJSON.usuario)

        if (!usuarioReal || !usuarioReal.activo) {
            localStorage.setItem("usuario", "")
            cierreSesion("Error: Usuario inexistente o inactivo")
            return ""
        }

        if (usuarioReal.clave !== usuarioLocalJSON.clave) {
            localStorage.setItem("usuario", "")
            cierreSesion("Error: Credenciales invalidas, se cerrara la sesion")
            return ""
        }
    }

    const rolPagina = document.body.getAttribute("data-rol-permitido")

    const rolesPermitidos = rolPagina.split(" ")

    if (!rolesPermitidos.includes(usuarioReal.rol)) {
        alert("Error: Zona Restringida. No podes ingresar acá, vas a ser llevado al espacio designado por tu rol.")

        const urlActual = window.location.pathname
        let volverAtras = ""

        if (urlActual.includes("administracion-tecnico") || urlActual.includes("direccion")) {
            volverAtras = "../"
        }

        if (usuarioReal.rol === "administrador" || usuarioReal.rol === "tecnico") {
            window.location.href = `${volverAtras}homeAdmin.html`
        } else if (usuarioReal.rol === "docente") {
            window.location.href = `${volverAtras}homeDocente.html`
        } else if (usuarioReal.rol === "direccion") {
            window.location.href = `${volverAtras}homeDirector.html`
        } else {
            cierreSesion("Error: Rol no reconocido por el sistema")
        }
    }
}

verificarAcceso()
