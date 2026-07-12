// VARIABLES
const valoresPagina = new URLSearchParams(window.location.search)
const ticketIdActual = valoresPagina.get("id")

const tituloTicket = document.getElementById("tituloTicket")

const estadoPendiente = document.getElementById("pendiente")
const estadoEnProceso = document.getElementById("proceso")
const estadoResuelto = document.getElementById("resuelto")

const usuarioAsignado = document.getElementById("usuarioAsignado")
const btnAutoasignar = document.getElementById("btnAutoasignar")

const selectEstado = document.getElementById("selectorEstado")
const selectGravedad = document.getElementById("selectorGravedad")
const inputSalon = document.getElementById("ubicacionSalon")
const inputPc = document.getElementById("entradaPC")
const inputCategoria = document.getElementById("entradaCategoria")
const contenido = document.getElementById("contenido")

const contenedorJustificacion = document.getElementById("espacioJustificacion")
const justificacion = document.getElementById("justificacion")

const formControlTicket = document.getElementById("formDetalleTicket")
const btnFinalizarTicket = document.getElementById("btnFinalizarTicket")

const contenedorComentarios = document.getElementById("contenedorComentarios")
const nuevoComentario = document.getElementById("nuevoComentario")
const btnGuardarComentario = document.getElementById("btnGuardarComentario")

const tarjetaEscribirComentario = nuevoComentario ? nuevoComentario.closest(".card") : null

const btnVolver = document.getElementById("btnVolver")

// FUNCIONES
const obtenerUsuarioFirmado = () => {
    const usuario = localStorage.getItem("usuario")
    if (usuario === null || usuario === undefined || usuario === "") return null
    return JSON.parse(usuario)

}

const cargarTickets = () => {
    const datos = localStorage.getItem("tickets")
    if (!datos) return []
    return JSON.parse(datos)
}

const guardarTickets = (lista) => {
    localStorage.setItem("tickets", JSON.stringify(lista))
}

const registrarEnHistorialGeneral = (asunto, detalle, idEquipo) => {
    const datos = localStorage.getItem("registroTickets")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'),
        descripcionAccion: asunto,
        detalleOperador: detalle,
        equipoInvolucrado: idEquipo
    })
    localStorage.setItem("registroTickets", JSON.stringify(historial))
}

