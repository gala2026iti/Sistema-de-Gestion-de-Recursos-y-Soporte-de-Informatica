// VARIABLES
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

// FUNCIONES
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
        const salonesJSON = JSON.parse(salones)
        return salonesJSON.find(s => s.tipo === "prestamo")
    }
}

const cargarEquiposFiltrados = () => {
    let equiposEncontrados = []
    const salonPrestamo = cargarSalonPrestamo()
    if (!salonPrestamo || !salonPrestamo.espacios) return []
    
    const espacios = salonPrestamo.espacios
    espacios.forEach(espacio => {
        if (espacio !== null && espacio !== undefined && espacio !== "") {
            equiposEncontrados.push(espacio)
        }
    })
    return equiposEncontrados
}

const obtenerEquipo = (idEquipo) => {
    const equipos = localStorage.getItem("equipos")
    if (equipos === null || equipos === undefined || equipos === "") {
        return []
    } else {
        const equiposJSON = JSON.parse(equipos)
        return equiposJSON.find(e => e.id === idEquipo)
    }
}

const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    const equiposPrestamo = cargarEquiposFiltrados()

    equiposPrestamo.forEach(e => {
        const fila = document.createElement("tr")
        
        const idEquipo = document.createElement("td")
        idEquipo.innerText = e.id

        const estadoEquipo = document.createElement("td")
        const equipoInfo = obtenerEquipo(e.id)
        estadoEquipo.innerText = equipoInfo && equipoInfo.activo ? "Activo" : "Inactivo"

        const prestamistaEquipo = document.createElement("td")
        prestamistaEquipo.innerText = obtenerPrestamista(e.id)

        const opciones = document.createElement("td")
        const botonIncidencias = document.createElement("button")
        botonIncidencias.addEventListener("click", () => {
            localStorage.setItem("idEquipoIncidencias", e.id)
            window.location.href = "historialPorEquipo.html"
        })

        botonIncidencias.textContent = "Ver Incidencias"
        botonIncidencias.classList.add("btn", "btn-warning")

        opciones.appendChild(botonIncidencias)
        fila.appendChild(idEquipo)
        fila.appendChild(estadoEquipo)
        fila.appendChild(prestamistaEquipo)
        fila.appendChild(opciones)

        cuerpoTabla.appendChild(fila)
    })
}

const obtenerEquiposDisponiblesParaIncidencia = () => {
    const equiposPrestamo = cargarEquiposFiltrados()
    const prestamos = cargarPrestamos()

    return equiposPrestamo.filter(e => {
        const estaPrestado = prestamos.some(p => p.idEquipo === e.id && p.devuelto === false)
        return !estaPrestado
    })
}

const abrirModalIncidencia = () => {
    equipoSeleccionado.innerHTML = '<option value="">Seleccione una opción</option>'
    const disponibles = obtenerEquiposDisponiblesParaIncidencia()

    if (disponibles.length === 0) {
        alert("No hay equipos de prestamo disponibles (sin prestar) en este momento para reportar")
        return
    }

    disponibles.forEach(e => {
        const opt = document.createElement("option")
        opt.value = e.id
        opt.textContent = `PC: ${e.id}`
        equipoSeleccionado.appendChild(opt)
    })

    modalIncidenciaPrestamo.classList.replace("d-none", "d-flex")
}

const cerrarModalIncidencia = () => {
    formIncidenciaIndividual.reset()
    modalIncidenciaPrestamo.classList.replace("d-flex", "d-none")
}

actualizarTabla()

// EVENTOS
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