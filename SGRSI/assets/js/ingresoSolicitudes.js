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

const registrarHistorial = (ciUsuario, modificacion, idSolicitud, asunto) => {
    const datos = localStorage.getItem("registroSolicitudes")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        idSolicitud: idSolicitud,
        asunto: asunto,
        modificacion: modificacion,
        usuario: ciUsuario,
        fecha: new Date().toLocaleDateString('es-ES'),
        hora: new Date().toLocaleTimeString('es-ES')
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

    // Se establecen atributos para la adaptacion de la fecha a guardar
    const opcionesFecha = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
    const fechaFormateada = new Date(entradaFecha.value).toLocaleDateString('es-ES', opcionesFecha)

    const solicitud = {
        id: nuevoId,
        asunto: entradaAsunto.value.trim(),
        descripcion: entradaDescripcion.value.trim(),
        fecha: fechaFormateada,
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