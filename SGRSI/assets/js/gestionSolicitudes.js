// VARIABLES
const tabla = document.getElementById("tablaEquipos") || document.querySelector("table")
const cuerpoTabla = document.getElementById("cuerpoSolicitudes")
const filtroEstado = document.getElementById("filtroEstado")

// FUNCIONES
const obtenerSolicitudesServidor = () => {
    const solicitudesLocales = localStorage.getItem("solicitudes")
    if (!solicitudesLocales) return []
    return JSON.parse(solicitudesLocales)
}

const guardarSolicitudesServidor = (solicitudes) => {
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes))
    actualizarTabla()
}

const finalizarSolicitud = (id) => {
    const solicitudes = obtenerSolicitudesServidor()
    const solicitud = solicitudes.find(s => Number(s.id) === Number(id))
    
    if (solicitud) {
        solicitud.finalizada = true 
        guardarSolicitudesServidor(solicitudes)
    }
}

const actualizarTabla = () => {
    if (!cuerpoTabla) return
    cuerpoTabla.innerHTML = ""
    
    const solicitudes = obtenerSolicitudesServidor()
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
        asuntoFila.textContent = s.asunto

        const fechaFila = document.createElement("td")
        fechaFila.textContent = s.fecha 

        const descripcionFila = document.createElement("td")
        descripcionFila.textContent = s.descripcion

        const datosFila = document.createElement("td")
        datosFila.textContent = `Creado por: ${s.creador}` 

        const accionesFila = document.createElement("td")

        if (s.finalizada === false) {
            const btnFinalizar = document.createElement("button")
            btnFinalizar.textContent = "Finalizar petición"
            btnFinalizar.className = "btn btn-primary btn-sm"
            btnFinalizar.addEventListener("click", () => {
                if (confirm("¿Estás seguro de que querés finalizar esta solicitud? Esta acción va a registrar el trabajo como completado y no se puede deshacer.")) {
                    finalizarSolicitud(s.id)
                }
            })
            accionesFila.appendChild(btnFinalizar)
        } else {
            const spanFinalizado = document.createElement("span")
            spanFinalizado.className = "badge bg-success text-white px-2 py-1"
            spanFinalizado.textContent = "Resuelta"
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

actualizarTabla()

// EVENTOS
if (filtroEstado) {
    filtroEstado.addEventListener("change", actualizarTabla)
}