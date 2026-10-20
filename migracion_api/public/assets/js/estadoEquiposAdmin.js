const API_EQUIPOS =
    "../index.php/equipos";

const API_TICKETS =
    "../index.php/tickets";


const tablaEquipos =
    document.querySelector("#tablaEquipos tbody");

const filtroUbicacion =
    document.getElementById("filtroUbicacion");

const filtroEstado =
    document.getElementById("filtroEstado");

const filtroIncidencias =
    document.getElementById("filtroIncidencias");

const filtroIntervencion =
    document.getElementById("filtroIntervencion");


const graficaEstados =
    document.getElementById("graficaEstados");

const graficaIncidencias =
    document.getElementById("graficaIncidencias");


let equipos = [];
let tickets = [];

let graficoEstados = null;
let graficoIncidencias = null;


document.addEventListener("DOMContentLoaded", () => {
    cargarDatos();
});


filtroUbicacion.addEventListener("change", aplicarFiltros);
filtroEstado.addEventListener("change", aplicarFiltros);
filtroIncidencias.addEventListener("change", aplicarFiltros);
filtroIntervencion.addEventListener("change", aplicarFiltros);


async function cargarDatos()
{
    try {

        const [
            respuestaEquipos,
            respuestaTickets
        ] = await Promise.all([
            fetch(API_EQUIPOS),
            fetch(API_TICKETS)
        ]);


        const datosEquipos =
            await respuestaEquipos.json();

        const datosTickets =
            await respuestaTickets.json();


        if (!respuestaEquipos.ok) {
            throw new Error(
                datosEquipos.mensaje ??
                "No se pudieron cargar los equipos."
            );
        }


        if (!respuestaTickets.ok) {
            throw new Error(
                datosTickets.mensaje ??
                "No se pudieron cargar los tickets."
            );
        }


        equipos = datosEquipos.datos;
        tickets = datosTickets.datos;


        agregarCantidadIncidencias();

        cargarUbicaciones();

        aplicarFiltros();

        mostrarGraficas();

    } catch (error) {

        console.error(
            "Error al cargar el estado de los equipos:",
            error
        );
    }
}


function agregarCantidadIncidencias()
{
    equipos.forEach(equipo => {

        const idEquipo =
            obtenerIdEquipo(equipo);


        equipo.cantidadIncidencias =
            tickets.filter(ticket => {

                const idEquipoTicket =
                    obtenerIdEquipoTicket(ticket);

                return String(idEquipoTicket) ===
                    String(idEquipo);

            }).length;
    });
}


function aplicarFiltros()
{
    let resultado = [...equipos];


    const ubicacion =
        filtroUbicacion.value;

    const estado =
        filtroEstado.value;

    const ordenIncidencias =
        filtroIncidencias.value;

    const ordenIntervencion =
        filtroIntervencion.value;


    if (ubicacion !== "") {

        resultado = resultado.filter(equipo => {

            const tipo =
                equipo.tipoUbicacion ?? null;

            const id =
                equipo.idUbicacion ?? null;


            if (ubicacion === "prestamo") {
                return tipo === "prestamo";
            }


            if (ubicacion === "ninguna") {
                return tipo === null || id === null;
            }


            return obtenerClaveUbicacion(equipo) ===
                ubicacion;
        });
    }


    if (estado !== "") {

        resultado = resultado.filter(equipo => {

            const activo =
                esActivo(equipo.activo);


            if (estado === "activo") {
                return activo;
            }

            return !activo;
        });
    }


    if (ordenIncidencias === "mayor") {

        resultado.sort(
            (a, b) =>
                b.cantidadIncidencias -
                a.cantidadIncidencias
        );

    } else if (ordenIncidencias === "menor") {

        resultado.sort(
            (a, b) =>
                a.cantidadIncidencias -
                b.cantidadIncidencias
        );
    }


    if (ordenIntervencion === "reciente") {

        resultado.sort(
            (a, b) =>
                obtenerFechaIntervencion(b) -
                obtenerFechaIntervencion(a)
        );

    } else if (ordenIntervencion === "antiguo") {

        resultado.sort(
            (a, b) =>
                obtenerFechaIntervencion(a) -
                obtenerFechaIntervencion(b)
        );
    }


    mostrarEquipos(resultado);
}


function mostrarEquipos(lista)
{
    tablaEquipos.innerHTML = "";


    if (lista.length === 0) {

        const fila =
            document.createElement("tr");

        const celda =
            document.createElement("td");

        celda.colSpan = 6;
        celda.textContent =
            "No se encontraron equipos.";

        celda.classList.add(
            "text-center",
            "py-4",
            "text-muted"
        );

        fila.appendChild(celda);
        tablaEquipos.appendChild(fila);

        return;
    }


    lista.forEach(equipo => {

        const fila =
            document.createElement("tr");


        agregarCelda(
            fila,
            `Equipo #${obtenerIdEquipo(equipo)}`
        );


        agregarCelda(
            fila,
            esActivo(equipo.activo)
                ? "Activo"
                : "Inactivo"
        );


        agregarCelda(
            fila,
            obtenerAsignacion(equipo)
        );


        agregarCelda(
            fila,
            obtenerUbicacion(equipo)
        );


        agregarCelda(
            fila,
            formatearFecha(
                equipo.ultimaIntervencion
            )
        );


        agregarCelda(
            fila,
            equipo.cantidadIncidencias
        );


        tablaEquipos.appendChild(fila);
    });
}


