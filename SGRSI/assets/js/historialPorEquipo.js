// VARIABLES
const cuerpoHistorialEquipo = document.getElementById("cuerpoHistorialEquipo")
const tituloHistorialEquipo = document.getElementById("titulo-historial-equipo")

// FUNCIONES
const obtenerIdEquipoBuscado = () => {
    return localStorage.getItem("idEquipoIncidencias")
}

const cargarTicketsGlobales = () => {
    const datos = localStorage.getItem("tickets")
    if (!datos) return []
    return JSON.parse(datos)
}

const renderizarHistorialDelEquipo = () => {
    cuerpoHistorialEquipo.innerHTML = ""
    
    const idEquipo = obtenerIdEquipoBuscado()

    tituloHistorialEquipo.textContent = `Historial de Incidencias - Equipo ${idEquipo}`
    
    const todosLosTickets = cargarTicketsGlobales()
    
    const idEquipoLimpio = String(idEquipo).trim().toLowerCase()
    
    const ticketsDelEquipo = todosLosTickets.filter(t => {
        if (!t.equipoId) return false
        return String(t.equipoId).trim().toLowerCase() === idEquipoLimpio
    })

    ticketsDelEquipo.sort((a, b) => Number(b.id) - Number(a.id))

    ticketsDelEquipo.forEach(ticket => {
        const fila = document.createElement("tr")

        const tdAsunto = document.createElement("td")
        tdAsunto.className = "fw-bold text-dark"
        tdAsunto.textContent = ticket.asunto || "Sin Asunto"

        const tdFecha = document.createElement("td")
        tdFecha.textContent = ticket.fechaCreacion || "N/A"

        const tdEstado = document.createElement("td")
        const spanEstado = document.createElement("span")
        spanEstado.textContent = String(ticket.estado).toUpperCase()
        
        const estadoLimpio = String(ticket.estado).toLowerCase().trim()
        if (estadoLimpio === "pendiente") spanEstado.className = "badge bg-secondary px-2 py-1"
        else if (estadoLimpio === "en proceso") spanEstado.className = "badge bg-info text-dark px-2 py-1"
        else if (estadoLimpio === "resuelto") spanEstado.className = "badge bg-success px-2 py-1"
        else spanEstado.className = "badge bg-dark px-2 py-1"
        tdEstado.appendChild(spanEstado)

        const tdGravedad = document.createElement("td")
        tdGravedad.textContent = ticket.gravedad || "General"
        
        const gravedadLimpia = String(ticket.gravedad).toLowerCase().trim()
        if (gravedadLimpia === "ligera") tdGravedad.className = "text-success fw-bold"
        else if (gravedadLimpia === "media") tdGravedad.className = "text-warning fw-bold"
        else if (gravedadLimpia === "grave") tdGravedad.className = "text-danger fw-bold"

        const tdAccion = document.createElement("td")
        const btnRedireccion = document.createElement("button")
        btnRedireccion.textContent = "Ver detalle"
        btnRedireccion.className = "btn btn-sm btn-primary fw-semibold"
        
        btnRedireccion.addEventListener("click", () => {
            window.location.href = `detalleTicket.html?id=${ticket.id}`
        })
        
        tdAccion.appendChild(btnRedireccion)

        fila.appendChild(tdAsunto)
        fila.appendChild(tdFecha)
        fila.appendChild(tdEstado)
        fila.appendChild(tdGravedad)
        fila.appendChild(tdAccion)

        cuerpoHistorialEquipo.appendChild(fila)
    })
}

renderizarHistorialDelEquipo()

// EVENTOS