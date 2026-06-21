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
    
    // se corrige la ruta de salida segun donde este parado el usuario
    const urlActual = window.location.pathname //obtiene la ruta donde se ejecuta el js
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
        cierreSesion("Error: No hay usuario logueado o la sesión expiró.")
        return ""
    }

    const usuarioLocalJSON = JSON.parse(usuarioLocal)
    let usuarioReal

// prioridad del usuario debug
    if (String(usuarioLocalJSON.usuario) === "12345678" && String(usuarioLocalJSON.clave) === "adminITI") {
        // simulacion del usuario real con los datos fijos de debug para que pueda acceder a su info en el futuro
        usuarioReal = {
            usuario: "12345678",
            clave: "adminITI",
            rol: "administrador",
            activo: true
        }
    } else {
// busca en el almacenamiento local 
        usuarioReal = buscarUsuarioStorage(usuarioLocalJSON.usuario)

        if (!usuarioReal || !usuarioReal.activo) {
            localStorage.setItem("usuario", "")
            cierreSesion("Error: Usuario inexistente o inactivo.")
            return ""
        }

        if (String(usuarioReal.clave) !== String(usuarioLocalJSON.clave)) {
            localStorage.setItem("usuario", "")
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
        
        const urlActual = window.location.pathname
        let prefijoRuta = "" //si está en la raiz de paginaweb se queda vacío

        if (urlActual.includes("administracion-tecnico") || urlActual.includes("direccion")) {
            prefijoRuta = "../" //sube un nivel si esta adentro de una carpeta
        }

        if (usuarioReal.rol === "administrador" || usuarioReal.rol === "tecnico") {
            window.location.href = `${prefijoRuta}homeAdmin.html`
        } else if (usuarioReal.rol === "docente") {
            window.location.href = `${prefijoRuta}homeDocente.html`
        } else if (usuarioReal.rol === "direccion") {
            window.location.href = `${prefijoRuta}homeDirector.html`
        } else {
            cierreSesion("Error: Rol no reconocido por el sistema.")
        }
    }
}

verificarAcceso()