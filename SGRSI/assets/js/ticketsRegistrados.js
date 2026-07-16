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
        tdGravedad.innerText = ticket.gravedad
        const gravedadLimpia = ticket.gravedad.toLowerCase()
        if (gravedadLimpia === "ligera") tdGravedad.className = "text-success fw-bold"
        else if (gravedadLimpia === "media") tdGravedad.className = "text-warning fw-bold"
        else if (gravedadLimpia === "grave") tdGravedad.className = "text-danger fw-bold"

        const tdEstado = document.createElement("td")
        const spanEstado = document.createElement("span")
        spanEstado.innerText = ticket.estado
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

                        detalleHistorial = `${idUsuarioActual} (${buscarNombre(idUsuarioActual)}) se desvinculó del ticket (Estado actual: ${ticketEncontrado.estado.toUpperCase()})`
                        alert("Te desvinculaste de este ticket correctamente.")

                    } else {
                        ticketEncontrado.colaboradores.push(idUsuarioActual)


                        detalleHistorial = `${idUsuarioActual} (${buscarNombre(idUsuarioActual)}) se asignó al ticket`
                        alert("Te asignaste al ticket con éxito.")
                    }

                    guardarTicketsSistema(ticketsTotales)
                    registrarEnHistorialSistema(descripcionHistorial, detalleHistorial)
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

const registrarEnHistorialSistema = (descripcion, detalle) => {
    const datosHistorial = localStorage.getItem("registroTickets")
    let listaHistorial = []

    if (datosHistorial === null || datosHistorial === undefined || datosHistorial === "") {
        listaHistorial = []
    } else {
        listaHistorial = JSON.parse(datosHistorial)
    }

    const nuevoRegistro = {
        id: listaHistorial.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'), //Es para que adopte el formato de dd/mm/aaaa
        descripcionAccion: descripcion,
        detalleOperador: detalle
    }

    listaHistorial.push(nuevoRegistro)
    localStorage.setItem("registroTickets", JSON.stringify(listaHistorial))
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