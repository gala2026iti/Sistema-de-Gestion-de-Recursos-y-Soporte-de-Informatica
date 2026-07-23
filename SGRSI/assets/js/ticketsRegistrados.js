// VARIABLES
const cuerpoTabla = document.querySelector("#tablaEquipos tbody")
const filtroFecha = document.getElementById("filtroFecha")
const filtroGravedad = document.getElementById("filtroGravedad")
const filtroClasificacion = document.getElementById("filtroClasificacion")
const filtroEstado = document.getElementById("filtroEstado")

// FUNCIONES
const obtenerUsuarioLogueado = () => {
    const usuarioSesion = localStorage.getItem("usuario")
    if (usuarioSesion === null || usuarioSesion === undefined || usuarioSesion === "") return "Desconocido"

    return JSON.parse(usuarioSesion)
}

const obtenerFecha = (dato) => {
    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()
    const formatoHora = fechaActual.getHours() + ":" + fechaActual.getMinutes()

    if(dato === "fecha"){
        return formatoFecha
    } else {
        return formatoHora
    }
}

const buscarNombre = (cedulaUsuario) => {
    let usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => u.usuario === cedulaUsuario)
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const cargarTicketsSistema = () => {
    const tickets = localStorage.getItem("tickets")
    if (tickets === null || tickets === undefined || tickets === "") return []

    return JSON.parse(tickets)
}


const capitalizar = (palabra) => {
  return palabra.charAt(0).toUpperCase() + palabra.slice(1)
}

const registrarHistorial = (asunto, detalle, idEquipo) => {
    const datos = localStorage.getItem("registroTickets")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        asuntoTicket: asunto,
        detalleTicket: detalle,
        equipoInvolucrado: idEquipo
    })
    localStorage.setItem("registroTickets", JSON.stringify(historial))
}