function agregarCelda(fila, texto)
{
    const celda =
        document.createElement("td");

    celda.textContent = texto;

    fila.appendChild(celda);
}


function cargarUbicaciones()
{
    const ubicaciones = new Map();


    equipos.forEach(equipo => {

        const tipo =
            equipo.tipoUbicacion;

        const id =
            equipo.idUbicacion;


        if (
            tipo === null ||
            tipo === undefined ||
            id === null ||
            id === undefined ||
            tipo === "prestamo"
        ) {
            return;
        }


        const clave =
            obtenerClaveUbicacion(equipo);


        ubicaciones.set(
            clave,
            `${capitalizar(tipo)} ${id}`
        );
    });


    const ubicacionesOrdenadas =
        Array.from(ubicaciones.entries())
            .sort((a, b) =>
                a[1].localeCompare(b[1])
            );


    ubicacionesOrdenadas.forEach(
        ([valor, texto]) => {

            const opcion =
                document.createElement("option");

            opcion.value = valor;
            opcion.textContent = texto;

            filtroUbicacion.appendChild(opcion);
        }
    );
}


function obtenerAsignacion(equipo)
{
    if (
        equipo.tipoUbicacion === null ||
        equipo.tipoUbicacion === undefined
    ) {
        return "Sin asignar";
    }


    if (equipo.tipoUbicacion === "prestamo") {
        return "Préstamo";
    }


    return "Asignado";
}


function obtenerUbicacion(equipo)
{
    const tipo =
        equipo.tipoUbicacion;

    const id =
        equipo.idUbicacion;


    if (
        tipo === null ||
        tipo === undefined ||
        id === null ||
        id === undefined
    ) {
        return "Sin ubicación";
    }


    if (tipo === "prestamo") {
        return "Área de préstamo";
    }


    return `${capitalizar(tipo)} ${id}`;
}


function obtenerClaveUbicacion(equipo)
{
    return `${equipo.tipoUbicacion}:${equipo.idUbicacion}`;
}


function mostrarGraficas()
{
    const equiposActivos =
        equipos.filter(
            equipo => esActivo(equipo.activo)
        ).length;


    const equiposInactivos =
        equipos.length - equiposActivos;


    if (graficoEstados !== null) {
        graficoEstados.destroy();
    }


    graficoEstados = new Chart(
        graficaEstados,
        {
            type: "bar",

            data: {
                labels: [
                    "Activos",
                    "Inactivos"
                ],

                datasets: [
                    {
                        label: "Equipos",
                        data: [
                            equiposActivos,
                            equiposInactivos
                        ]
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
        }
    );


    const equiposConIncidencias =
        equipos
            .filter(
                equipo =>
                    equipo.cantidadIncidencias > 0
            )
            .sort(
                (a, b) =>
                    b.cantidadIncidencias -
                    a.cantidadIncidencias
            );


    if (graficoIncidencias !== null) {
        graficoIncidencias.destroy();
    }


    graficoIncidencias = new Chart(
        graficaIncidencias,
        {
            type: "bar",

            data: {
                labels:
                    equiposConIncidencias.map(
                        equipo =>
                            `Equipo #${obtenerIdEquipo(equipo)}`
                    ),

                datasets: [
                    {
                        label: "Incidencias",
                        data:
                            equiposConIncidencias.map(
                                equipo =>
                                    equipo.cantidadIncidencias
                            )
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
        }
    );
}


function obtenerIdEquipo(equipo)
{
    return equipo.idEquipo ??
        equipo.id ??
        "";
}


function obtenerIdEquipoTicket(ticket)
{
    return ticket.idEquipo ??
        ticket.equipo ??
        ticket.id_equipo ??
        null;
}


function obtenerFechaIntervencion(equipo)
{
    if (!equipo.ultimaIntervencion) {
        return 0;
    }

    const fecha =
        new Date(equipo.ultimaIntervencion);

    if (Number.isNaN(fecha.getTime())) {
        return 0;
    }

    return fecha.getTime();
}


function formatearFecha(fecha)
{
    if (!fecha) {
        return "Sin intervención";
    }


    const partes =
        fecha.split("-");


    if (partes.length !== 3) {
        return fecha;
    }


    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}


function esActivo(valor)
{
    return (
        valor === true ||
        valor === 1 ||
        valor === "1"
    );
}


function capitalizar(texto)
{
    if (!texto) {
        return "";
    }

    return texto.charAt(0).toUpperCase() +
        texto.slice(1);
}