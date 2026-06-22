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

const verificarAcceso = () => {
    const usuarioLocal = localStorage.getItem("usuario")

    if (!usuarioLocal || usuarioLocal === "") {
        cierreSesion("Error: No hay usuario logueado")
        return ""
    }

    const usuarioLocalJSON = JSON.parse(usuarioLocal)
    let usuarioReal

    if (String(usuarioLocalJSON.usuario) === "12345678" && String(usuarioLocalJSON.clave) === "adminITI") {
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

        if (String(usuarioReal.clave) !== String(usuarioLocalJSON.clave)) {
            localStorage.setItem("usuario", "")
            cierreSesion("Error: Credenciales invalidas, se cerrara la sesion")
            return ""
        }
    }

    const atributoPagina = document.body.getAttribute("data-rol-permitido")

    const rolesPermitidos = atributoPagina.split(" ")

    if (!rolesPermitidos.includes(usuarioReal.rol)) {
        alert("No podes ingresar acá, vas a ser llevado a tu espacio.")
        
        const urlActual = window.location.pathname
        let prefijoRuta = ""

        if (urlActual.includes("administracion-tecnico") || urlActual.includes("direccion")) {
            prefijoRuta = "../"
        }

        if (usuarioReal.rol === "administrador" || usuarioReal.rol === "tecnico") {
            window.location.href = `${prefijoRuta}homeAdmin.html`
        } else if (usuarioReal.rol === "docente") {
            window.location.href = `${prefijoRuta}homeDocente.html`
        } else if (usuarioReal.rol === "direccion") {
            window.location.href = `${prefijoRuta}homeDirector.html`
        } else {
            cierreSesion("Error: Rol no reconocido por el sistema")
        }
    }
}

verificarAcceso()
