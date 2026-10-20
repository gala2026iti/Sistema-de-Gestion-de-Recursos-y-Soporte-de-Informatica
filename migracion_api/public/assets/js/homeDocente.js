const API_UBICACIONES = "../index.php/ubicaciones";
const API_EQUIPOS = "../index.php/equipos";
const API_TICKETS = "../index.php/tickets";


// ELEMENTOS DEL DOM

const botonRegistrarEstado = document.getElementById("btnRegistrarEstado");

const modalIncidencia = document.getElementById("incidencia");
const formDetalleIncidencia = document.getElementById("formDetalleIncidencia");

const tituloIncidencia = document.getElementById("titulo");
const tipoIncidencia = document.getElementById("tipo");
const asuntoIncidencia = document.getElementById("asunto");
const personaIncidencia = document.getElementById("persona");
const descripcionIncidencia = document.getElementById("descripcion");

const btnAceptar = document.getElementById("btnAceptar");
const btnCancelar = document.getElementById("btnCancelar");

const grupoLaboratorio = document.getElementById("grupoLaboratorio");
const grupoTalleres = document.getElementById("grupoTalleres");

const ubicacionSalon = document.getElementById("ubicacionSalon");
const contenedorEquipos = document.getElementById("contenedorEquipos");


// ESTADO

let equipoSeleccionado = null;

const incidencias = {};


// RESPUESTAS DE LA API

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();

    if (!texto.trim()) {
        throw new Error(`La API respondió sin cuerpo (HTTP ${respuesta.status}).`);
    }

    let json;

    try {
        json = JSON.parse(texto);

    } catch {
        throw new Error(`HTTP ${respuesta.status}: La API no devolvió JSON.`);
    }

    if (!respuesta.ok) {
        throw new Error(
            `HTTP ${respuesta.status}: ${json.mensaje ??
            "La solicitud no se pudo completar."
            }`);
    }

    return json.datos;
}


// PETICIONES

async function registrarTickets(datos) {
    const respuesta = await fetch(
        API_TICKETS, {
        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        }, body: JSON.stringify(datos)
    });

    return leerRespuestaAPI(respuesta);
}


async function obtenerUbicaciones() {
    const respuesta = await fetch(API_UBICACIONES);
    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(json.mensaje || "No se pudieron cargar las ubicaciones.");
    }

    return json.datos;
}


async function obtenerEquipos(tipoUbicacion, idUbicacion) {
    const parametros = new URLSearchParams();

    parametros.append("tipoUbicacion", tipoUbicacion);
    parametros.append("ubicacion", idUbicacion);

    const respuesta = await fetch(API_EQUIPOS + "?" + parametros.toString());

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(json.mensaje || "No se pudieron cargar los equipos.");
    }

    return json.datos;
}


// UBICACIONES

async function cargarUbicaciones() {
    try {
        const ubicaciones = await obtenerUbicaciones();

        grupoLaboratorio.innerHTML = "";
        grupoTalleres.innerHTML = "";

        ubicaciones.forEach(ubicacion => {

            if (ubicacion.tipo !== "laboratorio" && ubicacion.tipo !== "taller") {
                return;
            }

            const opcion = document.createElement("option");

            opcion.value = ubicacion.tipo + " " + ubicacion.id;

            opcion.textContent = ubicacion.tipo === "laboratorio" ? "Laboratorio " + ubicacion.id : "Taller " + ubicacion.id;

            if (ubicacion.tipo === "laboratorio") {
                grupoLaboratorio.appendChild(opcion);

            } else {
                grupoTalleres.appendChild(opcion);
            }
        });

    } catch (error) {
        console.error(error);
    }
}


// EQUIPOS

