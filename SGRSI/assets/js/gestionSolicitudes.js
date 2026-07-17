// VARIABLES
const cuerpoTabla = document.querySelector("#tablaSolicitudes tbody")
const filtroEstado = document.getElementById("filtroEstado")

const usuarioLocal = localStorage.getItem("usuario")
const usuarioLocalJSON = JSON.parse(usuarioLocal)

// FUNCIONES
const obtenerSolicitudes = () => {
    const solicitudesLocales = localStorage.getItem("solicitudes")
    if (solicitudesLocales === null || solicitudesLocales === undefined || solicitudesLocales === "") return []
    return JSON.parse(solicitudesLocales)
}

const guardarSolicitudes = (solicitudes) => {
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes))
    actualizarTabla()
}

const finalizarSolicitud = (id) => {
    const solicitudes = obtenerSolicitudes()
    const solicitud = solicitudes.find(s => s.id === id)

    if (solicitud) {
        solicitud.finalizada = true
        guardarSolicitudes(solicitudes)
    }
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

const actualizarTabla = () => {
    if (!cuerpoTabla) return //Si no se encuentra el cuerpoTabla, no se continua, esto para evitar errores de null o undefined
    cuerpoTabla.innerHTML = ""

    const solicitudes = obtenerSolicitudes()
    let solicitudesFiltradas = solicitudes

    if (filtroEstado && filtroEstado.value !== "todas") {
        if (filtroEstado.value === "pendiente") {
            solicitudesFiltradas = solicitudes.filter(s => s.finalizada === false)
        } else if (filtroEstado.value === "finalizada") {
            solicitudesFiltradas = solicitudes.filter(s => s.finalizada === true)
        }
    }

    solicitudesFiltradas.forEach(s => {
        const fila = document.createElement("tr")

        const asuntoFila = document.createElement("td")
        asuntoFila.className = "text-primary fw-bold"
        asuntoFila.innerText = s.asunto

        const fechaFila = document.createElement("td")
        fechaFila.innerText = s.fecha

        const descripcionFila = document.createElement("td")
        descripcionFila.innerText = s.descripcion

        const datosFila = document.createElement("td")
        datosFila.innerText = `Creado por: ${(s.creador || "N/A")}`

        const accionesFila = document.createElement("td")

        if (!s.finalizada) {
            const btnFinalizar = document.createElement("button")
            btnFinalizar.innerText = "Finalizar petición"
            btnFinalizar.className = "btn btn-primary btn-sm"
            btnFinalizar.addEventListener("click", () => {
                if (confirm("¿Estás seguro de que querés finalizar esta solicitud? Esta acción va a registrar el trabajo como completado y no se puede deshacer.")) {
                    finalizarSolicitud(s.id)
                    registrarHistorial(usuarioLocalJSON.usuario, "finalizacion", s.id, s.asunto)
                }
            })
            accionesFila.appendChild(btnFinalizar)
        } else {
            const spanFinalizado = document.createElement("span")
            spanFinalizado.className = "badge bg-success text-white px-2 py-1"
            spanFinalizado.innerText = "Resuelta"
            accionesFila.appendChild(spanFinalizado)
        }

        fila.appendChild(asuntoFila)
        fila.appendChild(fechaFila)
        fila.appendChild(descripcionFila)
        fila.appendChild(datosFila)
        fila.appendChild(accionesFila)
        cuerpoTabla.appendChild(fila)
    })
}

// EVENTOS
if (filtroEstado) { //Estas verificaciones se hacen para que, al momento de llamar al metodo, el mismo si exista, esto para no tener errores por null, undefined, etc.
    filtroEstado.addEventListener("change", actualizarTabla)
}
actualizarTabla()
