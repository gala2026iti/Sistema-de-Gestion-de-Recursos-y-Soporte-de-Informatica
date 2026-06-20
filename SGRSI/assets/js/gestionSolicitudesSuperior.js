const tabla = document.getElementById("tablaEquipos") || document.querySelector("table")
const cuerpoTabla = document.getElementById("cuerpoSolicitudes")
const filtroEstado = document.getElementById("filtroEstado")

const finalizarSolicitud = (id) => {
    const solicitudes = cargarSolicitudes()
    for (const solicitud of solicitudes) {
        if (solicitud.id === id) {
            solicitud.estado = "finalizada"
        }
    }
    actualizarSolicitudes(solicitudes)
}

const actualizarTabla = () => {
    const solicitudes = cargarSolicitudes()
    cuerpoTabla.innerHTML = ""

    let solicitudesFiltradas = solicitudes

    if (filtroEstado.value !== "todas") {
        solicitudesFiltradas = solicitudesFiltradas.filter(s => s.estado === filtroEstado.value)
    }

    solicitudesFiltradas.forEach(s => {
        const fila = document.createElement("tr")

        const asuntoFila = document.createElement("td")
        asuntoFila.className = "text-primary fw-bold"
        asuntoFila.textContent = s.asunto

        const fechaFila = document.createElement("td")
        fechaFila.textContent = s.fechaRequerida

        const descripcionFila = document.createElement("td")
        descripcionFila.textContent = s.descripcion

        const datosFila = document.createElement("td")
        datosFila.innerHTML = `Creado por Docente (C.I.): ${s.docente}`

        const accionesFila = document.createElement("td")

        if (s.estado === "pendiente") {
            const btnFinalizar = document.createElement("button")
            btnFinalizar.textContent = "Finalizar petición"
            btnFinalizar.className = "btn btn-primary"
            btnFinalizar.addEventListener("click", () => {
                if (confirm("¿Estás seguro de que querés finalizar esta solicitud? Esta acción va a registrar el trabajo como completado y no se puede deshacer.")) {
                    finalizarSolicitud(s.id)
                }
            })
            accionesFila.appendChild(btnFinalizar)
        } else {
            const spanFinalizado = document.createElement("span")
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

const actualizarSolicitudes = (solicitudes) => {
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes))
    actualizarTabla()
}

const cargarSolicitudes = () => {
    const solicitudesLocales = localStorage.getItem("solicitudes")
    if (solicitudesLocales === null || solicitudesLocales === "" || solicitudesLocales === undefined) {
        return []
    } else {
        return JSON.parse(solicitudesLocales)
    }
}

filtroEstado.addEventListener("change", actualizarTabla)

actualizarTabla()