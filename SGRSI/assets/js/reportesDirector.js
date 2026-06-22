// VARIABLES
const colPendiente = document.getElementById("columna-pendiente")
const colEnProceso = document.getElementById("columna-en-proceso")
const colCerrado = document.getElementById("columna-cerrado")

// FUNCIONES
const cargarTickets = () => {
    const datos = localStorage.getItem("tickets")
    if (!datos) return []
    return JSON.parse(datos)
}

const renderizarTablero = () => {
    colPendiente.innerHTML = ""
    colEnProceso.innerHTML = ""
    colCerrado.innerHTML = ""

    const todosLosTickets = cargarTickets()
    const ticketsCerrados = []

    todosLosTickets.forEach(ticket => {
        const estadoLimpio = String(ticket.estado).toLowerCase()

        if (ticket.resuelto === true || estadoLimpio === "resuelto" || estadoLimpio === "cerrado") {
            ticketsCerrados.push(ticket)
        } else {
            const fila = document.createElement("tr")
            const celda = document.createElement("td")
            celda.className = "ticket-marcado"

            const enlace = document.createElement("a")
            enlace.href = `detalleTicketDirector.html?id=${ticket.id}`
            enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2"
            
            const textoNode = document.createTextNode(ticket.asunto)
            enlace.appendChild(textoNode)
            celda.appendChild(enlace)
            fila.appendChild(celda)

            if (estadoLimpio === "en proceso") {
                colEnProceso.appendChild(fila)
            } else {
                colPendiente.appendChild(fila)
            }
        }
    })

    ticketsCerrados.sort((a, b) => Number(b.id) - Number(a.id))
    
    const ultimosVeinte = ticketsCerrados.slice(0, 20)

    ultimosVeinte.forEach(ticket => {
        const fila = document.createElement("tr")
        const celda = document.createElement("td")
        celda.className = "ticket-marcado"

        const enlace = document.createElement("a")
        enlace.href = `detalleTicketDirector.html?id=${ticket.id}`
        enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2"

        const textoNode = document.createTextNode(ticket.asunto)
        enlace.appendChild(textoNode)
        celda.appendChild(enlace)
        fila.appendChild(celda)

        colCerrado.appendChild(fila)
    })
}

// EVENTOS
    renderizarTablero()
