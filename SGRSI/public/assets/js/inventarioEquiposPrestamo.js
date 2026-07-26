// VARIABLES
const cuerpoTabla = document.querySelector("#tablaEquipos tbody")

const btnRegistrarIncidencia = document.getElementById("btnRegistrarIncidencia")
const modalIncidenciaPrestamo = document.getElementById("modalIncidenciaPrestamo")
const formIncidenciaIndividual = document.getElementById("formIncidenciaIndividual")
const btnCancelarModal = document.getElementById("btnCancelar")
const equipoSeleccionado = document.getElementById("equipoSeleccionado")

const inputTipo = document.getElementById("tipo")
const inputAsunto = document.getElementById("asunto")
const inputPersona = document.getElementById("persona")
const inputDescripcion = document.getElementById("descripcion")

// FUNCIONES
const cargarPrestamos = () => {
    const prestamos = localStorage.getItem("prestamos")
    if (prestamos === null || prestamos === undefined || prestamos === "") return []
    return JSON.parse(prestamos)
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


const registrarHistorial = (detalle, idTicket) => {
    const datos = localStorage.getItem("registroTickets")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        detalleTicket: detalle,
        idTicket: idTicket
    })
    localStorage.setItem("registroTickets", JSON.stringify(historial))
}

const cargarEquipos = () => {
    const equipos = localStorage.getItem("equipos")
    if (equipos === null || equipos === "" || equipos === undefined) return []
    return JSON.parse(equipos)
}

const obtenerPrestamista = (idEquipo) => {
    const prestamos = cargarPrestamos()
    //Un equipo está prestado si figura en la lista y devuelto es false, los String son para evitar errors de tipado
    const prestamoEquipo = prestamos.find(p => String(p.idEquipo) === String(idEquipo) && p.devuelto === false)
    if (prestamoEquipo) {
        return `${prestamoEquipo.nombrePrestado} (${prestamoEquipo.ciPrestado})`
    } else {
        return "No asignado"
    }
}

const cargarSalonPrestamo = () => {
    const salones = localStorage.getItem("salones")
    if (salones === null || salones === undefined || salones === "") return []
    return JSON.parse(salones)
}

const cargarTickets = () => {
    const tickets = localStorage.getItem("tickets")
    if (tickets === null || tickets === undefined || tickets === "") return []
    return JSON.parse(tickets)
}

const tieneTicketsActivos = (idEquipo) => {
    const tickets = cargarTickets()
    return tickets.some(t =>
        String(t.equipoId) === String(idEquipo) && (t.estado.toLowerCase() === "pendiente" || t.estado.toLowerCase() === "en proceso")
    )
}

const buscarNombre = (cedulaUsuario) => {
    let usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => u.usuario === cedulaUsuario)
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    if (equipoSeleccionado) equipoSeleccionado.innerHTML = ""

    const salones = cargarSalonPrestamo()
    const salonPrestamo = salones.find(s => s.tipo === "prestamo")
    const listaEquipos = salonPrestamo ? salonPrestamo.espacios : []

    const todosLosEquipos = cargarEquipos()

        if (listaEquipos.length === 0) {
        const filaSinResultados = document.createElement("tr")
        const celdaSinResultados = document.createElement("td")

        celdaSinResultados.colSpan = 9 // colSpan es para que ocupe todas las columnas, porque sino queda solo en la primera y se ve re gagá

        celdaSinResultados.className = "text-center py-4 text-muted bg-light fw-semibold"
        celdaSinResultados.innerText = "No se encontraron Equipos de Préstamo."

        filaSinResultados.appendChild(celdaSinResultados)
        cuerpoTabla.appendChild(filaSinResultados)

    } else {

    listaEquipos.forEach(eq => {
        const infoGlobalEquipo = todosLosEquipos.find(e => String(e.id) === String(eq.id))
        const esActivo = infoGlobalEquipo.activo

        const asignadoA = obtenerPrestamista(eq.id)

        let estadoTexto = "Disponible"
        if (!esActivo || tieneTicketsActivos(eq.id)) {
            estadoTexto = "No disponible"
        }

        if (asignadoA !== "No asignado") {
            estadoTexto = "Prestado"
        }

        const tr = document.createElement("tr")

        const tdId = document.createElement("td")
        tdId.innerText = eq.id

        const tdAsignado = document.createElement("td")
        tdAsignado.innerText = asignadoA

        const tdEstado = document.createElement("td")
        tdEstado.innerText = estadoTexto

        //Se le asigna un color segun el estado, desprende aura y mejora la visualizacion
        if (estadoTexto === "Disponible") tdEstado.className = "text-success fw-bold"
        else if (estadoTexto === "Prestado") tdEstado.className = "text-warning fw-bold"
        else if (estadoTexto === "No disponible") tdEstado.className = "text-danger fw-bold"

        const tdAcciones = document.createElement("td")
        const btnVerIncidencias = document.createElement("button")

        btnVerIncidencias.className = "btn btn-warning btn-sm"
        btnVerIncidencias.innerText = "Ver Incidencias"
        btnVerIncidencias.addEventListener("click", () => {
            window.location.href = `historialGeneral.php?equipoId=${eq.id}`
        })
        tdAcciones.appendChild(btnVerIncidencias)

        tr.appendChild(tdId)
        tr.appendChild(tdEstado)
        tr.appendChild(tdAsignado)
        tr.appendChild(tdAcciones)
        cuerpoTabla.appendChild(tr)


        if (equipoSeleccionado && esActivo && asignadoA === "No asignado" && !tieneTicketsActivos(eq.id)) {
            //Un equipo solo puede ser prestado si esta activo, no fue prestado y no tiene incidencias activas
            const opt = document.createElement("option")
            opt.value = eq.id
            opt.innerText = `PC: ${eq.id}`
            equipoSeleccionado.appendChild(opt)
        }
    })
    //Opcion cuando no hayan equipos disponibles
    if (equipoSeleccionado && equipoSeleccionado.options.length === 0) {
        const opt = document.createElement("option")
        opt.value = ""
        opt.innerText = "Actualmente no hay equipos disponibles"
        equipoSeleccionado.appendChild(opt)
    }
}
}


