// VARIABLES
const metricaTotalEquipos = document.getElementById("cantTotalEquipos") //Las variables de los objetos "dinamicos" si existen en el html, y son citadas por id ya que no hay necesidad de crear objetos nuevos, solo se debe modificar los objetos actuales
const metricaEquiposFallas = document.getElementById("cantEquiposFallados")
const metricaUltimoIncidente = document.getElementById("fechaUltimoIncidente")
const topEquipoId = document.getElementById("equipoMasFallado")
const topEquipoCantidad = document.getElementById("cantEquipoMasFallado")
const contadorVandalismo = document.getElementById("cantIncidencias")
const tablaRankingFallas = document.getElementById("tablaFallas")
const tablaUsuarios = document.getElementById("tablaUsuarios")

// FUNCIONES
const obtenerDatos = (clave) => {
    const datos = localStorage.getItem(clave)
    if (datos === null || datos === undefined || datos === "") return []
    return JSON.parse(datos)
}

const actualizarPagina = () => {
    const equipos = obtenerDatos("equipos")
    const tickets = obtenerDatos("tickets")

    metricaTotalEquipos.innerText = equipos.length

    const equiposConFallasActivas = new Set(tickets.filter(t => t.estado.toLowerCase() !== "resuelto").map(t => t.equipoId))  //Un set es como un array, pero los conjuntos obligatoriamente no se repiten, tambien cuenta con metodos unicos
    metricaEquiposFallas.innerText = equiposConFallasActivas.size

    if (tickets.length > 0) {
        const ticketsOrdenadosPorId = [...tickets].sort((a, b) => b.id - a.id) // El [...tickets] es una funcion que agarra los elementos restantes en el set, al ponerlo asi nada mas, lee como que el restante es todo el set, por lo que, en resumen, agarra tooodo el set
        metricaUltimoIncidente.innerHTML = ticketsOrdenadosPorId[0].fechaCreacion || ticketsOrdenadosPorId[0].fecha || "N/A"
    } else {
        metricaUltimoIncidente.innerText = "Sin registros"
    }

    const historialFallasPorEquipo = {}

    tickets.forEach(ticket => {
        const idEq = ticket.equipoId
        if (idEq === null || idEq === undefined || idEq === "") return null // Si no hay id de equipo, salta el ticket

        if (!historialFallasPorEquipo[idEq]) {
            historialFallasPorEquipo[idEq] = {
                id: idEq,
                salon: ticket.salon || 'N/A',
                fallas: 0
            }
        }
        historialFallasPorEquipo[idEq].fallas += 1
    })

    const listaOrdenadaEquipos = Object.values(historialFallasPorEquipo).sort((a, b) => b.fallas - a.fallas) //Obtiene el valor dentro del objeto

    if (listaOrdenadaEquipos.length > 0) {
        topEquipoId.innerText = listaOrdenadaEquipos[0].id
        topEquipoCantidad.innerText = listaOrdenadaEquipos[0].fallas
    } else {
        topEquipoId.innerText = "Ninguno"
        topEquipoCantidad.innerText = "0"
    }

    tablaRankingFallas.innerHTML = ""

        if (listaOrdenadaEquipos.length === 0) {
        const filaSinResultados = document.createElement("tr")
        const celdaSinResultados = document.createElement("td")

        celdaSinResultados.colSpan = 3 // colSpan es para que ocupe todas las columnas, porque sino queda solo en la primera y se ve re gagá

        celdaSinResultados.className = "text-center py-4 text-muted bg-light fw-semibold"
        celdaSinResultados.innerText = "No se encontraron Equipos."

        filaSinResultados.appendChild(celdaSinResultados)
        tablaRankingFallas.appendChild(filaSinResultados)

    } else {

    listaOrdenadaEquipos.forEach(item => {
        const fila = document.createElement("tr")

        const celdaId = document.createElement("td")
        const fuerte = document.createElement("strong")
        fuerte.innerText = item.id
        celdaId.appendChild(fuerte)

        const celdaSalon = document.createElement("td")
        celdaSalon.innerText = item.salon

        const celdaBadge = document.createElement("td")
        const spanBadge = document.createElement("span")
        spanBadge.className = "badge bg-secondary px-2 py-1.5"
        spanBadge.innerText = item.fallas
        celdaBadge.appendChild(spanBadge)

        fila.appendChild(celdaId)
        fila.appendChild(celdaSalon)
        fila.appendChild(celdaBadge)
        tablaRankingFallas.appendChild(fila)
    
    })
    }

    const totalCriticos = tickets.filter(t => {
        const grav = t.gravedad.toLowerCase()
        return grav === "baja" || grav === "media" || grav === "alta"
    }).length
    contadorVandalismo.innerText = totalCriticos

    tablaUsuarios.innerHTML = ""
    const listaUsuarios = obtenerDatos("usuarios")

    const usuariosRegistrados = listaUsuarios.sort(u => {
        return u.activo
    })

    usuariosRegistrados.forEach(u => {
        const tr = document.createElement("tr")

        const tdNombre = document.createElement("td")
        tdNombre.className = "ps-4 fw-bold text-dark"
        tdNombre.innerText = u.nombre

        const tdCorreo = document.createElement("td")
        tdCorreo.className = "text-muted"
        tdCorreo.innerText = u.correo

        const tdRol = document.createElement("td")
        tdRol.innerText = u.rol

        const tdEstado = document.createElement("td")
        tdEstado.className = "pe-4 text-center"

        const spanActivo = document.createElement("span")
        let estiloCampoActivo = ""
        if (u.activo) estiloCampoActivo = "badge bg-success px-3 py-1.5 rounded-pill"
        else estiloCampoActivo = "badge bg-danger px-3 py-1.5 rounded-pill"

        spanActivo.className = estiloCampoActivo
        spanActivo.innerText = u.activo ? "Activo" : "Inactivo"
        tdEstado.appendChild(spanActivo)

        tr.appendChild(tdNombre)
        tr.appendChild(tdCorreo)
        tr.appendChild(tdRol)
        tr.appendChild(tdEstado)

        tablaUsuarios.appendChild(tr)
    })
}

actualizarPagina()
