// VARIABLES
const parametros = new URLSearchParams(window.location.search)
const idBuscado = parametros.get("id")

const txtTitulo = document.getElementById("txt-titulo-ticket")
const contenedorBadges = document.getElementById("contenedor-badges")

const infoDocente = document.getElementById("info-docente")
const infoFecha = document.getElementById("info-fecha")
const infoSalon = document.getElementById("info-salon")
const infoPc = document.getElementById("info-pc")
const infoGravedad = document.getElementById("info-gravedad")
const infoCategoria = document.getElementById("info-categoria")
const infoDescripcion = document.getElementById("info-descripcion")
const infoEncargados = document.getElementById("info-encargados")

const bloqueJustificacion = document.getElementById("bloque-justificacion")
const infoJustificacion = document.getElementById("info-justificacion")
const contenedorComentarios = document.getElementById("contenedor-comentarios")

// FUNCIONES
const cargarTicketEspecifico = () => {
    const datos = localStorage.getItem("tickets")
    if (!datos) return null
    const lista = JSON.parse(datos)
    return lista.find(t => String(t.id) === String(idBuscado))
}

const rellenarCampos = () => {
    const ticket = cargarTicketEspecifico()

    if (!ticket) {
        if (txtTitulo) {
            txtTitulo.textContent = ""
            txtTitulo.appendChild(document.createTextNode("Incidencia no encontrada en la base de datos"))
        }
        return
    }

    if (txtTitulo) {
        txtTitulo.textContent = ""
        txtTitulo.appendChild(document.createTextNode(ticket.asunto + " "))
        const spanId = document.createElement("span")
        spanId.className = "fw-bold text-muted"
        spanId.appendChild(document.createTextNode(`#${ticket.id}`))
        txtTitulo.appendChild(spanId)
    }

    if (contenedorBadges) {
        contenedorBadges.innerHTML = ""
        const badgeEstado = document.createElement("span")
        const estadoLimpio = String(ticket.estado).toLowerCase()

        if (ticket.resuelto === true || estadoLimpio === "resuelto") {
            badgeEstado.className = "badge bg-success px-3 py-2 fs-6"
            badgeEstado.appendChild(document.createTextNode("Cerrado / Resuelto"))
            
            if (bloqueJustificacion) bloqueJustificacion.classList.remove("d-none")
            if (infoJustificacion) {
                infoJustificacion.textContent = ""
                infoJustificacion.appendChild(document.createTextNode(ticket.justificacion || "Sin desglose de justificación."))
            }
        } else if (estadoLimpio === "en proceso") {
            badgeEstado.className = "badge bg-warning text-dark px-3 py-2 fs-6"
            badgeEstado.appendChild(document.createTextNode("En progreso"))
        } else {
            badgeEstado.className = "badge bg-danger px-3 py-2 fs-6"
            badgeEstado.appendChild(document.createTextNode("Pendiente"))
        }
        contenedorBadges.appendChild(badgeEstado)
    }

    if (infoDocente) {
        infoDocente.textContent = ""
        infoDocente.appendChild(document.createTextNode(ticket.docente || "No registrado"))
    }

    if (infoFecha) {
        infoFecha.textContent = ""
        infoFecha.appendChild(document.createTextNode(ticket.fechaCreacion || "N/A"))
    }

    if (infoSalon) {
        infoSalon.textContent = ""
        infoSalon.appendChild(document.createTextNode(ticket.salon || "General"))
    }

    if (infoPc) {
        infoPc.textContent = ""
        infoPc.appendChild(document.createTextNode(ticket.equipoId || "N/A"))
    }

    if (infoGravedad) {
        infoGravedad.textContent = ""
        infoGravedad.appendChild(document.createTextNode(String(ticket.gravedad || "Ligera").toUpperCase()))
    }

    if (infoCategoria) {
        infoCategoria.textContent = ""
        infoCategoria.appendChild(document.createTextNode(ticket.tipo || "General"))
    }

    if (infoDescripcion) {
        infoDescripcion.textContent = ""
        infoDescripcion.appendChild(document.createTextNode(ticket.descripcion || "Sin descripción adicional."))
    }

    if (infoEncargados) {
        infoEncargados.textContent = ""
        const colaboradores = ticket.colaboradores && ticket.colaboradores.length > 0 
            ? ticket.colaboradores.join(", ") 
            : "Sin técnicos asignados actualmente"
        infoEncargados.appendChild(document.createTextNode(colaboradores))
    }

    if (contenedorComentarios) {
        contenedorComentarios.innerHTML = ""
        const comentarios = ticket.comentarios || []

        if (comentarios.length === 0) {
            const aviso = document.createElement("p")
            aviso.className = "text-muted small italic p-2"
            aviso.appendChild(document.createTextNode("No se registran comentarios en este flujo operativo."))
            contenedorComentarios.appendChild(aviso)
        } else {
            comentarios.forEach(com => {
                const itemComentario = document.createElement("article")
                itemComentario.className = "card mb-3 shadow-sm"

                const cabecera = document.createElement("div")
                cabecera.className = "card-header d-flex justify-content-between align-items-center bg-white py-2"

                const autor = document.createElement("strong")
                autor.className = "text-secondary small"
                autor.appendChild(document.createTextNode(com.autor))

                const tiempo = document.createElement("span")
                tiempo.className = "text-muted small"
                tiempo.appendChild(document.createTextNode(`el ${com.fecha}`))

                cabecera.appendChild(autor)
                cabecera.appendChild(tiempo)

                const cuerpo = document.createElement("div")
                cuerpo.className = "card-body py-2 bg-light"

                const parrafo = document.createElement("p")
                parrafo.className = "card-text text-dark m-0 small"
                parrafo.appendChild(document.createTextNode(com.texto))

                cuerpo.appendChild(parrafo)
                itemComentario.appendChild(cabecera)
                itemComentario.appendChild(cuerpo)

                contenedorComentarios.appendChild(itemComentario)
            })
        }
    }
}

    rellenarCampos()
