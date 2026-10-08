const urlParams = new URLSearchParams(window.location.search);
const idTicket = urlParams.get("id");
const API_DETALLE = `../../api/ticket.php?id=${idTicket}`;

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();
    if (!texto.trim()) throw new Error("Sin respuesta de la API.");
    const json = JSON.parse(texto);
    if (!respuesta.ok) throw new Error(json.mensaje || "Error.");
    return json.datos;
}

async function cargarDetalleTicket() {
    if (!idTicket) {
        alert("ID de ticket no especificado.");
        return;
    }

    try {
        const respuesta = await fetch(API_DETALLE);
        const datos = await leerRespuestaAPI(respuesta);
        const ticket = Array.isArray(datos) ? datos[0] : datos;

        document.getElementById("tituloTicket").textContent = `${ticket.asunto} - ID: ${ticket.id}`;
        document.getElementById("usuarioAsignado").value = ticket.colaboradores || "Sin asignar";
        document.getElementById("selectorEstado").value = ticket.estado;
        document.getElementById("selectorGravedad").value = ticket.gravedad;
        document.getElementById("entradaPC").value = ticket.idEquipo;
        document.getElementById("entradaCategoria").value = ticket.tipo;
        document.getElementById("contenido").value = ticket.descripcion;

    } catch (error) {
        alert("No se pudo cargar el detalle del ticket: " + error.message);
    }
}

document.getElementById("formDetalleTicket").addEventListener("submit", async (e) => {
    e.preventDefault();
    alert("Cambios guardados exitosamente.");
});

cargarDetalleTicket();