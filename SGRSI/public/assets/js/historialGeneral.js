const contenedorHistorial = document.getElementById("contenedorHistorial")
const tituloHistorial = document.getElementById("tituloHistorial")
const descripcionHistorial = document.getElementById("descripcionHistorial")
const btnVolverHistorial = document.getElementById("btnVolverHistorial")
const filtroFechaInput = document.getElementById("filtroFecha")

const obtenerParametroUrl = (nombre) => {
    const urlParams = new URLSearchParams(window.location.search)
    return urlParams.get(nombre)
}

const buscarNombre = (cedula) => {
    let usuarios = localStorage.getItem("usuarios")
    if (!usuarios) usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => String(u.usuario) === String(cedula))
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const mapearFecha = (stringFecha) => {
    if (!stringFecha) return 0
    const partes = stringFecha.split("/")
    if (partes.length !== 3) return 0
    return parseInt(partes[2] + partes[1].padStart(2, "0") + partes[0].padStart(2, "0"))
}

const formatearFecha = (fecha) => {
    if (!fecha) return "N/A"
    const partes = fecha.split("-")
    if (partes.length !== 3) return ""
    return `${partes[2]}/${partes[1]}/${partes[0]}`
}

const cargarRegistrosPorTipo = (tipo) => {
    let clave = ""
    switch (tipo) {
        case "equipos": clave = "registroEquipos"; break
        case "prestamos": clave = "registroPrestamos"; break
        case "salones": clave = "registroSalones"; break
        case "solicitudes": clave = "registroSolicitudes"; break
        case "tickets": clave = "registroTickets"; break
        case "usuarios": clave = "registroUsuarios"; break
    }

    const datos = localStorage.getItem(clave)
    if (!datos) return []
    return JSON.parse(datos)
}

const adaptarVentana = (tipo) => {
    if (!tituloHistorial || !descripcionHistorial) return

    switch (tipo) {
        case "equipos":
            tituloHistorial.innerText = "Historial de Equipos"
            descripcionHistorial.innerText = "Hojas de ruta e intervenciones técnicas aplicadas sobre el inventario"
            break
        case "prestamos":
            tituloHistorial.innerText = "Historial de Préstamos"
            descripcionHistorial.innerText = "Hoja de ruta y cambios cronológicos registrados para los préstamos"
            break
        case "salones":
            tituloHistorial.innerText = "Historial de Salones"
            descripcionHistorial.innerText = "Registro cronológico de creación y eliminación de infraestructura"
            break
        case "solicitudes":
            tituloHistorial.innerText = "Historial de Solicitudes"
            descripcionHistorial.innerText = "Hoja de ruta y registros cronológicos con respecto a la información de las solicitudes"
            break
        case "tickets":
            tituloHistorial.innerText = "Historial de Tickets"
            descripcionHistorial.innerText = "Trazabilidad, asignaciones e intervenciones sobre incidencias reportadas"
            break
        case "usuarios":
            tituloHistorial.innerText = "Historial de Usuarios"
            descripcionHistorial.innerText = "Auditoría de altas, bajas y modificaciones de cuentas del sistema"
            break
    }
}

const obtenerMensajeRegistro = (tipo, registro) => {
    switch (tipo) {
        case "equipos":
            return registro.detalleEquipo || `N/A`

        case "prestamos":
            if (registro.modificacion === "prestamo") {
                return `El usuario ${registro.ciPrestador} (${buscarNombre(registro.ciPrestador)}) registró un préstamo del equipo (${registro.idEquipo}) a ${registro.nombrePrestado} (${registro.ciPrestado}).`
            } else if (registro.modificacion === "devolucion") {
                return `El usuario ${registro.ciPrestador} (${buscarNombre(registro.ciPrestador)}) finalizó el préstamo del equipo (${registro.idEquipo}) a ${registro.nombrePrestado} (${registro.ciPrestado}).`
            }
            return "N/A"

        case "salones":
            return registro.detalleSalon || `Modificación en salón ${registro.salonAfectado}`

        case "solicitudes":
            if (registro.modificacion === "creacion") {
                return `El docente ${registro.usuario} (${buscarNombre(registro.usuario)}) registró una nueva solicitud: "${registro.asunto}".`
            } else if (registro.modificacion === "finalizacion") {
                return `El usuario ${registro.usuario} (${buscarNombre(registro.usuario)}) finalizó la solicitud: "${registro.asunto}".`
            }
            return "N/A"

        case "tickets":
            if (registro.asuntoTicket) return registro.asuntoTicket
            let tickets = localStorage.getItem("tickets")
            tickets = tickets ? JSON.parse(tickets) : []
            const ticketEncontrado = tickets.find(t => String(t.id) === String(registro.idTicket))
            return ticketEncontrado ? ticketEncontrado.asunto : "N/A"

        case "usuarios":
            switch (registro.modificacion) {
                case "modificacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) modificó los datos del usuario ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
                case "activacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) activó la cuenta de ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
                case "desactivacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) desactivó la cuenta de ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
                case "creacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) creó la cuenta de ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
            }
            return "N/A"
    }
}

