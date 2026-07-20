const contenedorHistorial = document.getElementById("contenedorHistorial")
const tituloHistorial = document.getElementById("tituloHistorial")
const descripcionHistorial = document.getElementById("descripcionHistorial")
const btnVolverHistorial = document.getElementById("btnVolverHistorial")
const filtroFechaInput = document.getElementById("filtroFecha")

const obtenerParametroUrl = (nombre) => {
    const urlParams = new URLSearchParams(window.location.search)
    return urlParams.get(nombre)
}

const obtenerUsuarioLogueado = () => {
    const sesion = localStorage.getItem("usuario")
    if (sesion === null || sesion === undefined || sesion === "") return null
    return JSON.parse(sesion)
}

const buscarNombre = (cedula) => {
    let usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => String(u.usuario) === String(cedula))
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const mapearFecha = (stringFecha) => {
    if (stringFecha === null || stringFecha === undefined || stringFecha === "") return 0
    const partes = stringFecha.split("/")
    if (partes.length !== 3) return 0
    return parseInt(partes[2] + partes[1].padStart(2, "0") + partes[0].padStart(2, "0"))
}

const formatearFecha = (fechaHtml) => {
    if (!fechaHtml) return ""
    const partes = fechaHtml.split("-")
    if (partes.length !== 3) return ""
    return `${partes[2]}/${partes[1]}/${partes[0]}`
}

const cargarTipoRegistros = (tipo) => {
    let clave = ""
    switch (tipo) {
        case "equipos":
            clave = "registroEquipos"
            break
        case "prestamos":
            clave = "registroPrestamos"
            break
        case "salones":
            clave = "registroSalones"
            break
        case "solicitudes":
            clave = "registroSolicitudes"
            break
        case "tickets":
            clave = "registroTickets"
            break
        case "usuarios":
            clave = "registroUsuarios"
            break
    }

    const datos = localStorage.getItem(clave)
    if (datos === null || datos === undefined || datos === "") return []
    return JSON.parse(datos)
}

const validarRol = (tipo, usuario) => {
    if (!usuario) return false
    const rol = usuario.rol ? usuario.rol.toLowerCase() : ""

    switch (tipo) {
        case "prestamos":
        case "equipos":
            return rol === "administrador" || rol === "tecnico"
        default:
            return rol === "administrador"
    }
}

