// VARIABLES
const txtTicketsAbiertos = document.getElementById("cantTicketsAbiertos")
const txtTicketsCerrados = document.getElementById("cantTicketsCerrados")
const txtEquiposActivos = document.getElementById("cantEquiposActivos")
const txtEquiposInactivos = document.getElementById("cantEquiposInactivos")
const txtSolicitudesPendientes = document.getElementById("cantSolicitudesPendientes")
const txtPrestamosActivos = document.getElementById("cantPrestamosActivos")

// FUNCIONES
const obtenerDatos = (dato) => {
    const datos = localStorage.getItem(dato)
    if(datos === null || datos === undefined || datos === "") return []
    return JSON.parse(datos)
}

const actualizarDatos = () => {
    const tickets = obtenerDatos("tickets")
    const equipos = obtenerDatos("equipos")
    const prestamos = obtenerDatos("prestamos")

    const abiertos = tickets.filter(t => t.estado === "pendiente").length
    const cerrados = tickets.filter(t => t.estado === "resuelto").length
    
    if (!(txtTicketsAbiertos === null || txtTicketsAbiertos === undefined || txtTicketsAbiertos === "")) txtTicketsAbiertos.innerText = abiertos
    if (!(txtTicketsCerrados === null || txtTicketsCerrados === undefined || txtTicketsCerrados === "")) txtTicketsCerrados.innerText = cerrados

    const activos = equipos.filter(e => e.activo === true).length
    const inactivos = equipos.filter(e => e.activo === false).length
    
    if (!(txtEquiposActivos === null || txtEquiposActivos === undefined || txtEquiposActivos === "")) txtEquiposActivos.innerText = activos
    if (!(txtEquiposInactivos === null || txtEquiposInactivos === undefined || txtEquiposInactivos === "")) txtEquiposInactivos.innerText = inactivos

    const prestados = prestamos.filter(p => p.devuelto === false).length
    if (!(txtPrestamosActivos === null || txtPrestamosActivos === undefined || txtPrestamosActivos === "")) txtPrestamosActivos.innerText = prestados

    let labCount = 0
    let prestamoCount = 0

    tickets.forEach(t => {
        const salon =  t.salon.toLowerCase()
        if (salon.includes("laboratorio") || salon.includes("taller")) {
            labCount++
        } else {
            prestamoCount++
        }
    })

    const canvasSectores = document.getElementById("graficaTicketsSector")
    if (!(canvasSectores === null || canvasSectores === undefined || canvasSectores === "")) {
        const ctxSectores = canvasSectores.getContext("2d")
        new Chart(ctxSectores, {
            type: 'doughnut', //Es la grafica circular o de dona
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

    actualizarDatos()
