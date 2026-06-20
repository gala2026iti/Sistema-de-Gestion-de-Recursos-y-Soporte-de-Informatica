const formSolicitud = document.getElementById("formSolicitud")

const cargarSolicitudes = () => {
    const datos = localStorage.getItem("solicitudes")
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const guardarSolicitudesBase = (lista) => {
    localStorage.setItem("solicitudes", JSON.stringify(lista))
}

formSolicitud.addEventListener("submit", function (e) {
    e.preventDefault()

    const inputAsunto = document.getElementById("asunto").value.trim()
    const inputDescripcion = document.getElementById("Area").value.trim()
    const inputFechaHora = document.getElementById("fecha").value

// se valida que la fecha y hora estane adelanradas a la fecha de hoy
    const fechaSeleccionada = new Date(inputFechaHora)
    const fechaActual = new Date()

    if (fechaSeleccionada <= fechaActual) {
        alert("Error: La fecha y hora de deben ser posteriores al dia de hoy")
        return
    }

    const usuarioLocal = localStorage.getItem("usuario")
    let cedulaDocente = "Desconocido" //por el momento, si tira error se sabra que esta condicion no cambio

    if (usuarioLocal && usuarioLocal !== "") {
        const usuarioLocalJSON = JSON.parse(usuarioLocal)
        cedulaDocente = usuarioLocalJSON.cedula
    }

    const solicitudes = cargarSolicitudes()
    
    let nuevoId = 1
    if (solicitudes.length > 0) {
        nuevoId = solicitudes[solicitudes.length - 1].id + 1
    }

    const opcionesFecha = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
    const fechaFormateadaString = fechaSeleccionada.toLocaleDateString('es-ES', opcionesFecha) 
    // supuestamente son configuraciones importante spara la fecha
    // mas especificamente por el cambio horario
    const solicitud = {
        id: nuevoId,
        docente: cedulaDocente,
        asunto: inputAsunto,
        descripcion: inputDescripcion,
        fechaRequerida: fechaFormateadaString,
        estado: "pendiente"
    }

    solicitudes.push(solicitud)
    guardarSolicitudesBase(solicitudes)

    alert("Solicitud enviada con exito")
    formSolicitud.reset()
})