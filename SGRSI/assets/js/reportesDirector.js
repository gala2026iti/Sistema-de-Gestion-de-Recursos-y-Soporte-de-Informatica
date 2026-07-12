// VARIABLES
const colPendiente = document.querySelector("#tablaPendiente tbody")
const colEnProceso = document.querySelector("#tablaEnProceso tbody")
const colCerrado = document.querySelector("#tablaResuelto tbody")

// FUNCIONES
const cargarTickets = () => {
    const datos = localStorage.getItem("tickets")
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const renderizarTablero = () => {
    colPendiente.innerHTML = ""
    colEnProceso.innerHTML = ""
    colCerrado.innerHTML = ""

    const todosLosTickets = cargarTickets()
    const ticketsCerrados = []

    todosLosTickets.forEach(ticket => {
        const estado = ticket.estado.toLowerCase() //Prevencion en caso de que el texto presente mayusculas

        if (ticket.resuelto) {
            ticketsCerrados.push(ticket)
        } else {
            const fila = document.createElement("tr")
            const celda = document.createElement("td")
            celda.className = "ticket-marcado"

            const enlace = document.createElement("a")
            enlace.href = `detalleTicketDirector.html?id=${ticket.id}`
            enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2"

            enlace.innerText = ticket.asunto
            celda.appendChild(enlace)
            fila.appendChild(celda)

            if (estado === "en proceso") {
                colEnProceso.appendChild(fila)
            } else {
                colPendiente.appendChild(fila)
            }
        }
    })

    ticketsCerrados.sort((a, b) => b.id - a.id)

    const ultimosVeinte = ticketsCerrados.slice(0, 20)

    ultimosVeinte.forEach(ticket => {
        const fila = document.createElement("tr")
        const celda = document.createElement("td")
        celda.className = "ticket-marcado"

        const enlace = document.createElement("a")
        enlace.href = `detalleTicketDirector.html?id=${ticket.id}`
        enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2"

        enlace.innerText = ticket.asunto
        celda.appendChild(enlace)
        fila.appendChild(celda)

        colCerrado.appendChild(fila)
    })
}

// EVENTOS
renderizarTablero()
