// VARIABLES
const metricTotalEquipos = document.getElementById("metric-total-equipos")
const metricEquiposFallas = document.getElementById("metric-equipos-fallas")
const metricUltimoIncidente = document.getElementById("metric-ultimo-incidente")
const topEquipoId = document.getElementById("top-equipo-id")
const topEquipoCantidad = document.getElementById("top-equipo-cantidad")
const contadorVandalismo = document.getElementById("contador-vandalismo")
const tablaRankingFallas = document.getElementById("tabla-ranking-fallas")
const tablaUsuariosActivos = document.getElementById("tabla-usuarios-activos")

const obtenerDatos = (clave) => {
    const datos = localStorage.getItem(clave)
    return datos ? JSON.parse(datos) : []
}

const equipos = obtenerDatos("equipos")
const tickets = obtenerDatos("tickets")

// FUNCIONES
const renderizarUsuariosActivos = () => {
    tablaUsuariosActivos.innerHTML = ""
    const listaUsuarios = obtenerDatos("usuarios")

    const usuariosActivos = listaUsuarios.filter(u => {
        return u.activo === true || 
               String(u.activo).toLowerCase() === "activo" || 
               String(u.activo).toLowerCase() === "true" ||
               String(u.estado).toLowerCase() === "activo"
    })

    if (usuariosActivos.length === 0) {
        const tr = document.createElement("tr")
        const td = document.createElement("td")
        td.setAttribute("colspan", "4")
        td.className = "text-center text-muted py-4 italic"
        td.appendChild(document.createTextNode("No se registran usuarios activos cargados en la plataforma."))
        tr.appendChild(td)
        tablaUsuariosActivos.appendChild(tr)
        return
    }

    usuariosActivos.forEach(u => {
        const tr = document.createElement("tr")

        const tdNombre = document.createElement("td")
        tdNombre.className = "ps-4 fw-bold text-dark"
        const nombreMostrar = u.nombre || u.nombreCompleto || u.usuario || "Usuario Anónimo"
        tdNombre.appendChild(document.createTextNode(nombreMostrar))

        const tdCorreo = document.createElement("td")
        tdCorreo.className = "text-muted"
        tdCorreo.appendChild(document.createTextNode(u.correo || u.email || "Sin correo registrado"))

        const tdRol = document.createElement("td")
        const rolTexto = String(u.rol || "Técnico").toUpperCase()
        tdRol.appendChild(document.createTextNode(rolTexto))

        const tdEstado = document.createElement("td")
        tdEstado.className = "pe-4 text-center"
        const badge = document.createElement("span")
        badge.className = "badge bg-success px-3 py-1.5 rounded-pill"
        badge.appendChild(document.createTextNode("Activo"))
        tdEstado.appendChild(badge)

        tr.appendChild(tdNombre)
        tr.appendChild(tdCorreo)
        tr.appendChild(tdRol)
        tr.appendChild(tdEstado)
        
        tablaUsuariosActivos.appendChild(tr)
    })
}

// EVENTOS
    metricTotalEquipos.textContent = equipos.length

    const equiposConFallasActivas = new Set(
        tickets.filter(t => String(t.estado).toLowerCase() !== "resuelto" && String(t.estado).toLowerCase() !== "cerrado").map(t => t.equipoId)
    )
    metricEquiposFallas.textContent = equiposConFallasActivas.size

    if (tickets.length > 0) {
        const ticketsOrdenadosPorId = [...tickets].sort((a, b) => Number(b.id) - Number(a.id))
        metricUltimoIncidente.textContent = ticketsOrdenadosPorId[0].fechaCreacion || ticketsOrdenadosPorId[0].fecha || "S/D"
    } else {
        metricUltimoIncidente.textContent = "Sin registros"
    }

    const historialFallasPorEquipo = {}
    tickets.forEach(ticket => {
        const idEq = ticket.equipoId
        if (idEq) {
            if (!historialFallasPorEquipo[idEq]) {
                historialFallasPorEquipo[idEq] = {
                    id: idEq,
                    salon: ticket.salon || "General",
                    fallas: 0
                }
            }
            historialFallasPorEquipo[idEq].fallas += 1
        }
    })

    const listaOrdenadaEquipos = Object.values(historialFallasPorEquipo).sort((a, b) => b.fallas - a.fallas)

    if (listaOrdenadaEquipos.length > 0) {
        topEquipoId.textContent = listaOrdenadaEquipos[0].id
        topEquipoCantidad.textContent = listaOrdenadaEquipos[0].fallas
    } else {
        topEquipoId.textContent = "Ninguno"
        topEquipoCantidad.textContent = "0"
    }

    tablaRankingFallas.innerHTML = ""
    if (listaOrdenadaEquipos.length === 0) {
        const filaVacia = document.createElement("tr")
        const celdaVacia = document.createElement("td")
        celdaVacia.setAttribute("colspan", "3")
        celdaVacia.className = "text-center text-muted"
        celdaVacia.appendChild(document.createTextNode("No hay registros de incidencias históricas."))
        filaVacia.appendChild(celdaVacia)
        tablaRankingFallas.appendChild(filaVacia)
    } else {
        listaOrdenadaEquipos.forEach(item => {
            const fila = document.createElement("tr")

            const celdaId = document.createElement("td")
            const fuerte = document.createElement("strong")
            fuerte.appendChild(document.createTextNode(item.id))
            celdaId.appendChild(fuerte)

            const celdaSalon = document.createElement("td")
            celdaSalon.appendChild(document.createTextNode(item.salon))

            const celdaBadge = document.createElement("td")
            const spanBadge = document.createElement("span")
            spanBadge.className = "badge bg-secondary px-2 py-1.5"
            spanBadge.appendChild(document.createTextNode(item.fallas))
            celdaBadge.appendChild(spanBadge)

            fila.appendChild(celdaId)
            fila.appendChild(celdaSalon)
            fila.appendChild(celdaBadge)
            tablaRankingFallas.appendChild(fila)
        })
    }

    const totalCriticos = tickets.filter(t => {
        const grav = String(t.gravedad).toLowerCase()
        return grav === "alta" || grav === "grave" || grav === "critica" || grav === "crítica"
    }).length
    contadorVandalismo.textContent = totalCriticos

    renderizarUsuariosActivos()
