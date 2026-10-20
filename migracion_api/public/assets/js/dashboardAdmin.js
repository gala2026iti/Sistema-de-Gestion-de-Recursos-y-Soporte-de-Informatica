const API_METRICAS = "../index.php/metricas/resumen";

const cantTicketsAbiertos =
    document.getElementById("cantTicketsAbiertos");

const cantTicketsCerrados =
    document.getElementById("cantTicketsCerrados");

const cantEquiposActivos =
    document.getElementById("cantEquiposActivos");

const cantEquiposInactivos =
    document.getElementById("cantEquiposInactivos");

const cantSolicitudesPendientes =
    document.getElementById("cantSolicitudesPendientes");

const cantPrestamosActivos =
    document.getElementById("cantPrestamosActivos");

const graficaTicketsSector =
    document.getElementById("graficaTicketsSector");


document.addEventListener("DOMContentLoaded", () => {
    cargarMetricas();
});


async function cargarMetricas()
{
    try {

        const respuesta = await fetch(API_METRICAS);

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(
                datos.mensaje ??
                "No se pudieron cargar las métricas."
            );
        }

        mostrarResumen(datos.datos.resumen);

        mostrarGraficaTickets(
            datos.datos.ticketsPorTipo
        );

    } catch (error) {

        console.error(
            "Error al cargar las métricas:",
            error
        );
    }
}


function mostrarResumen(resumen)
{
    cantTicketsAbiertos.textContent =
        resumen.ticketsAbiertos;

    cantTicketsCerrados.textContent =
        resumen.ticketsCerrados;

    cantEquiposActivos.textContent =
        resumen.equiposActivos;

    cantEquiposInactivos.textContent =
        resumen.equiposInactivos;

    cantSolicitudesPendientes.textContent =
        resumen.solicitudesPendientes;

    cantPrestamosActivos.textContent =
        resumen.prestamosActivos;
}


function mostrarGraficaTickets(tickets)
{
    const tipos = tickets.map(
        ticket => ticket.tipo
    );

    const cantidades = tickets.map(
        ticket => ticket.cantidad
    );

    new Chart(graficaTicketsSector, {
        type: "bar",

        data: {
            labels: tipos,

            datasets: [
                {
                    label: "Cantidad de tickets",
                    data: cantidades
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                        stepSize: 1
                    }
                }
            }
        }
    });
}