const abrirModalIncidencia = () => {
    actualizarTabla()
    modalIncidenciaPrestamo.classList.remove("d-none")
    modalIncidenciaPrestamo.classList.add("d-flex")
}

const cerrarModalIncidencia = () => {
    formIncidenciaIndividual.reset()
    modalIncidenciaPrestamo.classList.remove("d-flex")
    modalIncidenciaPrestamo.classList.add("d-none")
}

// EVENTOS
formIncidenciaIndividual.addEventListener("submit", (e) => {
    e.preventDefault()

    const idPC = equipoSeleccionado.value

    if (idPC === "") {
        alert("Error: Debe seleccionar un dispositivo válido para continuar.")
        return
    }

    const todosLosEquipos = cargarEquipos()
    const equipoValidar = todosLosEquipos.find(e => String(e.id) === String(idPC))

    if (equipoValidar && !equipoValidar.activo) {
        alert("Error: El equipo seleccionado se encuentra desactivado.")
        return
    }

    if (obtenerPrestamista(idPC) !== "No asignado") {
        alert("Error: El equipo seleccionado ya se encuentra prestado.")
        return
    }

    if (tieneTicketsActivos(idPC)) {
        alert("Error: El equipo seleccionado posee incidencias abiertas.")
        return
    }

    const tipo = inputTipo.value
    const asunto = inputAsunto.value.trim()
    const persona = inputPersona.value.trim()
    const desc = inputDescripcion.value.trim()

    const gravedadSeleccionada = formIncidenciaIndividual.querySelector('input[name="gravedad"]:checked').value

    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()
    const formatoHora = fechaActual.getHours() + ":" + fechaActual.getMinutes()

    const ticketsGuardados = localStorage.getItem("tickets")
    const tickets = ticketsGuardados ? JSON.parse(ticketsGuardados) : []

    let proximoId = (tickets.length || 0) + 1

    const nuevoTicket = {
        id: proximoId,
        equipoId: idPC,
        tipo: tipo,
        asunto: asunto,
        docente: persona,
        gravedad: gravedadSeleccionada,
        descripcion: desc,
        fechaCreacion: formatoFecha,
        horaCreacion: formatoHora,
        estado: "pendiente",
        salon: "prestamos",
        colaboradores: [],
        comentarios: [],
        justificacion: undefined
    }

    tickets.push(nuevoTicket)
    localStorage.setItem("tickets", JSON.stringify(tickets))

    const detalleHistorial = `El asistente ${nuevoTicket.docente} (${buscarNombre(nuevoTicket.docente)}) registró una incidencia sobre la PC: ${nuevoTicket.equipoId}`

    registrarHistorial(detalleHistorial, nuevoTicket.id)
    

    alert(`Ticket #${proximoId} registrado con éxito.`)
    cerrarModalIncidencia()
    actualizarTabla()
})

btnRegistrarIncidencia.addEventListener("click", abrirModalIncidencia)
btnCancelarModal.addEventListener("click", cerrarModalIncidencia)

actualizarTabla()