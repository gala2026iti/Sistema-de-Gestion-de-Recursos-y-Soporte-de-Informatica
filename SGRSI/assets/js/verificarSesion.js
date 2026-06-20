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
    localStorage.setItem("usuario", "")
    window.location.href = "index.html"
}

const verificarAcceso = () => {
    const usuarioLocal = localStorage.getItem("usuario")

    if (!usuarioLocal || usuarioLocal === "") {
        cierreSesion("Error: No hay usuario logueado o la sesión expiró.")
        return ""
    }

    const usuarioLocalJSON = JSON.parse(usuarioLocal)
    let usuarioReal

// primero se verifica si el usuario es debug o no
    if (String(usuarioLocalJSON.cedula) === "12345678" && String(usuarioLocalJSON.clave) === "adminITI") {
// mas cosas de objetos json correspondientes al usuario debug
        usuarioReal = {
            usuario: "12345678",
            clave: "adminITI",
            rol: "administrador",
            activo: true
        }
    } else {
// si el usuario no es el debug, busca al usuario dentro del alamcenamiento
        usuarioReal = buscarUsuarioStorage(usuarioLocalJSON.cedula)

        if (!usuarioReal || !usuarioReal.activo) {
            cierreSesion("Error: Usuario inexistente o inactivo.")
            return ""
        }

        if (usuarioReal.clave !== usuarioLocalJSON.clave) {
            cierreSesion("Error: Credenciales invalidas, se cerrará la sesión.")
            return ""
        }
    }

    const atributoPagina = document.body.getAttribute("data-rol-permitido")

    const rolesPermitidos = atributoPagina.split(" ")
    // para saber que usuarios pueden entrar a x pagina, se agrega un valor nuevo en el body de cada pagina
    // no es muy seguro, pero algo es mejor que nada
    // y como se pueden guardar multiples valores, se obtiene como array

    if (!rolesPermitidos.includes(usuarioReal.rol)) {
        alert("No podes ingresar acá, vas a ser llevado a tu espacio.")
        
        if (usuarioReal.rol === "administrador") {
            window.location.href = "homeAdmin.html"
        } else if (usuarioReal.rol === "docente") {
            window.location.href = "homeDocente.html"
        } else if (usuarioReal.rol === "tecnico") {
            window.location.href = "homeAdmin.html" 
        } else if (usuarioReal.rol === "direccion") {
            window.location.href = "homeDirector.html"
        } else {
            cierreSesion("Error: Rol no reconocido por el sistema.")
        }
    }
}

verificarAcceso()