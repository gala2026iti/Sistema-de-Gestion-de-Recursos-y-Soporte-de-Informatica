const tabla = document.getElementById("tablaEquipos")
const cuerpoTabla = tabla.querySelector("tbody")

const btnRegistrarIncidencia = document.getElementById("btnRegistrarIncidencia")
const modalIncidenciaPrestamo = document.getElementById("modalIncidenciaPrestamo")
const formIncidenciaIndividual = document.getElementById("formIncidenciaIndividual")
const btnCancelarModal = document.getElementById("btnCancelarModal")
const equipoSeleccionado = document.getElementById("equipoSeleccionado")

const inputTipo = document.getElementById("tipo")
const inputAsunto = document.getElementById("asunto-modal")
const inputPersona = document.getElementById("persona-modal")
const inputDescripcion = document.getElementById("descripcion-modal")

const cargarPrestamos = () => {
    const prestamos = localStorage.getItem("prestamos")
    return (prestamos === null || prestamos === undefined || prestamos === "") ? [] : JSON.parse(prestamos)
}

const obtenerPrestamista = (idEquipo) => {
    const prestamos = cargarPrestamos()
    const prestamoEquipo = prestamos.find(p => p.idEquipo === idEquipo && p.devuelto === false)
    if (prestamoEquipo) {
        return `${prestamoEquipo.nombrePrestado} (${prestamoEquipo.cedulaPrestado})`
    } else {
        return "No asignado"
    }
}

const cargarSalonPrestamo = () => {
    const salones = localStorage.getItem("salones")
    if (salones === null || salones === undefined || salones === "") {
        return []
    } else {
        return JSON.parse(salones)
    }
}

const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    if (equipoSeleccionado) equipoSeleccionado.innerHTML = ""

    const salones = cargarSalonPrestamo()
    const salonPrestamo = salones.find(s => s.tipo === "prestamo")
    const listaEquipos = salonPrestamo ? (salonPrestamo.prestamos || salonPrestamo.espacios || []) : []

    listaEquipos.forEach(eq => {
        const idReal = typeof eq === "object" && eq !== null ? (eq.id || eq.codigo) : String(eq)
        const asignadoA = obtenerPrestamista(idReal)
        const estadoTexto = asignadoA !== "No asignado" ? "Prestado" : "Disponible"

        const tr = document.createElement("tr")

        const tdId = document.createElement("td")
        tdId.appendChild(document.createTextNode(idReal))

        const tdAsignado = document.createElement("td")
        tdAsignado.appendChild(document.createTextNode(asignadoA))

        const tdEstado = document.createElement("td")
        tdEstado.appendChild(document.createTextNode(estadoTexto))

        const tdAcciones = document.createElement("td")
        const btnVerIncidencias = document.createElement("button")
        btnVerIncidencias.className = "btn btn-warning btn-sm"
        btnVerIncidencias.appendChild(document.createTextNode("Ver Incidencias"))
        btnVerIncidencias.addEventListener("click", () => {
            window.location.href = `historialTickets.html?equipoId=${idReal}`
        })
        tdAcciones.appendChild(btnVerIncidencias)

        tr.appendChild(tdId)
        tr.appendChild(tdEstado)
        tr.appendChild(tdAsignado)
        tr.appendChild(tdAcciones)
        cuerpoTabla.appendChild(tr)

        if (equipoSeleccionado) {
            const opt = document.createElement("option")
            opt.value = idReal
            opt.appendChild(document.createTextNode(`PC: ${idReal}`))
            equipoSeleccionado.appendChild(opt)
        }
    })
}

const abrirModalIncidencia = () => {
    modalIncidenciaPrestamo.classList.replace("d-none", "d-flex")
}

const cerrarModalIncidencia = () => {
    formIncidenciaIndividual.reset()
    modalIncidenciaPrestamo.classList.replace("d-flex", "d-none")
}

actualizarTabla()

formIncidenciaIndividual.addEventListener("submit", (e) => {
    e.preventDefault()

    const idPC = equipoSeleccionado.value
    const tipo = inputTipo.value
    const asunto = inputAsunto.value.trim()
    const persona = inputPersona.value.trim()
    const desc = inputDescripcion.value.trim()
    
    const gravedadSeleccionada = formIncidenciaIndividual.querySelector('input[name="gravedad"]:checked').value

    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()

    const ticketsGuardados = localStorage.getItem("tickets")
    const tickets = ticketsGuardados ? JSON.parse(ticketsGuardados) : []

    const nuevoTicket = {
        id: "TK-" + Date.now(),
        equipoId: idPC,
        tipo: tipo,
        asunto: asunto,
        usuarioAfectado: persona,
        gravedad: gravedadSeleccionada,
        descripcion: desc,
        fecha: formatoFecha,
        estado: "abierto",
        sector: "Préstamos"
    }

    tickets.push(nuevoTicket)
    localStorage.setItem("tickets", JSON.stringify(tickets))

    alert(`Ticket registrado con exito`)
    cerrarModalIncidencia()
    actualizarTabla() 
})

btnRegistrarIncidencia.addEventListener("click", abrirModalIncidencia)
btnCancelarModal.addEventListener("click", cerrarModalIncidencia)