// VARIABLES
let chartEstadosInstance = null
let chartIncidenciasInstance = null

const filtroUbicacion = document.getElementById("filtroUbicacion")
const filtroEstado = document.getElementById("filtroEstado")
const filtroIncidencias = document.getElementById("filtroIncidencias")
const filtroIntervencion = document.getElementById("filtroIntervencion")
const cuerpoTabla = document.querySelector("#tablaEquipos tbody")

// FUNCIONES
const obtenerSalones = () => {
    const datos = localStorage.getItem("salones")
    return datos ? JSON.parse(datos) : []
}

const obtenerEquipos = () => {
    const datos = localStorage.getItem("equipos")
    return datos ? JSON.parse(datos) : []
}

const calcularIncidencias = (idEquipo) => {
    const datos = localStorage.getItem("tickets")
    if (!datos) return 0
    const lista = JSON.parse(datos)
    const buscado = String(idEquipo).trim().toLowerCase()
    
    return lista.filter(t => {
        const idTicket = t.equipoId || t.idEquipo || t.codigoEquipo || ""
        const limpio = String(idTicket).trim().toLowerCase()
        return limpio === buscado || limpio === "pc-" + buscado
    }).length
}

const encontrarUbicacion = (idEquipo) => {
    const salones = obtenerSalones()
    const buscado = String(idEquipo).trim().toLowerCase()

    for (let s of salones) {
        if (s.espacios && Array.isArray(s.espacios)) {
            for (let j = 0; j < s.espacios.length; j++) {
                const item = s.espacios[j]
                let idEspacio = item && typeof item === 'object' ? (item.id || item.codigo || "") : (item || "")
                
                if (String(idEspacio).trim().toLowerCase() === buscado) {
                    if (s.tipo === "laboratorios") return { nombre: "Laboratorio " + s.id, posicion: j + 1, modo: "salon" }
                    if (s.tipo === "talleres") return { nombre: "Taller " + s.id, posicion: j + 1, modo: "salon" }
                    if (s.tipo === "prestamo") return { nombre: "prestamo", posicion: 0, modo: "prestamo", prestado: item.prestado }
                }
            }
        }
    }
    return { nombre: "ninguna", posicion: 0, modo: "ninguna" }
}

const poblarFiltroUbicaciones = () => {
    filtroUbicacion.innerHTML = ""
    
    const optTodos = document.createElement("option")
    optTodos.value = "todos"
    optTodos.appendChild(document.createTextNode("Todas las ubicaciones"))
    
    const optPrestamo = document.createElement("option")
    optPrestamo.value = "prestamo"
    optPrestamo.appendChild(document.createTextNode("Dispositivos para prestar"))
    
    const optNinguna = document.createElement("option")
    optNinguna.value = "ninguna"
    optNinguna.appendChild(document.createTextNode("Sin asignar / No ingresados"))
    
    filtroUbicacion.appendChild(optTodos)
    filtroUbicacion.appendChild(optPrestamo)
    filtroUbicacion.appendChild(optNinguna)

    const salones = obtenerSalones()
    salones.forEach(s => {
        if (s.tipo === "laboratorios" || s.tipo === "talleres") {
            const opt = document.createElement("option")
            const texto = (s.tipo === "laboratorios" ? "Laboratorio " : "Taller ") + s.id
            opt.value = texto
            opt.appendChild(document.createTextNode(texto))
            filtroUbicacion.appendChild(opt)
        }
    })
}

const convertirFechaAEntero = (fTexto) => {
    if (!fTexto) return 0
    const p = String(fTexto).split("/")
    if (p.length !== 3) return 0
    return parseInt(p[2] + p[1].padStart(2, "0") + p[0].padStart(2, "0"))
}

