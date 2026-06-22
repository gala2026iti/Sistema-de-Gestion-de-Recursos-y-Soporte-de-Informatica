const contenedorHistorial = document.getElementById("contenedor-historial")

const cargarRegistrosTickets = () => {
    const datos = localStorage.getItem("tickets")
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const mapearFechaParaOrdenar = (stringFecha) => {
    if (!stringFecha) return 0
    const partes = stringFecha.split("/")
    if (partes.length !== 3) return 0
    return parseInt(partes[2] + partes[1].padStart(2, "0") + partes[0].padStart(2, "0"))
}

const mostrarHistorial = () => {
    contenedorHistorial.innerHTML = ""
    
    const urlParams = new URLSearchParams(window.location.search)
    const equipoIdUrl = urlParams.get("equipoId")

    let historial = cargarRegistrosTickets()

    if (equipoIdUrl) {
        const idBuscado = String(equipoIdUrl).trim().toLowerCase()
        historial = historial.filter(ticket => {
            const idTicketEquipo = String(ticket.equipoId || ticket.idEquipo || "").trim().toLowerCase()
            return idTicketEquipo === idBuscado
        })
    }

    for (let i = 0; i < historial.length - 1; i++) {
        for (let j = 0; j < historial.length - i - 1; j++) {
            const fechaA = mapearFechaParaOrdenar(historial[j].fecha)
            const fechaB = mapearFechaParaOrdenar(historial[j + 1].fecha)
            if (fechaA < fechaB) {
                const temp = historial[j]
                historial[j] = historial[j + 1]
                historial[j + 1] = temp
            }
        }
    }

    let ultimaFechaRenderizada = ""
    let listaActualUl = null

    historial.forEach(registro => {
        if (ultimaFechaRenderizada !== registro.fechaCreacion) {
            ultimaFechaRenderizada = registro.fechaCreacion

            const spanFecha = document.createElement("span")
            spanFecha.className = "fw-bold d-block mt-3 text-secondary"
            spanFecha.textContent = `Intervenciones el ${registro.fechaCreacion}`
            contenedorHistorial.appendChild(spanFecha)

            listaActualUl = document.createElement("ul")
            listaActualUl.className = "historial-lista mt-2 mb-3 list-unstyled"
            contenedorHistorial.appendChild(listaActualUl)
        }

        const li = document.createElement("li")
        li.className = "historial-contenido d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded shadow-sm"

        const divColumn = document.createElement("div")
        divColumn.className = "d-flex flex-column"

        const spanDescripcion = document.createElement("span")
        spanDescripcion.className = "fw-bold text-dark"
        spanDescripcion.textContent = registro.asunto || registro.tipo


        const spanDetalle = document.createElement("span")
        spanDetalle.className = "text-muted small"
        spanDetalle.textContent = ` ${registro.gravedad} - ${registro.descripcion}`

        divColumn.appendChild(spanDescripcion)
        divColumn.appendChild(spanDetalle)
        
        const spanFechaRegistro = document.createElement("span")
        spanFechaRegistro.className = "text-muted small"
        spanFechaRegistro.textContent = registro.fechaCreacion
        
        li.appendChild(divColumn)
        li.appendChild(spanFechaRegistro)

        if (listaActualUl) {
            listaActualUl.appendChild(li)
        }
    })
}

mostrarHistorial()