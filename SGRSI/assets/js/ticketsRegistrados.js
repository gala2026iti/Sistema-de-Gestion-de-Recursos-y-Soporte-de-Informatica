// VARIABLES
const cuerpoTablaTickets = document.getElementById("cuerpoTablaTickets")
const filtroFecha = document.getElementById("filtroFecha")
const filtroGravedad = document.getElementById("filtroGravedad")
const filtroClasificacion = document.getElementById("filtroClasificacion")
const filtroEstado = document.getElementById("filtroEstado")

// FUNCIONES
const obtenerUsuarioLogueado = () => {
    const usuarioSesion = localStorage.getItem("usuario")
    if (usuarioSesion) {
        return JSON.parse(usuarioSesion)
    }
    return null
}

const cargarTicketsSistema = () => {
    const datos = localStorage.getItem("tickets")
    if (datos === null || datos === undefined || datos === "") {
        return []
    }
    return JSON.parse(datos)
}

const guardarTicketsSistema = (listaModificada) => {
    localStorage.setItem("tickets", JSON.stringify(listaModificada))
    actualizarTablaTickets()
}

const registrarEnHistorialSistema = (descripcion, detalle) => {
    const datosHistorial = localStorage.getItem("registroTickets")
    let listaHistorial = []
    
    if (datosHistorial !== null && datosHistorial !== undefined && datosHistorial !== "") {
        listaHistorial = JSON.parse(datosHistorial)
    }

    const nuevoRegistro = {
        id: listaHistorial.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'),
        descripcionAccion: descripcion,
        detalleOperador: detalle
    }

    listaHistorial.push(nuevoRegistro)
    localStorage.setItem("registroTickets", JSON.stringify(listaHistorial))
}

const mapearFechaParaOrdenar = (stringFecha) => { //se tiene que separar la fecha sino el filtro de antiguedad no funca
    if (!stringFecha) return 0
    const partes = stringFecha.split("/")
    if (partes.length !== 3) return 0
    
    const dia = partes[0].padStart(2, "0") 
    const mes = partes[1].padStart(2, "0")
    const anio = partes[2]
    
    return parseInt(anio + mes + dia)
}