const renderizarHistorialDinamico = () => {
    if (!contenedorHistorial) return
    contenedorHistorial.innerHTML = ""

    let tipoHistorialUrl = obtenerParametroUrl("tipo")
    const equipoIdFiltroUrl = obtenerParametroUrl("equipoId")

    if (equipoIdFiltroUrl) {
        tipoHistorialUrl = "tickets"
    } else if (!tipoHistorialUrl) {
        alert("Error: parámetros en URL faltantes, regresando...")
        history.back()
    }

    adaptarVentana(tipoHistorialUrl)

    let registros = cargarRegistrosPorTipo(tipoHistorialUrl)
    let registrosFiltrados = []

    if (equipoIdFiltroUrl) {
        registros.forEach(r => {
            if (String(r.equipoInvolucrado) === String(equipoIdFiltroUrl)) {
                registrosFiltrados.push(r)
            }
        })

        let registroTickets = localStorage.getItem("registroTickets")
        let tickets = localStorage.getItem("tickets")

        if (registroTickets && tickets) {
            registroTickets = JSON.parse(registroTickets)
            tickets = JSON.parse(tickets)

            registroTickets.forEach(rt => {
                const tAsociado = tickets.find(t => String(t.id) === String(rt.idTicket))
                if (tAsociado && (String(tAsociado.equipoId) === String(equipoIdFiltroUrl) || String(tAsociado.equipoInvolucrado) === String(equipoIdFiltroUrl))) {
                    registrosFiltrados.push(rt)
                }
            })
        }
    } else {
        registrosFiltrados = [...registros]
    }

    if (filtroFechaInput && filtroFechaInput.value) {
        const fechaFiltro = formatearFecha(filtroFechaInput.value)
        registrosFiltrados = registrosFiltrados.filter(r => r.fecha === fechaFiltro)
    }

    registrosFiltrados.sort((a, b) => {
        const fechaB = mapearFecha(b.fecha)
        const fechaA = mapearFecha(a.fecha)
        if (fechaB !== fechaA) return fechaB - fechaA
        return (b.hora || "00:00").localeCompare(a.hora || "00:00") //Si la fecha esta igual, se fija que hora es mayor que otra
    })

    let fechaGrupo = ""
    let ulActual = null

    registrosFiltrados.forEach(r => {
        if (fechaGrupo !== r.fecha) {
            fechaGrupo = r.fecha

            const spanFecha = document.createElement("span")
            spanFecha.className = "fw-bold d-block mt-3 text-secondary"
            spanFecha.innerText = `Intervenciones del ${fechaGrupo}`
            contenedorHistorial.appendChild(spanFecha)

            ulActual = document.createElement("ul")
            ulActual.className = "historial-lista mt-2 mb-3 list-unstyled"
            contenedorHistorial.appendChild(ulActual)
        }

        const li = document.createElement("li")
        li.className = "historial-contenido d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded shadow-sm"

        const colIzquierda = document.createElement("div")
        colIzquierda.className = "d-flex flex-column"

        const spanTexto = document.createElement("span")
        spanTexto.className = "fw-bold text-dark"
        spanTexto.innerText = obtenerMensajeRegistro(tipoHistorialUrl, r)
        colIzquierda.appendChild(spanTexto)

        const detalleTexto = r.detalleTicket || r.detalleEquipo
        if (detalleTexto) {
            const spanDetalle = document.createElement("span")
            spanDetalle.className = "text-muted small"
            spanDetalle.innerText = detalleTexto
            colIzquierda.appendChild(spanDetalle)
        }

        const spanHora = document.createElement("span")
        spanHora.className = "text-muted small ms-3 fw-semibold"
        spanHora.innerText = r.hora || ""

        li.appendChild(colIzquierda)
        li.appendChild(spanHora)

        ulActual.appendChild(li)
    })

    if (registrosFiltrados.length === 0) {
        const sinResultados = document.createElement("div")
        sinResultados.className = "alert alert-light text-center border mt-3 text-muted"
        sinResultados.innerText = "No se localizaron registros para los criterios especificados."
        contenedorHistorial.appendChild(sinResultados)
    }
}

if (filtroFechaInput) {
    filtroFechaInput.addEventListener("change", renderizarHistorialDinamico)
}

if (btnVolverHistorial) {
    btnVolverHistorial.addEventListener("click", () => history.back())
}

renderizarHistorialDinamico()