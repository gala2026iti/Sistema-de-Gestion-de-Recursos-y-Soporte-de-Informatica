// VARIABLES
const formulario = document.getElementById("formSolicitud")

// FUNCIONES
const cargarSolicitudes = () => {
    const solicitudesLocales = localStorage.getItem("solicitudes")
    if (solicitudesLocales === null || solicitudesLocales === undefined || solicitudesLocales === "") {
        return []
    }
    return JSON.parse(solicitudesLocales)
}

const guardarSolicitud = (solicitud) => {
    const solicitudes = cargarSolicitudes()
    solicitudes.push(solicitud)
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes))

    alert("Solicitud registrada con exito")
    formulario.reset()
}

const obtenerFecha = (dato) => {
    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()
    const formatoHora = fechaActual.getHours() + ":" + fechaActual.getMinutes()

    if(dato === "fecha"){
        return formatoFecha
    } else {
        return formatoHora
    }
}

const registrarHistorial = (ciUsuario, modificacion, idSolicitud, asunto) => {
    const datos = localStorage.getItem("registroSolicitudes")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        idSolicitud: idSolicitud,
        modificacion: modificacion,
        usuario: ciUsuario,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora")
    })
    localStorage.setItem("registroSolicitudes", JSON.stringify(historial))
}

const fechaValida = (fechaInput) => {
    const fechaSeleccionada = new Date(fechaInput)
    const hoy = new Date()

    return fechaSeleccionada > hoy
}

// EVENTOS
formulario.addEventListener("submit", function (e) {
    e.preventDefault()

    const entradaAsunto = document.getElementById("asunto")
    const entradaDescripcion = document.getElementById("descripcion")
    const entradaFecha = document.getElementById("fecha")

    const usuario = localStorage.getItem('usuario')
    const usuarioLocalJSON = JSON.parse(usuario)

    const solicitudes = cargarSolicitudes()
    const nuevoId = solicitudes.length + 1


    const solicitud = {
        id: nuevoId,
        asunto: entradaAsunto.value.trim(),
        descripcion: entradaDescripcion.value.trim(),
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        creador: usuarioLocalJSON.usuario,
        finalizada: false
    }

    if (fechaValida(entradaFecha.value)) {
        guardarSolicitud(solicitud)
        registrarHistorial(usuarioLocalJSON.usuario, "creacion", solicitud.id, solicitud.asunto)

    } else {
        alert("Error: La fecha y hora deben ser posteriores al momento actual")
    }
})