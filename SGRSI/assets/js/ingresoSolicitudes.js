// VARIABLES
const formulario = document.getElementById("formSolicitud")

// FUNCIONES
const cargarSolicitudes = () => {
    const solicitudesLocales = localStorage.getItem("solicitudes")
    if (!solicitudesLocales) return []
    return JSON.parse(solicitudesLocales)
}

const guardarSolicitud = (solicitud) => {
    const solicitudes = cargarSolicitudes()
    solicitudes.push(solicitud)
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes))

    alert("Solicitud registrada con exito")
    formulario.reset()
}

const fechaValida = (fechaInput) => {
    const fechaSeleccionada = new Date(fechaInput)
    const hoy = new Date()
    
    return fechaSeleccionada > hoy
}

// EVENTOS
formulario.addEventListener("submit", function(e) {
    e.preventDefault()

    const entradaAsunto = document.getElementById("asunto")
    const entradaDescripcion = document.getElementById("Area")
    const entradaFecha = document.getElementById("fecha")
    
    const sesion = localStorage.getItem('usuario')
    const usuario = sesion ? JSON.parse(sesion) : null
    const identificadorCreador = usuario ? (usuario.cedula || usuario.usuario || "Docente") : "Desconocido"

    const solicitudes = cargarSolicitudes()
    const nuevoId = solicitudes.length > 0 ? solicitudes[solicitudes.length - 1].id + 1 : 1

    const opcionesFecha = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
    const fechaFormateada = new Date(entradaFecha.value).toLocaleDateString('es-ES', opcionesFecha)

    const solicitud = {
        id: nuevoId,
        asunto: entradaAsunto.value.trim(),
        descripcion: entradaDescripcion.value.trim(),
        fecha: fechaFormateada,
        creador: identificadorCreador,
        finalizada: false
    }

    if (fechaValida(entradaFecha.value)) {
        guardarSolicitud(solicitud)
    } else {
        alert("Error: La fecha y hora deben ser posteriores al momento actual")
    }
})