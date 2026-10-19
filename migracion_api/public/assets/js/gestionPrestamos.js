const API_PRESTAMOS = "../index.php/prestamos";


// ELEMENTOS DEL DOM

const formFiltros = document.getElementById("formFiltros");

const filtroEstado = document.getElementById("estado");
const filtroId = document.getElementById("idPrestamo");

const btnLimpiarFiltros = document.getElementById("btnLimpiarFiltros");

const cuerpoTablaPrestamos = document.getElementById("cuerpoTablaPrestamos");

const btnRegistrarPrestamo = document.getElementById("btnRegistrarPrestamo");
const btnCancelarPrestamo = document.getElementById("btnCancelarPrestamo");

const modalPrestamo = document.getElementById("modalPrestamo");
const formPrestamo = document.getElementById("formPrestamo");

const ciPrestado = document.getElementById("ciPrestado");
const nombrePrestado = document.getElementById("nombrePrestado");
const listaDispositivos = document.getElementById("listaDispositivos");
const fechaFin = document.getElementById("fechaFin");


// RESPUESTAS DE LA API

async function leerRespuestaAPI(respuesta)
{
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
            `HTTP ${respuesta.status}: ${
                json.mensaje ??
                "La solicitud no se pudo completar."
            }`);
    }

    return json.datos;
}


// PETICIONES

async function obtenerPrestamos()
{
    const parametros = new URLSearchParams();
    if (filtroEstado.value !== "") {
        parametros.set("estado",filtroEstado.value);
    }

    if (filtroId.value.trim() !== "") {
        parametros.set("id",filtroId.value.trim());
    }

    let url = API_PRESTAMOS;

    if (parametros.toString() !== "") {
        url += "?" + parametros.toString();
    }

    const respuesta = await fetch(url);
    return leerRespuestaAPI(respuesta);
}

async function obtenerEquiposDisponibles()
{
    const respuesta = await fetch(API_PRESTAMOS + "?disponibles=1");
    return leerRespuestaAPI(respuesta);
}

async function registrarPrestamo(prestamo)
{
    const respuesta = await fetch(
        API_PRESTAMOS,{
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            },

            body: JSON.stringify(prestamo)
        });
    return leerRespuestaAPI(respuesta);
}

async function devolverPrestamo(idPrestamo)
{
    const respuesta = await fetch(
        API_PRESTAMOS + "/" + encodeURIComponent(idPrestamo),{
            method: "PATCH",
            headers: {
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            }
        });
    return leerRespuestaAPI(respuesta);
}


// TABLA

function crearCelda(texto)
{
    const celda = document.createElement("td");
    celda.textContent = texto;
    return celda;
}


function crearBotonDevolver(prestamo)
{
    const boton = document.createElement("button");

    boton.type = "button";
    boton.classList.add("btn", "btn-primary", "ms-1", "text-bold");
    boton.textContent = "Finalizar";

    boton.addEventListener("click",async function ()
    {
        const confirmar = confirm("¿Desea registrar la devolución de este préstamo?");

        if (!confirmar) {
            return;
        }

        try {
            const resultado = await devolverPrestamo(prestamo.id);
            alert(resultado.mensaje);
            await cargarPrestamos();

        } catch (error) {
            alert(error.message);
        }
    });
    return boton;
}


function crearFilaPrestamo(prestamo)
{
    const fila = document.createElement("tr");
    fila.appendChild(crearCelda(prestamo.id));
    const tecnico = prestamo.nombreTecnico !== null ? prestamo.nombreTecnico + " - (" + prestamo.ciTecnico + ")" : "No disponible";
    fila.appendChild(crearCelda(tecnico));
    fila.appendChild( crearCelda( prestamo.nombrePrestado + " - (" + prestamo.ciPrestado + ")" ));
    fila.appendChild(crearCelda(prestamo.idEquipo));
    fila.appendChild(crearCelda( prestamo.fechaFin + " - " + prestamo.horaFin));

    const devuelto = Number(prestamo.devuelto) === 1;

    fila.appendChild(
        crearCelda( devuelto ? "Devuelto" : "Pendiente"));

    const celdaAcciones = document.createElement("td");

    if (!devuelto) {
        celdaAcciones.appendChild(crearBotonDevolver(prestamo));
    }

    fila.appendChild(celdaAcciones);

    return fila;
}


