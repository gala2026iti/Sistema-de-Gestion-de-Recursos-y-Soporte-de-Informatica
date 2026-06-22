// VARIABLES
const txtTicketsAbiertos = document.getElementById("dash-tickets-abiertos")
const txtTicketsCerrados = document.getElementById("dash-tickets-cerrados")
const txtEquiposActivos = document.getElementById("dash-equipos-activos")
const txtEquiposInactivos = document.getElementById("dash-equipos-inactivos")
const txtSolicitudesPendientes = document.getElementById("dash-solicitudes-pendientes")
const txtPrestamosActivos = document.getElementById("dash-prestamos-activos")

// FUNCIONES
const obtenerItems = (key) => {
    const res = localStorage.getItem(key)
    return res ? JSON.parse(res) : []
}

const renderizarDashboard = () => {
    const tickets = obtenerItems("tickets")
    const equipos = obtenerItems("equipos")
    const prestamos = obtenerItems("prestamos")
    const solicitudes = obtenerItems("solicitudes")

    // --- 1. PROCESAMIENTO Y ASIGNACIÓN DE MÉTRICAS ---
    const abiertos = tickets.filter(t => t.estado === "abierto" || t.estado === "pendiente").length
    const cerrados = tickets.filter(t => t.estado === "resuelto" || t.estado === "cerrado").length
    
    if (txtTicketsAbiertos) txtTicketsAbiertos.textContent = abiertos
    if (txtTicketsCerrados) txtTicketsCerrados.textContent = cerrados

    const activos = equipos.filter(e => e.activo === true).length
    const inactivos = equipos.filter(e => e.activo === false).length
    
    if (txtEquiposActivos) txtEquiposActivos.textContent = activos
    if (txtEquiposInactivos) txtEquiposInactivos.textContent = inactivos

    const prestados = prestamos.filter(p => p.devuelto === false).length
    if (txtPrestamosActivos) txtPrestamosActivos.textContent = prestados

    const totalSolicitudes = solicitudes.length > 0 ? solicitudes.filter(s => s.estado === "pendiente").length : 7
    if (txtSolicitudesPendientes) txtSolicitudesPendientes.textContent = totalSolicitudes

    // --- 2. GENERACIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS) ---
    let conteoDificultad = { "Baja": 0, "Media": 0, "Alta": 0 }
    
    if (solicitudes.length > 0) {
        solicitudes.forEach(s => {
            if (s.estado === "pendiente") {
                const dif = s.dificultad || s.prioridad || "Media"
                if (conteoDificultad[dif] !== undefined) conteoDificultad[dif]++
            }
        })
    } else {
        conteoDificultad = { "Baja": 2, "Media": 4, "Alta": 1 }
    }

    const canvasSolicitudes = document.getElementById("graficaSolicitudesDificultad")
    if (canvasSolicitudes) {
        const ctxSolicitudes = canvasSolicitudes.getContext("2d")
        new Chart(ctxSolicitudes, {
            type: 'bar',
            data: {
                labels: ['Baja / Simple', 'Media / Estándar', 'Alta / Crítica'],
                datasets: [{
                    label: 'Cantidad de Solicitudes',
                    data: [conteoDificultad["Baja"], conteoDificultad["Media"], conteoDificultad["Alta"]],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 1.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        })
    }

    let labCount = 0
    let prestamoCount = 0

    tickets.forEach(t => {
        const sector = String(t.sector || t.salon).toLowerCase()
        if (sector.includes("lab") || sector.includes("taller")) {
            labCount++
        } else {
            prestamoCount++
        }
    })

    if (tickets.length === 0) { 
        labCount = 6
        prestamoCount = 3 
    }

    const canvasSectores = document.getElementById("graficaTicketsSector")
    if (canvasSectores) {
        const ctxSectores = canvasSectores.getContext("2d")
        new Chart(ctxSectores, {
            type: 'doughnut',
            data: {
                labels: ['Laboratorios / Talleres', 'Equipos de Préstamo'],
                datasets: [{
                    data: [labCount, prestamoCount],
                    backgroundColor: ['#0d6efd', '#fd7e14'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        })
    }
}

    renderizarDashboard()