function crearEquipo(equipo) {
    const contenedor = document.createElement("div");

    contenedor.className = "espacioEquipo-body d-flex flex-column justify-content-between p-3";
    contenedor.dataset.idEquipo = equipo.idEquipo;
    contenedor.dataset.posicion = equipo.posicion;


    const cabecera = document.createElement("div");
    cabecera.className = "d-flex justify-content-between align-items-center mb-3";

    const nombreEquipo = document.createElement("h4");

    nombreEquipo.className = "h5 mb-0 fw-bold text-secondary";
    nombreEquipo.textContent = "PC" + equipo.posicion;

    const botonEditar = document.createElement("button");

    botonEditar.type = "button";
    botonEditar.className = "btn btn-primary btn-sm d-none";
    botonEditar.textContent = "Editar";

    cabecera.appendChild(nombreEquipo);
    cabecera.appendChild(botonEditar);


    const opciones = document.createElement("div");
    opciones.className = "bg-light p-2 rounded-3 d-flex justify-content-around";


    const contenedorSinProblemas = document.createElement("div");
    contenedorSinProblemas.className = "form-check form-check-inline mb-0";

    const sinProblemas = document.createElement("input");

    sinProblemas.className = "form-check-input";
    sinProblemas.type = "radio";
    sinProblemas.name = "estado-" + equipo.idEquipo;
    sinProblemas.id = "ok-" + equipo.idEquipo;
    sinProblemas.value = "ok";
    sinProblemas.checked = true;

    const textoSinProblemas = document.createElement("label");

    textoSinProblemas.className = "form-check-label text-success fw-semibold";
    textoSinProblemas.htmlFor = sinProblemas.id;
    textoSinProblemas.textContent = "Sin problemas";

    contenedorSinProblemas.appendChild(sinProblemas);
    contenedorSinProblemas.appendChild(textoSinProblemas);


    const contenedorConProblemas = document.createElement("div");
    contenedorConProblemas.className = "form-check form-check-inline mb-0";

    const conProblemas = document.createElement("input");

    conProblemas.className = "form-check-input";
    conProblemas.type = "radio";
    conProblemas.name = "estado-" + equipo.idEquipo;
    conProblemas.id = "inc-" + equipo.idEquipo;
    conProblemas.value = "incidencia";

    const textoConProblemas = document.createElement("label");

    textoConProblemas.className = "form-check-label text-danger fw-semibold";
    textoConProblemas.htmlFor = conProblemas.id;
    textoConProblemas.textContent = "Hay incidencia";

    contenedorConProblemas.appendChild(conProblemas);
    contenedorConProblemas.appendChild(textoConProblemas);


    conProblemas.addEventListener("change",
        function () {
            if (conProblemas.checked) {
                abrirFormularioIncidencia(equipo, botonEditar);
            }
        });


    sinProblemas.addEventListener("change",
        function () {
            if (sinProblemas.checked) {

                /*
                 * Si vuelve a marcar "Sin problemas",
                 * eliminamos la incidencia de esa PC.
                 */
                delete incidencias[equipo.idEquipo];

                botonEditar.classList.add("d-none");
            }
        });


    botonEditar.addEventListener("click",
        function () {
            abrirFormularioIncidencia(equipo, botonEditar);
        });


    opciones.appendChild(contenedorSinProblemas);
    opciones.appendChild(contenedorConProblemas);

    contenedor.appendChild(cabecera);
    contenedor.appendChild(opciones);

    return contenedor;
}


async function cargarEquipos() {
    contenedorEquipos.innerHTML = "";

    /*
     * Si cambia de laboratorio o taller,
     * descartamos las incidencias que
     * todavía no fueron registradas.
     */
    for (const idEquipo in incidencias) {
        delete incidencias[idEquipo];
    }

    equipoSeleccionado = null;

    const valorUbicacion = ubicacionSalon.value;

    if (valorUbicacion === "") {
        return;
    }

    const partes = valorUbicacion.split(" ");
    const tipoUbicacion = partes[0];
    const idUbicacion = partes[1];

    try {
        const equipos = await obtenerEquipos(tipoUbicacion, idUbicacion);

        if (equipos.length === 0) {
            const mensaje = document.createElement("p");

            mensaje.className = "text-center mt-3";
            mensaje.textContent = "No hay equipos registrados en esta ubicación.";

            contenedorEquipos.appendChild(mensaje);

            return;
        }

        equipos.forEach(equipo => {
            const elementoEquipo = crearEquipo(equipo);
            contenedorEquipos.appendChild(elementoEquipo);
        });

    } catch (error) {
        console.error(error);
    }
}


// INCIDENCIAS