const mostrarInfoTicket = () => {
    const lista = cargarTickets()
    // Se hace pasaje a string para evitar errores de tipo con el igual estricto
    const ticket = lista.find(t => String(t.id) === String(ticketIdActual))

    if (!ticket) {
        tituloTicket.innerText = "Ticket no encontrado"
        return
    }

    tituloTicket.innerText = ticket.asunto + " "
    const spanId = document.createElement("span")
    spanId.className = "fw-bold"
    spanId.innerText = `#${ticket.id}`
    tituloTicket.appendChild(spanId)

    const estadoLimpio = ticket.estado ? ticket.estado.toLowerCase() : "pendiente"

    //Se despintan todas las opciones de estado debido a que aun no se sabe cual es el estado del ticket
    if (estadoResuelto) estadoResuelto.className = "d-none"
    if (estadoEnProceso) estadoEnProceso.className = "d-none"
    if (estadoPendiente) estadoPendiente.className = "d-none"

    if (estadoLimpio === "resuelto") {
        if (estadoResuelto) estadoResuelto.className = "badge bg-success px-2 py-1 fs-6"
        if (btnFinalizarTicket) btnFinalizarTicket.style.setProperty("display", "none")
        if (selectEstado) selectEstado.disabled = true

        if (contenedorJustificacion) contenedorJustificacion.classList.remove("d-none")
        if (justificacion) justificacion.value = ticket.justificacion || "Sin justificación registrada."

        if (tarjetaEscribirComentario) tarjetaEscribirComentario.style.setProperty("display", "none")

    } else {
        if (tarjetaEscribirComentario) tarjetaEscribirComentario.style.setProperty("display", "block")

        if (estadoLimpio === "en proceso") {
            if (estadoEnProceso) estadoEnProceso.className = "badge bg-warning px-2 py-1 fs-6"
            if (btnFinalizarTicket) btnFinalizarTicket.style.setProperty("display", "block")
            if (selectEstado) selectEstado.disabled = false
            if (contenedorJustificacion) contenedorJustificacion.classList.add("d-none")
        } else {
            if (estadoPendiente) estadoPendiente.className = "badge bg-danger px-2 py-1 fs-6"
            if (btnFinalizarTicket) btnFinalizarTicket.style.setProperty("display", "none")
            if (selectEstado) selectEstado.disabled = false
            if (contenedorJustificacion) contenedorJustificacion.classList.add("d-none")
        }
    }

    //Se filtran los colaboradores quitando los null y los undefined del array
    const colaboradoresValidos = (ticket.colaboradores || []).filter(c => c !== null && c !== undefined && c !== "")
    const encargados = colaboradoresValidos.length > 0 ? colaboradoresValidos.join(", ") : "Ninguno - Sin asignar"

    if (usuarioAsignado) usuarioAsignado.value = encargados
    if (selectEstado) selectEstado.value = estadoLimpio
    if (selectGravedad && ticket.gravedad) selectGravedad.value = ticket.gravedad.toLowerCase()
    if (inputSalon) inputSalon.value = ticket.salon || "No especificado"
    if (inputPc) inputPc.value = ticket.equipoId || "N/A"
    if (inputCategoria) inputCategoria.value = ticket.tipo || "General"
    if (contenido) contenido.value = ticket.descripcion || ""

    contenedorComentarios.innerHTML = ""
    const comentarios = ticket.comentarios || []

    comentarios.forEach(com => {
        const articulo = document.createElement("article")
        articulo.className = "card mb-4 shadow-sm"

        const divHeader = document.createElement("div")
        divHeader.className = "card-header d-flex justify-content-between align-items-center bg-light"

        const h3User = document.createElement("h3")
        h3User.className = "h6 m-0 fw-bold text-secondary"
        h3User.innerText = com.autor

        const spanTiempo = document.createElement("span")
        spanTiempo.className = "text-muted small"
        spanTiempo.innerText = `el ${com.fecha}`

        divHeader.appendChild(h3User)
        divHeader.appendChild(spanTiempo)

        const divBody = document.createElement("div")
        divBody.className = "card-body"

        const espacioContenido = document.createElement("p")
        espacioContenido.className = "card-text text-dark"
        espacioContenido.innerText = com.texto

        divBody.appendChild(espacioContenido)
        articulo.appendChild(divHeader)
        articulo.appendChild(divBody)

        contenedorComentarios.appendChild(articulo)
    })

    if (estadoLimpio === "resuelto") {
        if (nuevoComentario) {
            nuevoComentario.disabled = true
            nuevoComentario.placeholder = "Este ticket ha sido resuelto. No se permiten más comentarios."
        }
        if (btnGuardarComentario) {
            btnGuardarComentario.disabled = true
            btnGuardarComentario.classList.add("disabled")
        }
    } else {
        if (nuevoComentario) {
            nuevoComentario.disabled = false
            nuevoComentario.placeholder = "Escribe un comentario o actualización de la PC..."
        }
        if (btnGuardarComentario) {
            btnGuardarComentario.disabled = false
            btnGuardarComentario.classList.remove("disabled")
        }
    }
}

//EVENTOS

btnVolver.addEventListener("click", (e) => { //Si hay una página que puede ser ingresada desde multiples espacios, esta es una muy buena alternativa para volver mas facilmente
    history.back()
})

mostrarInfoTicket()

