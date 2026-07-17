// VARIABLES
const contenedorHistorial = document.getElementById("contenedorHistorial")
const btnVolver = document.getElementById("btnVolver")

// EVENTOS
const cargarRegistrosTickets = () => {
    const datos = localStorage.getItem("registroTickets")
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const mapearFechaParaOrdenar = (stringFecha) => {
    if (stringFecha === null || stringFecha === undefined || stringFecha === "") {
        return 0
    }
    const partes = stringFecha.split("/")
    if (partes.length !== 3) {
        return 0
    }
    return partes[2] + partes[1].padStart(2, "0") + partes[0].padStart(2, "0") //El pad start añade ceros a la izquierda para que la longitud del texto sea la esperada
}

const mostrarHistorial = () => {
    contenedorHistorial.innerHTML = ""

    const urlParams = new URLSearchParams(window.location.search) //Esto debe aplicarse a casos similares usando URL
    const equipoIdUrl = urlParams.get("equipoId")                 //Es un objeto encargado de leer los datos del URL

    let historial = cargarRegistrosTickets()

    if (equipoIdUrl) {
        const idBuscado = equipoIdUrl.trim()
        historial = historial.filter(ticket => {
            const idTicketEquipo = ticket.equipoInvolucrado
            return idTicketEquipo === idBuscado
        })
    }

    historial.sort((a, b) => {
        return mapearFechaParaOrdenar(b.fecha) - mapearFechaParaOrdenar(a.fecha)
    })

    let ultimaFecha = ""
    let listaActualUl = null

    historial.forEach(registro => {
        if (ultimaFecha !== registro.fecha) {
            ultimaFecha = registro.fecha

            const spanFecha = document.createElement("span")
            spanFecha.className = "fw-bold d-block mt-3 text-secondary"
            spanFecha.innerText = `Intervenciones del ${registro.fecha}`
            contenedorHistorial.appendChild(spanFecha)

            listaActualUl = document.createElement("ul")
            listaActualUl.className = "historial-lista mt-2 mb-3 list-unstyled"
            contenedorHistorial.appendChild(listaActualUl)
        }

        const li = document.createElement("li")
        li.className = "historial-contenido d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded shadow-sm"

        const columna = document.createElement("div")
        columna.className = "d-flex flex-column"

        const spanDescripcion = document.createElement("span")
        spanDescripcion.className = "fw-bold text-dark"
        spanDescripcion.innerText = registro.asuntoTicket

        const detalles = document.createElement("span")
        detalles.className = "text-muted small"
        detalles.innerText = registro.detalleTicket

        columna.appendChild(spanDescripcion)
        columna.appendChild(detalles)

        const spanFechaRegistro = document.createElement("span")
        spanFechaRegistro.className = "text-muted small"
        spanFechaRegistro.innerText = registro.hora
        

        li.appendChild(columna)
        li.appendChild(spanFechaRegistro)

        listaActualUl.appendChild(li)
    })
}

mostrarHistorial()

btnVolver.addEventListener("click", (e) => { //Si hay una página que puede ser ingresada desde multiples espacios, esta es una muy buena alternativa para volver mas facilmente
    history.back()
})