const renderizarGraficas = (activos, inactivos, labelsB, dataB) => {
    if (chartEstadosInstance) chartEstadosInstance.destroy()
    const ctx1 = document.getElementById("graficaEstados").getContext("2d")
    chartEstadosInstance = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Operativos', 'Inactivos'],
            datasets: [{
                label: 'Cantidad de Equipos',
                data: [activos, inactivos],
                backgroundColor: ['rgba(25, 135, 84, 0.6)', 'rgba(220, 53, 69, 0.6)'],
                borderColor: ['#198754', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    })

    if (chartIncidenciasInstance) chartIncidenciasInstance.destroy()
    const ctx2 = document.getElementById("graficaEstados1").getContext("2d")
    chartIncidenciasInstance = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labelsB.length > 0 ? labelsB : ["Sin Equipos"],
            datasets: [{
                label: 'Número de Falla(s)',
                data: dataB.length > 0 ? dataB : [0],
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: '#0d6efd',
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    })
}

const procesarYRenderizar = () => {
    cuerpoTabla.innerHTML = ""
    
    const lista = obtenerEquipos()
    let filtrados = []
    const uSel = filtroUbicacion.value

    lista.forEach(eq => {
        const idReal = eq.codigo || eq.id
        const ubic = encontrarUbicacion(idReal)
        if (uSel === "todos" || ubic.nombre === uSel) {
            filtrados.push(eq)
        }
    })

    if (filtroEstado.value !== "") {
        const activoBuscado = filtroEstado.value === "activo"
        filtrados = filtrados.filter(eq => {
            const esActivo = eq.activo === true || String(eq.activo).toLowerCase() === "activo" || String(eq.activo).toLowerCase() === "true" || String(eq.estado).toLowerCase() === "activo"
            return esActivo === activoBuscado
        })
    }

    if (filtroIncidencias.value !== "") {
        filtrados.sort((a, b) => {
            const incA = calcularIncidencias(a.codigo || a.id)
            const incB = calcularIncidencias(b.codigo || b.id)
            return filtroIncidencias.value === "menor" ? incA - incB : incB - incA
        })
    }

    if (filtroIntervencion.value !== "") {
        filtrados.sort((a, b) => {
            const fA = convertirFechaAEntero(a.ultimaIntervencion || a.fecha)
            const fB = convertirFechaAEntero(b.ultimaIntervencion || b.fecha)
            return filtroIntervencion.value === "reciente" ? fB - fA : fA - fB
        })
    }

    let activos = 0
    let inactivos = 0
    const labelsG = []
    const dataG = []

    filtrados.forEach(eq => {
        const idReal = eq.codigo || eq.id
        const ubic = encontrarUbicacion(idReal)
        const incs = calcularIncidencias(idReal)
        const esActivo = eq.activo === true || String(eq.activo).toLowerCase() === "activo" || String(eq.activo).toLowerCase() === "true" || String(eq.estado).toLowerCase() === "activo"

        esActivo ? activos++ : inactivos++
        labelsG.push("PC-" + idReal)
        dataG.push(incs)

        const tr = document.createElement("tr")

        const tdCodigo = document.createElement("td")
        tdCodigo.appendChild(document.createTextNode(idReal))

        const tdEstado = document.createElement("td")
        tdEstado.appendChild(document.createTextNode(esActivo ? "Activo" : "Inactivo"))

        const tdTipoAsignacion = document.createElement("td")
        let textoAsignacion = "Sin asignar"
        if (ubic.modo === "prestamo") {
            textoAsignacion = ubic.prestado ? "Prestado" : "Disponible"
        } else if (ubic.modo === "salon") {
            textoAsignacion = "En salón"
        }
        tdTipoAsignacion.appendChild(document.createTextNode(textoAsignacion))

        const tdDetalleUbicacion = document.createElement("td")
        const textoDetalle = ubic.modo === "salon" ? `${ubic.nombre} - Banco ${ubic.posicion}` : "N/A"
        tdDetalleUbicacion.appendChild(document.createTextNode(textoDetalle))

        const tdFecha = document.createElement("td")
        tdFecha.appendChild(document.createTextNode(eq.ultimaIntervencion || eq.fecha || "Sin registros"))

        const tdIncidencias = document.createElement("td")
        tdIncidencias.appendChild(document.createTextNode(incs))

        tr.appendChild(tdCodigo)
        tr.appendChild(tdEstado)
        tr.appendChild(tdTipoAsignacion)
        tr.appendChild(tdDetalleUbicacion)
        tr.appendChild(tdFecha)
        tr.appendChild(tdIncidencias)

        cuerpoTabla.appendChild(tr)
    })

    renderizarGraficas(activos, inactivos, labelsG, dataG)
}

// EVENTOS
    poblarFiltroUbicaciones()
    procesarYRenderizar()
    
    filtroUbicacion.addEventListener("change", procesarYRenderizar)
    filtroEstado.addEventListener("change", procesarYRenderizar)
    filtroIncidencias.addEventListener("change", procesarYRenderizar)
    filtroIntervencion.addEventListener("change", procesarYRenderizar)
