// VARIABLES
const columnaPendiente = document.querySelector("#tablaPendiente tbody")
const columnaEnProceso = document.querySelector("#tablaEnProceso tbody")
const columnaResuelto = document.querySelector("#tablaResuelto tbody")

// FUNCIONES
const obtenerOperadorLogueado = () => {
    const sesion = localStorage.getItem("usuario")
    if (sesion === null || sesion === undefined || sesion === "") {
        return []
    }
    return JSON.parse(sesion)
}

const cargarTicketsGlobales = () => {
    const tickets = localStorage.getItem("tickets")
    if (tickets === null || tickets === undefined || tickets === "") {
        return []
    }
    return JSON.parse(tickets)
}

const renderizarTableroKanban = () => {
    columnaPendiente.innerHTML = ""
    columnaEnProceso.innerHTML = ""
    columnaResuelto.innerHTML = ""

    const usuario = obtenerOperadorLogueado()
    const idUsuarioActual = usuario.usuario || "N/A"

    const todosLosTickets = cargarTicketsGlobales()

    const ticketsResueltosUsuario = []
    todosLosTickets.forEach(ticket => {
        if (!ticket.colaboradores) ticket.colaboradores = [] //Si en algun caso no existen colaboradores, se crea un array vacio
        if (ticket.colaboradores.includes(idUsuarioActual)) {
            const estado = ticket.estado.toLowerCase()

            if (estado === "resuelto") {
                ticketsResueltosUsuario.push(ticket)
            } else {
                const tr = document.createElement("tr")
                const td = document.createElement("td")
                td.className = "ticket-marcado"

                const enlace = document.createElement("a")
                enlace.href = `detalleTicket.html?id=${ticket.id}`
                enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2"

                enlace.innerText = ticket.asunto
                td.appendChild(enlace)
                tr.appendChild(td)

                if (estado === "en proceso") {
                    columnaEnProceso.appendChild(tr)
                } else {
                    columnaPendiente.appendChild(tr)
                }
            }
        }

    })

    ticketsResueltosUsuario.sort((a, b) => b.id - a.id)

    const ultimosDiezResueltos = ticketsResueltosUsuario.slice(0, 10)

    ultimosDiezResueltos.forEach(ticket => {
        const tr = document.createElement("tr")
        const td = document.createElement("td")
        td.className = "ticket-marcado"

        const enlace = document.createElement("a")
        enlace.href = `detalleTicket.html?id=${ticket.id}`
        enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2"

        enlace.innerText = ticket.asunto
        td.appendChild(enlace)
        tr.appendChild(td)

        columnaResuelto.appendChild(tr)
    })
}

renderizarTableroKanban()
