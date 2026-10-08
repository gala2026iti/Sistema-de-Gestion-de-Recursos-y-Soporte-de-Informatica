// VARIABLES
const parametros = new URLSearchParams(window.location.search)
const idBuscado = parametros.get("id")

const txtTitulo = document.getElementById("tituloTicket")
const contenedorAsunto = document.getElementById("contenedorAsunto")
const infoDocente = document.getElementById("infoDocente")
const infoFecha = document.getElementById("infoFecha")
const infoSalon = document.getElementById("infoSalon")
const infoPc = document.getElementById("infoPC")
const infoGravedad = document.getElementById("infoGravedad")
const infoCategoria = document.getElementById("infoCategoria")
const infoDescripcion = document.getElementById("infoDescripcion")
const infoEncargados = document.getElementById("infoEncargados")

const bloqueJustificacion = document.getElementById("bloqueJustificacion")
const infoJustificacion = document.getElementById("infoJustificacion")
const contenedorComentarios = document.getElementById("contenedorComentarios")

// FUNCIONES
const cargarTicketEspecifico = () => {
    const datos = localStorage.getItem("tickets")
    if (datos === null || datos === undefined || datos == "") return []
    const lista = JSON.parse(datos)
    return lista.find(t => String(t.id) === String(idBuscado)) //Por algun motivo, sin los String no funca el filtro y agarra todo
}


const rellenarCampos = () => {
    const ticket = cargarTicketEspecifico()

    if (ticket === null || ticket === undefined | ticket === "") {
        txtTitulo.innerText = "Incidencia no encontrada en la base de datos"
        return
    }
    txtTitulo.innerHTML = `<span class="fw-bold text-muted">${ticket.asunto} - </span> #${ticket.id} `


    contenedorAsunto.innerHTML = ""
    const campoEstado = document.createElement("span")
    const estadoLimpio = ticket.estado.toLowerCase()

    if (estadoLimpio === "resuelto") {
        campoEstado.className = "badge bg-success px-3 py-2 fs-6"
        campoEstado.innerText = "Cerrado / Resuelto"

        bloqueJustificacion.classList.remove("d-none") //Como el ticket esta resuelto, se muetra el campo de justificacion
        infoJustificacion.innerText = ticket.justificacion || "No se pudo obtener información de justificación."

    } else if (estadoLimpio === "en proceso") {
        campoEstado.className = "badge bg-warning text-dark px-3 py-2 fs-6"
        campoEstado.innerText = "En progreso"
    } else {
        campoEstado.className = "badge bg-danger px-3 py-2 fs-6"
        campoEstado.innerText = "Pendiente"
    }
    contenedorAsunto.appendChild(campoEstado)


    if (infoDocente) { //Se mantienen, con el objetivo de que, si se manipula el HTML de manera forzada, no colapse el js
        infoDocente.innerText = ""
        infoDocente.innerText = ticket.docente || "No registrado"
    }

    if (infoFecha) infoFecha.innerText = ticket.fechaCreacion || "N/A"
    if (infoSalon) infoSalon.innerText = ticket.salon || "General"
    if (infoPc) infoPc.innerText = ticket.equipoId || "N/A"
    if (infoGravedad) infoGravedad.innerText = ticket.gravedad || "N/A"
    if (infoCategoria) infoCategoria.innerText = ticket.tipo || "N/A"
    if (infoDescripcion) infoDescripcion.innerText = ticket.descripcion || "Descripción no disponible."
    if (infoEncargados) {
        const colaboradores = ticket.colaboradores && ticket.colaboradores.length > 0 ? ticket.colaboradores.join(", ") : "Sin técnicos asignados." //El join une los elementos de un array o set usando el separador 
        infoEncargados.innerText = colaboradores
    }
    if (contenedorComentarios) {
        contenedorComentarios.innerHTML = ""
        const comentarios = ticket.comentarios || []

        if (comentarios.length === 0) {
            const aviso = document.createElement("p")
            aviso.className = "text-muted small italic p-2"
            aviso.innerText = "No se encontraron comentarios."
            contenedorComentarios.appendChild(aviso)
        } else {
            comentarios.forEach(c => {
                const itemComentario = document.createElement("article")
                itemComentario.className = "card mb-3 shadow-sm"

                const cabecera = document.createElement("div")
                cabecera.className = "card-header d-flex justify-content-between align-items-center bg-white py-2"

                const autor = document.createElement("strong") //strong marca el texto como negrita, y en el sistema interno, se marca como contenido importante, afectando en el pasaje a voz
                autor.className = "text-secondary small"
                autor.innerText = c.autor

                const tiempo = document.createElement("span")
                tiempo.className = "text-muted small"
                tiempo.innerText = `el ${c.fecha}`

                cabecera.appendChild(autor)
                cabecera.appendChild(tiempo)

                const cuerpo = document.createElement("div")
                cuerpo.className = "card-body py-2 bg-light"

                const parrafo = document.createElement("p")
                parrafo.className = "card-text text-dark m-0 small"
                parrafo.innerText = c.texto

                cuerpo.appendChild(parrafo)
                itemComentario.appendChild(cabecera)
                itemComentario.appendChild(cuerpo)

                contenedorComentarios.appendChild(itemComentario)
            })
        }
    }
}

rellenarCampos()