function abrirFormularioIncidencia(equipo, botonEditar) {
    equipoSeleccionado = {
        idEquipo: equipo.idEquipo,
        posicion: equipo.posicion,
        botonEditar: botonEditar
    };

    tituloIncidencia.textContent = "Registro de incidencia - PC" + equipo.posicion;

    /*
     * Si la PC ya tenía una incidencia,
     * cargamos los datos anteriores.
     */
    if (incidencias[equipo.idEquipo]) {
        const incidencia = incidencias[equipo.idEquipo];

        tipoIncidencia.value = incidencia.tipo;
        asuntoIncidencia.value = incidencia.asunto;
        personaIncidencia.value = incidencia.persona;
        descripcionIncidencia.value = incidencia.descripcion;

        const gravedad = document.querySelector('input[name="gravedad"][value="' + incidencia.gravedad + '"]');

        if (gravedad) {
            gravedad.checked = true;
        }

    } else {

        /*
         * Si es una incidencia nueva,
         * limpiamos el formulario.
         */
        formDetalleIncidencia.reset();
    }

    modalIncidencia.showModal();
}


function obtenerTicketsRegistrados() {
    const tickets = [];

    for (const idEquipo in incidencias) {
        const incidencia = incidencias[idEquipo];

        tickets.push({
            idEquipo: idEquipo,
            tipo: incidencia.tipo,
            asunto: incidencia.asunto,
            persona: incidencia.persona,
            gravedad: incidencia.gravedad,
            descripcion: incidencia.descripcion
        });
    }

    return tickets;
}


async function gestionarRegistroTickets() {
    const valorUbicacion = ubicacionSalon.value.trim();

    if (valorUbicacion === "") {
        alert("Debe seleccionar un laboratorio o taller.");
        return;
    }

    const partes = valorUbicacion.split(" ");

    const tipoUbicacion = partes[0] ?? "";
    const idUbicacion = partes[1] ?? "";

    const tickets = obtenerTicketsRegistrados();

    /*
     * Por ahora la API de tickets solamente
     * registra incidencias.
     *
     * Las PCs marcadas como "Sin problemas"
     * no generan un ticket.
     */
    if (tickets.length === 0) {
        alert("No hay incidencias para registrar.");

        return;
    }

    const datos = {
        idUbicacion: idUbicacion,
        tipoUbicacion: tipoUbicacion,
        tickets: tickets
    };

    try {
        const resultado = await registrarTickets(datos);

        alert(resultado.mensaje);

        /*
         * Limpiamos las incidencias
         * temporales después de que
         * el servidor confirmó el registro.
         */
        for (const idEquipo in incidencias) {
            delete incidencias[idEquipo];
        }

        /*
         * Volvemos a cargar las PCs.
         * Por lo tanto aparecerán nuevamente
         * como "Sin problemas".
         */
        await cargarEquipos();

    } catch (error) {
        alert("No se pudieron registrar las incidencias: " + error.message);
    }
}


// EVENTOS

btnCancelar.addEventListener("click",
    function () {
        if (equipoSeleccionado === null) {
            modalIncidencia.close();
            return;
        }

        const idEquipo = equipoSeleccionado.idEquipo;

        /*
         * Si todavía no existe una incidencia
         * guardada, significa que canceló
         * una incidencia nueva.
         */
        if (!incidencias[idEquipo]) {
            const opcionSinProblemas = document.getElementById("ok-" + idEquipo);

            if (opcionSinProblemas) {
                opcionSinProblemas.checked = true;
            }
        }

        modalIncidencia.close();

        equipoSeleccionado = null;
    });


btnAceptar.addEventListener("click",
    function () {
        if (equipoSeleccionado === null) {
            return;
        }

        const gravedadSeleccionada = document.querySelector(
            'input[name="gravedad"]:checked'
        );

        if (
            tipoIncidencia.value === "" ||
            asuntoIncidencia.value.trim() === "" ||
            gravedadSeleccionada === null ||
            descripcionIncidencia.value.trim() === ""
        ) {
            alert("Debe completar los datos de la incidencia.");

            return;
        }

        incidencias[equipoSeleccionado.idEquipo] = {
            tipo: tipoIncidencia.value.toLowerCase(),
            asunto: asuntoIncidencia.value.trim(),
            persona: personaIncidencia.value.trim(),
            gravedad: gravedadSeleccionada.value,
            descripcion: descripcionIncidencia.value.trim()
        };

        equipoSeleccionado.botonEditar.classList.remove("d-none");

        modalIncidencia.close();

        equipoSeleccionado = null;
    });


ubicacionSalon.addEventListener("change", cargarEquipos);
botonRegistrarEstado.addEventListener("click", gestionarRegistroTickets);

document.addEventListener("DOMContentLoaded", cargarUbicaciones);