const prepararTitulos = (tipo) => {
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

const cargarDatos = (tipo, registro) => {
    switch (tipo) {
        case "equipos":
            return registro.detalleEquipo 
        
        case "prestamos":
            switch (registro.modificacion) {
                case "prestamo":
                    return `El usuario ${registro.ciPrestador} (${buscarNombre(registro.ciPrestador)}) registró un prestamo del equipo (${registro.idEquipo}) a ${registro.nombrePrestado} (${registro.ciPrestado}).`
                case "devolucion":
                    return `El usuario ${registro.ciPrestador} (${buscarNombre(registro.ciPrestador)}) finalizó el prestamo del equipo (${registro.idEquipo}) a ${registro.nombrePrestado} (${registro.ciPrestado}).`
            }
            break

        case "salones":
            return registro.detalleSalon 

        case "solicitudes":
            switch (registro.modificacion) {
                case "creacion":
                    return `El docente ${registro.usuario} (${buscarNombre(registro.usuario)}) Registró una nueva solicitud.\nID: ${registro.idSolicitud}, Asunto: "${registro.asunto}".`
                case "finalizacion":
                    return `El usuario ${registro.usuario} (${buscarNombre(registro.usuario)}) Finalizó una solicitud.\nID: ${registro.idSolicitud}, Asunto: "${registro.asunto}".`
            }
            break

        case "tickets":
            let tickets = localStorage.getItem("tickets")
            if (tickets === null || tickets === undefined || tickets === "") tickets = []
            else tickets = JSON.parse(tickets)
            const ticketEncontrado = tickets.find(t => String(t.id) === String(registro.idTicket))
            return ticketEncontrado.asunto || "N/A"

        case "usuarios":
            switch (registro.modificacion) {
                case "modificacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) Modifico los datos del usuario ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
                case "activacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) Activo la cuenta del usuario ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
                case "desactivacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) Desactivo la cuenta del usuario ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
                case "creacion":
                    return `El usuario ${registro.ciActor} (${buscarNombre(registro.ciActor)}) Creó la cuenta del usuario ${registro.ciModificado} (${buscarNombre(registro.ciModificado)}).`
            }
            break
    }
}

const renderizarHistorialDinamico = () => {
    contenedorHistorial.innerHTML = ""
    
    const tipoHistorialUrl = obtenerParametroUrl("tipo")
    if (!tipoHistorialUrl) return

    const usuarioActual = obtenerUsuarioLogueado()
    if (!validarRol(tipoHistorialUrl, usuarioActual)) {
        alert("Error: Rol no correspondiente a la acción deseada. Redireccionando.")
        history.back()
        return
    }

    prepararTitulos(tipoHistorialUrl)

    let listaRegistros = cargarTipoRegistros(tipoHistorialUrl)

    let registrosFiltrados = []
    
    switch (tipoHistorialUrl) {
        case "salones":
            const registrosEquiposRaiz = cargarTipoRegistros("equipos")
            registrosEquiposRaiz.forEach(r => {
                if (r.idEquipo === "todos" || (r.detalleEquipo && (r.detalleEquipo.includes("creó el") || r.detalleEquipo.includes("eliminó el")))) {
                    let copiaRegistro = JSON.parse(JSON.stringify(r))
                    copiaRegistro.detalleSalon = r.detalleEquipo
                    copiaRegistro.salonAfectado = r.idEquipo
                    registrosFiltrados.push(copiaRegistro)
                }
            })
            listaRegistros.forEach(r => {
                registrosFiltrados.push(r)
            })
            break
        case "equipos":
            listaRegistros.forEach(r => {
                if (!(r.idEquipo === "todos" || (r.detalleEquipo && (r.detalleEquipo.includes("creó el") || r.detalleEquipo.includes("eliminó el"))))) {
                    registrosFiltrados.push(r)
                }
            })
            break
        case "tickets":
            const equipoIdFiltroUrl = obtenerParametroUrl("equipoId")
            let ticketsBase = localStorage.getItem("tickets")
            if (ticketsBase === null || ticketsBase === undefined || ticketsBase === "") ticketsBase = []
            else ticketsBase = JSON.parse(ticketsBase)
            listaRegistros.forEach(r => {
                if (equipoIdFiltroUrl) {
                    const ticketAsociado = ticketsBase.find(t => String(t.id) === String(r.idTicket))
                    if (ticketAsociado && String(ticketAsociado.equipoInvolucrado).trim() === String(equipoIdFiltroUrl).trim()) {
                        registrosFiltrados.push(r)
                    }
                } else {
                    registrosFiltrados.push(r)
                }
            })
            break
        default:
            listaRegistros.forEach(r => {
                registrosFiltrados.push(r)
            })
            break
    }

    const fechaSeleccionadaHtml = filtroFechaInput.value
    if (fechaSeleccionadaHtml) {
        const fechaFormateadaFiltro = formatearFecha(fechaSeleccionadaHtml)
        let auxiliarFiltro = []
        registrosFiltrados.forEach(r => {
            if (r.fecha === fechaFormateadaFiltro) {
                auxiliarFiltro.push(r)
            }
        })
        registrosFiltrados = auxiliarFiltro
    }

    registrosFiltrados.sort((a, b) => {
        const fechaB = mapearFecha(b.fecha)
        const fechaA = mapearFecha(a.fecha)
        if (fechaB !== fechaA) {
            return fechaB - fechaA
        }
        
        const horaB = b.hora || "00:00:00"
        const horaA = a.hora || "00:00:00"
        return horaB.localeCompare(horaA)
    })

    let fechaActualGrupo = ""
    let listaActualUl = null

    registrosFiltrados.forEach(registro => {
        if (fechaActualGrupo !== registro.fecha) {
            fechaActualGrupo = registro.fecha

            const spanFecha = document.createElement("span")
            spanFecha.className = "fw-bold d-block mt-3 text-secondary"
            spanFecha.innerText = `Intervenciones del ${fechaActualGrupo}`
            contenedorHistorial.appendChild(spanFecha)

            listaActualUl = document.createElement("ul")
            listaActualUl.className = "historial-lista mt-2 mb-3 list-unstyled"
            contenedorHistorial.appendChild(listaActualUl)
        }

        const li = document.createElement("li")
        li.className = "historial-contenido d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded shadow-sm"

        const columna = document.createElement("div")
        columna.className = "d-flex flex-column"

        const spanDescripcion = document.createElement("span")
        spanDescripcion.className = "fw-bold text-dark"
        
        let textoPrincipal = cargarDatos(tipoHistorialUrl, registro)
        if (tipoHistorialUrl === "tickets") {
            let tickets = localStorage.getItem("tickets")
            if (tickets === null || tickets === undefined || tickets === "") tickets = []
            else tickets = JSON.parse(tickets)
            const ticketEncontrado = tickets.find(t => String(t.id) === String(registro.idTicket))
            if (ticketEncontrado && ticketEncontrado.salonInvolucrado) {
                let salonLimpio = ticketEncontrado.salonInvolucrado
                if (salonLimpio.includes(" ")) {
                    salonLimpio = salonLimpio.replace(" ", "-")
                }
                textoPrincipal = `${textoPrincipal} (${salonLimpio})`
            }
        }
        spanDescripcion.innerText = textoPrincipal

        const detalles = document.createElement("span")
        detalles.className = "text-muted small"
        detalles.innerText = registro.detalleTicket || registro.hora || ""

        columna.appendChild(spanDescripcion)
        if (registro.detalleTicket) {
            columna.appendChild(detalles)
        }

        const spanHoraRegistro = document.createElement("span")
        spanHoraRegistro.className = "text-muted small"
        spanHoraRegistro.innerText = registro.hora || ""

        li.appendChild(columna)
        li.appendChild(spanHoraRegistro)

        listaActualUl.appendChild(li)
    })

    if (registrosFiltrados.length === 0) {
        const sinResultados = document.createElement("div")
        sinResultados.className = "alert alert-light text-center border mt-3 text-muted"
        sinResultados.innerText = "No se localizaron registros para los criterios especificados."
        contenedorHistorial.appendChild(sinResultados)
    }
}

filtroFechaInput.addEventListener("change", renderizarHistorialDinamico)

btnVolverHistorial.addEventListener("click", (e) => {
    history.back()
})

renderizarHistorialDinamico()