function mostrarPrestamos(prestamos)
{
    cuerpoTablaPrestamos.replaceChildren();

    if (prestamos.length === 0) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");

        celda.colSpan = 7;
        celda.classList.add("text-center", "py-4", "text-muted", "text-bold");
        celda.textContent = "No se encontraron préstamos.";

        fila.appendChild(celda);
        cuerpoTablaPrestamos.appendChild(fila);

        return;
    }

    for (const prestamo of prestamos) {
        cuerpoTablaPrestamos.appendChild(crearFilaPrestamo(prestamo));
    }
}


// EQUIPOS

function mostrarEquiposDisponibles(equipos)
{
    listaDispositivos.replaceChildren();

    const opcionInicial = document.createElement("option");

    opcionInicial.value = "";
    opcionInicial.textContent = "Elegir equipo";

    listaDispositivos.appendChild(opcionInicial);

    for (const equipo of equipos) {
        const opcion = document.createElement("option");

        opcion.value = equipo.idEquipo;
        opcion.textContent = "PC: " + equipo.idEquipo;

        listaDispositivos.appendChild(opcion);
    }
}


// MODAL

async function abrirModalPrestamo()
{
    try {
        const equipos = await obtenerEquiposDisponibles();

        mostrarEquiposDisponibles(equipos);

        modalPrestamo.classList.remove("d-none");
        modalPrestamo.classList.add("d-flex");

    } catch (error) {
        alert(
            "No se pudieron cargar los equipos disponibles: " + error.message);
    }
}


function cerrarModalPrestamo()
{
    formPrestamo.reset();

    modalPrestamo.classList.remove("d-flex");
    modalPrestamo.classList.add("d-none");
}


// GESTIÓN DE PRÉSTAMOS

async function cargarPrestamos()
{
    try {
        const prestamos = await obtenerPrestamos();

        mostrarPrestamos(prestamos);

    } catch (error) {
        alert(
            "No se pudieron cargar los préstamos: " + error.message );
    }
}


async function gestionarRegistroPrestamo(evento)
{
    evento.preventDefault();

    const cedula = ciPrestado.value.trim();
    const nombre = nombrePrestado.value.trim();
    const equipo = listaDispositivos.value;
    const fechaHora = fechaFin.value;

    if (!/^\d{8}$/.test(cedula)) {
        alert("La cédula debe contener 8 dígitos.");
        return;
    }

    if (nombre === "") {
        alert("Debe ingresar el nombre del solicitante.");
        return;
    }

    if (equipo === "") {
        alert("Debe seleccionar un equipo.");
        return;
    }

    if (fechaHora === "") {
        alert("Debe ingresar la fecha y hora de devolución.");
        return;
    }

    const fechaSeleccionada = new Date(fechaHora);

    if (fechaSeleccionada <= new Date()) {
        alert("La fecha y hora de devolución deben ser posteriores al momento actual.");
        return;
    }

    const partes = fechaHora.split("T");

    const prestamo = {
        nombrePrestado: nombre,
        ciPrestado: cedula,
        idEquipo: equipo,
        fechaFin: partes[0],
        horaFin: partes[1]
    };

    try {
        const resultado = await registrarPrestamo(prestamo);
        alert(resultado.mensaje);
        cerrarModalPrestamo();
        await cargarPrestamos();

    } catch (error) {
        alert(error.message);
    }
}


function aplicarFiltros(evento)
{
    evento.preventDefault();

    const id = filtroId.value.trim();

    if (id !== "" && !/^\d+$/.test(id)) {
        alert("El ID del préstamo no es válido.");
        return;
    }

    cargarPrestamos();
}


function limpiarFiltros()
{
    filtroEstado.value = "";
    filtroId.value = "";
    cargarPrestamos();
}


// EVENTOS

formFiltros.addEventListener("submit",aplicarFiltros);
btnLimpiarFiltros.addEventListener("click",limpiarFiltros);

btnRegistrarPrestamo.addEventListener("click",abrirModalPrestamo);
btnCancelarPrestamo.addEventListener("click",cerrarModalPrestamo);

formPrestamo.addEventListener("submit",gestionarRegistroPrestamo);


// INICIALIZACIÓN

cargarPrestamos();