if (btnAutoasignar) {
    btnAutoasignar.addEventListener("click", () => {
        const usuarioLogueado = obtenerUsuarioFirmado()

        let idUsuarioActual = "N/A"
        if (usuarioLogueado) {
            idUsuarioActual = usuarioLogueado.usuario || "N/A"
        }

        if (idUsuarioActual === "N/A") {
            alert("Error: No se pudo identificar tu sesión de operador técnico.")
            return
        }

        const lista = cargarTickets()

        const ticket = lista.find(t => String(t.id) === String(ticketIdActual))

        if (ticket) {
            if (ticket.estado.toLowerCase() === "resuelto") {
                alert("Error: Este ticket ya está resuelto y cerrado.")
                return
            }

            //Se limpian los valores nulos del array de colaboradores para evitar errores
            ticket.colaboradores = (ticket.colaboradores || []).filter(col => col !== null && col !== undefined)

            const yaEstaAsignado = ticket.colaboradores.some(col => String(col) === String(idUsuarioActual))

            if (yaEstaAsignado) {
                alert("Error: Ya te encuentras asignado a este ticket.")
                return
            }

            ticket.colaboradores.push(idUsuarioActual)


            guardarTickets(lista)
            mostrarInfoTicket()
            registrarEnHistorialGeneral(ticket.asunto, `${idUsuarioActual} se unió como colaborador.`, ticket.equipoId)

            alert("Te has asignado exitosamente al ticket.")
        }
    })
}

if (formControlTicket) {
    formControlTicket.addEventListener("submit", (e) => {
        e.preventDefault()
        const lista = cargarTickets()
        const ticket = lista.find(t => String(t.id) === String(ticketIdActual))

        if (ticket) {
            if (ticket.estado.toLowerCase() === "resuelto") return
            const usuarioLogueado = obtenerUsuarioFirmado()
            const idUsuarioActual = usuarioLogueado ? usuarioLogueado.usuario : "N/A"

            if (selectEstado) ticket.estado = selectEstado.value
            if (selectGravedad) ticket.gravedad = selectGravedad.value

            guardarTickets(lista)
            mostrarInfoTicket()
            registrarEnHistorialGeneral(ticket.asunto, `${idUsuarioActual} actualizó el estado a ${ticket.estado.toUpperCase()} y la gravedad a ${selectGravedad.value.toUpperCase()}.`, ticket.equipoId)
            alert("Cambios guardados con éxito.")
        }
    })
}

if (btnFinalizarTicket) {
    btnFinalizarTicket.addEventListener("click", () => {
        const justificacionPrevia = prompt("Por favor, introduzca una justificación detallada de cómo se resolvió la incidencia:")
        const justificacionLimpia = justificacionPrevia.trim() || ""

        if (justificacionLimpia === "") {
            alert("Error: Operación cancelada. La justificación de cierre es obligatoria.")
            return
        }

        const lista = cargarTickets()
        const ticket = lista.find(t => String(t.id) === String(ticketIdActual))

        if (ticket) {
            const usuarioLogueado = obtenerUsuarioFirmado()
            const idUsuarioActual = usuarioLogueado ? usuarioLogueado.usuario : "N/A"

            ticket.estado = "resuelto"
            ticket.justificacion = justificacionLimpia

            guardarTickets(lista)
            mostrarInfoTicket()
            registrarEnHistorialGeneral(ticket.asunto, `${idUsuarioActual} finalizó el ticket. Resolución: ${justificacionLimpia}`, ticket.equipoId)
            alert("Ticket finalizado y cerrado con éxito.")
        }
    })
}

if (btnGuardarComentario) {
    btnGuardarComentario.addEventListener("click", () => {
        const textoComentario = nuevoComentario.value.trim()
        if (textoComentario === "") {
            alert("Error: El comentario no puede estar vacío.")
            return
        }

        const lista = cargarTickets()
        const ticket = lista.find(t => String(t.id) === String(ticketIdActual))

        if (ticket) {
            if (ticket.estado.toLowerCase() === "resuelto") {
                alert("Error: No se permiten comentarios en tickets cerrados.")
                return
            }

            const usuarioLogueado = obtenerUsuarioFirmado()
            const idUsuarioActual = usuarioLogueado ? usuarioLogueado.usuario : "N/A"

            if (!ticket.comentarios) ticket.comentarios = []

            ticket.comentarios.push({
                autor: idUsuarioActual,
                fecha: new Date().toLocaleDateString('es-ES'),
                texto: textoComentario
            })

            guardarTickets(lista)
            nuevoComentario.value = ""
            mostrarInfoTicket()
            alert("Comentario registrado.")
        }
    })
}