const actualizarTablaTickets = () => {
    cuerpoTablaTickets.innerHTML = ""

    const usuarioLogueado = obtenerUsuarioLogueado()
    const idUsuarioActual = usuarioLogueado ? (usuarioLogueado.usuario || "Técnico Genérico") : "Administrador Técnico"

    const listaTickets = cargarTicketsSistema()
    let ticketsFiltrados = []

    listaTickets.forEach(ticket => {
        const coincideGravedad = filtroGravedad.value === "" || ticket.gravedad.toLowerCase() === filtroGravedad.value
        const coincideClasificacion = filtroClasificacion.value === "" || ticket.tipo.toLowerCase() === filtroClasificacion.value
        const coincideEstado = filtroEstado.value === "" || ticket.estado.toLowerCase() === filtroEstado.value

        if (coincideGravedad && coincideClasificacion && coincideEstado) {
            ticketsFiltrados.push(ticket)
        }
    })

    for (let i = 0 ;i < ticketsFiltrados.length - 1 ;i++) { //organizar por fecha haciendo uso de un sistema de filtrado
        for (let j = 0; j < ticketsFiltrados.length - i - 1 ;j++) {
            const fechaA = mapearFechaParaOrdenar(ticketsFiltrados[j].fechaCreacion)
            const fechaB = mapearFechaParaOrdenar(ticketsFiltrados[j + 1].fechaCreacion)

            let intercambiar = false
            if (filtroFecha.value === "recientes" && fechaA < fechaB) intercambiar = true
            if (filtroFecha.value === "antiguos" && fechaA > fechaB) intercambiar = true

            if (intercambiar) {
                const temporal = ticketsFiltrados[j]
                ticketsFiltrados[j] = ticketsFiltrados[j + 1]
                ticketsFiltrados[j + 1] = temporal
            }
        }
    }

    ticketsFiltrados.forEach(ticket => {
        const fila = document.createElement("tr")

        const tdAsunto = document.createElement("td")
        const enlace = document.createElement("a")
        enlace.href = `administracion-tecnico/detalleTicket.html?id=${ticket.id}`
        enlace.className = "ticket-link"
        enlace.textContent = ticket.asunto
        tdAsunto.appendChild(enlace)

        const tdTipo = document.createElement("td")
        tdTipo.textContent = ticket.tipo

        const tdGravedad = document.createElement("td")
        tdGravedad.textContent = ticket.gravedad
        const gravedadLimpia = String(ticket.gravedad).toLowerCase()
        if (gravedadLimpia === "ligera") tdGravedad.className = "text-success fw-bold"
        else if (gravedadLimpia === "media") tdGravedad.className = "text-warning fw-bold"
        else if (gravedadLimpia === "grave") tdGravedad.className = "text-danger fw-bold"

        const tdEstado = document.createElement("td")
        const spanEstado = document.createElement("span")
        spanEstado.textContent = String(ticket.estado).toUpperCase()
        
        const estadoLimpio = String(ticket.estado).toLowerCase()
        if (estadoLimpio === "pendiente") spanEstado.className = "badge bg-secondary text-wrap"
        else if (estadoLimpio === "en proceso") spanEstado.className = "badge bg-info text-dark text-wrap"
        else if (estadoLimpio === "resuelto") spanEstado.className = "badge bg-success text-wrap"
        tdEstado.appendChild(spanEstado)

        const tdCreacion = document.createElement("td")
        const textoCreadoPor = document.createTextNode("Creado por ")
        const subDocente = document.createElement("u")
        subDocente.className = "fw-bold"
        subDocente.textContent = ticket.docente || "Sistema"
        const textoFecha = document.createTextNode(` el ${ticket.fechaCreacion || 'N/A'}`)
        
        tdCreacion.appendChild(textoCreadoPor)
        tdCreacion.appendChild(subDocente)
        tdCreacion.appendChild(textoFecha)

        const tdAccion = document.createElement("td")
        const btnAsignacion = document.createElement("button")
        
        let yaEstaAsignado = false
        if (ticket.colaboradores && ticket.colaboradores.includes(idUsuarioActual)) { //verifica si la id del usuario esta en el array de colaboradores
            yaEstaAsignado = true
        }

        if (yaEstaAsignado) {
            btnAsignacion.textContent = "Desasignarme"
            btnAsignacion.className = "btn btn-sm btn-danger w-100 fw-semibold"
        } else {
            btnAsignacion.textContent = "Asignar ticket"
            btnAsignacion.className = "btn btn-sm btn-primary w-100 btn-asignar fw-semibold"
        }

        btnAsignacion.addEventListener("click", () => {
            const ticketsTotales = cargarTicketsSistema()
            const ticketEncontrado = ticketsTotales.find(t => t.id === ticket.id)

            if (ticketEncontrado) {
                if (!ticketEncontrado.colaboradores) {
                    ticketEncontrado.colaboradores = []
                }

                let descripcionHistorial = ticketEncontrado.asunto
                let detalleHistorial = ""

                if (yaEstaAsignado) {
                    const nuevaLista = []
                    ticketEncontrado.colaboradores.forEach(user => {
                        if (user !== idUsuarioActual) {
                            nuevaLista.push(user)
                        }
                    })
                    ticketEncontrado.colaboradores = nuevaLista

                    if (ticketEncontrado.colaboradores.length === 0 && ticketEncontrado.resuelto === false) {
                        ticketEncontrado.estado = "pendiente"
                    }

                    detalleHistorial = `${idUsuarioActual} se desvinculó (Estado actual: ${ticketEncontrado.estado.toUpperCase()})`
                    alert("Te has desvinculado de este ticket correctamente.")
                } else {
                    ticketEncontrado.colaboradores.push(idUsuarioActual)
                    
                    if (ticketEncontrado.estado === "pendiente") {
                        ticketEncontrado.estado = "en proceso"
                    }

                    detalleHistorial = `${idUsuarioActual} se asignó al ticket (Pasó a En Proceso)`
                    alert("Te has asignado al ticket con éxito.")
                }

                guardarTicketsSistema(ticketsTotales)
                registrarEnHistorialSistema(descripcionHistorial, detalleHistorial)
            }
        })

        tdAccion.appendChild(btnAsignacion)

        fila.appendChild(tdAsunto)
        fila.appendChild(tdTipo)
        fila.appendChild(tdGravedad)
        fila.appendChild(tdEstado)
        fila.appendChild(tdCreacion)
        fila.appendChild(tdAccion)

        cuerpoTablaTickets.appendChild(fila)
    })
}

// EVENTOS 
filtroFecha.addEventListener("change", renderizarTablaTickets)
filtroGravedad.addEventListener("change", renderizarTablaTickets)
filtroClasificacion.addEventListener("change", renderizarTablaTickets)
filtroEstado.addEventListener("change", renderizarTablaTickets)

renderizarTablaTickets()