const actualizarTablaTickets = () => {
    cuerpoTabla.innerHTML = ""

    const usuarioLogueado = obtenerUsuarioLogueado()
    const idUsuarioActual = usuarioLogueado ? (usuarioLogueado.usuario || usuarioLogueado.id) : "N/A"

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

    ticketsFiltrados.sort((a, b) => {
        const fechaA = mapearFechaParaOrdenar(a.fechaCreacion)
        const fechaB = mapearFechaParaOrdenar(b.fechaCreacion)

        return filtroFecha.value === "recientes" ? fechaB - fechaA : fechaA - fechaB
    })

    ticketsFiltrados.forEach(ticket => {
        if (ticket.colaboradores) {
            ticket.colaboradores = ticket.colaboradores.filter(c => c !== null && c !== undefined)
        } else {
            ticket.colaboradores = []
        }

        const fila = document.createElement("tr")

        const tdAsunto = document.createElement("td")
        const titulo = document.createElement("b")
        titulo.innerText = ticket.asunto
        tdAsunto.appendChild(titulo)

        const tdTipo = document.createElement("td")
        tdTipo.innerText = ticket.tipo

        const tdGravedad = document.createElement("td")
        tdGravedad.innerText = capitalizar(ticket.gravedad) || "N/A"
        const gravedadLimpia = ticket.gravedad.toLowerCase()
        if (gravedadLimpia === "ligera") tdGravedad.className = "text-success fw-bold"
        else if (gravedadLimpia === "media") tdGravedad.className = "text-warning fw-bold"
        else if (gravedadLimpia === "grave") tdGravedad.className = "text-danger fw-bold"

        const tdEstado = document.createElement("td")
        const spanEstado = document.createElement("span")
        spanEstado.innerText = capitalizar(ticket.estado) || "N/A"

        const estadoLimpio = ticket.estado.toLowerCase()
        if (estadoLimpio === "resuelto") tdEstado.className = "text-success fw-bold"
        else if (estadoLimpio === "en proceso") tdEstado.className = "text-warning fw-bold"
        else if (estadoLimpio === "pendiente") tdEstado.className = "text-danger fw-bold"

        tdEstado.appendChild(spanEstado)

        const tdCreacion = document.createElement("td")
        tdCreacion.innerText = (`Creado por ${buscarNombre(ticket.docente) || 'N/A'} (${ticket.docente || 'N/A'}) el ${ticket.fechaCreacion || 'N/A'}`)

        const tdAccion = document.createElement("td")
        tdAccion.className = "d-flex gap-1"

        const btnAsignacion = document.createElement("button")

        let yaEstaAsignado = ticket.colaboradores.includes(idUsuarioActual)

        if (yaEstaAsignado) {
            btnAsignacion.innerText = "Desasignarme"
            btnAsignacion.className = "btn btn-sm btn-danger fw-semibold flex-grow-1"
        } else {
            btnAsignacion.innerText = "Asignar ticket"
            btnAsignacion.className = "btn btn-sm btn-primary btn-asignar fw-semibold flex-grow-1"
        }

        btnAsignacion.addEventListener("click", () => {
            const ticketsTotales = cargarTicketsSistema()
            const ticketEncontrado = ticketsTotales.find(t => String(t.id) === String(ticket.id))

            if (ticketEncontrado) {
                //Se eliminan los valores en null para evitar errores de inexistencia
                ticketEncontrado.colaboradores = (ticketEncontrado.colaboradores || []).filter(c => c !== null && c !== undefined)

                let descripcionHistorial = ticketEncontrado.asunto
                let detalleHistorial = ""
                if (ticket.estado.toLowerCase() === "resuelto") {
                    alert("Error: Este ticket ya esta resuelto y cerrado.")
                } else {
                    if (yaEstaAsignado) {//Si el ticket ya esta asignado, al presionar el boton se va a desvincular al usuario del ticket

                        ticketEncontrado.colaboradores = ticketEncontrado.colaboradores.filter(u => String(u) !== String(idUsuarioActual))

                        detalleHistorial = `${idUsuarioActual} (${buscarNombre(idUsuarioActual)}) se desvinculó del ticket\nEstado actual: ${ticketEncontrado.estado.toUpperCase()}`
                        alert("Te desvinculaste de este ticket correctamente.")

                    } else {
                        ticketEncontrado.colaboradores.push(idUsuarioActual)


                        detalleHistorial = `${idUsuarioActual} (${buscarNombre(idUsuarioActual)}) se unió como colaborador`
                        alert("Te asignaste al ticket con éxito.")
                    }

                    guardarTicketsSistema(ticketsTotales)
                    registrarHistorial(descripcionHistorial, detalleHistorial, ticket.idEquipo)
                }
            }
        })

        const btnVerDetalle = document.createElement("button")
        btnVerDetalle.innerText = "Ver"
        btnVerDetalle.className = "btn btn-sm btn-secondary fw-semibold"
        btnVerDetalle.addEventListener("click", () => {
            window.location.href = `administracion-tecnico/detalleTicket.html?id=${ticket.id}`
        })

        tdAccion.appendChild(btnAsignacion)
        tdAccion.appendChild(btnVerDetalle)

        fila.appendChild(tdAsunto)
        fila.appendChild(tdTipo)
        fila.appendChild(tdGravedad)
        fila.appendChild(tdEstado)
        fila.appendChild(tdCreacion)
        fila.appendChild(tdAccion)

        cuerpoTabla.appendChild(fila)
    })
}

const guardarTicketsSistema = (listaModificada) => {
    localStorage.setItem("tickets", JSON.stringify(listaModificada))
    actualizarTablaTickets()
}

const mapearFechaParaOrdenar = (stringFecha) => {
    if (stringFecha == null || stringFecha === undefined || stringFecha === "") return 0

    const partes = stringFecha.split("/")
    if (partes.length !== 3) return 0

    const dia = partes[0].padStart(2, "0")
    const mes = partes[1].padStart(2, "0")
    const anio = partes[2]

    return parseInt(anio + mes + dia)
}



actualizarTablaTickets()

// EVENTOS
filtroFecha.addEventListener("change", actualizarTablaTickets)
filtroGravedad.addEventListener("change", actualizarTablaTickets)
filtroClasificacion.addEventListener("change", actualizarTablaTickets)
filtroEstado.addEventListener("change", actualizarTablaTickets)