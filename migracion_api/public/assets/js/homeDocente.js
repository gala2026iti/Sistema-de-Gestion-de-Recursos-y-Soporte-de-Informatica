const API_UBICACIONES = "../../api/ticket.php";


const selectUbicacion = document.getElementById("ubicacionSalon");
const grupoLaboratorio = document.getElementById("grupoLaboratorio");
const grupoTalleres = document.getElementById("grupoTalleres");
const contenedorEquipos = document.getElementById("contenedorEquipos");

async function leerRespuestaAPI(respuesta) {
    const texto = await respuesta.text();
    if (!texto.trim()) throw new Error("Sin respuesta de la API.");
    const json = JSON.parse(texto);
    if (!respuesta.ok) throw new Error(json.mensaje || "Error.");
    return json.datos;
}

async function cargarUbicaciones() {
    try {
        const respuesta = await fetch(API_UBICACIONES);
        const ubicaciones = await leerRespuestaAPI(respuesta);

        ubicaciones.forEach(ubi => {
            const option = document.createElement("option");
            option.value = `${ubi.tipo}-${ubi.id}`;
            option.textContent = `${ubi.tipo.charAt(0).toUpperCase() + ubi.tipo.slice(1)} ${ubi.id}`;

            if (ubi.tipo.toLowerCase() === 'laboratorio') {
                grupoLaboratorio.appendChild(option);
            } else if (ubi.tipo.toLowerCase() === 'taller') {
                grupoTalleres.appendChild(option);
            }
        });
    } catch (error) {
        console.error("Error al cargar ubicaciones:", error.message);
    }
}

selectUbicacion.addEventListener("change", async (e) => {
    contenedorEquipos.replaceChildren();
    const valorSeleccionado = e.target.value;
    if (!valorSeleccionado) return;

    const [tipo, idUbicacion] = valorSeleccionado.split("-");

    try {
        const respuesta = await fetch(`${API_UBICACIONES}?ubicacion=${idUbicacion}&tipo=${tipo}`);
        const equipos = await leerRespuestaAPI(respuesta);

        equipos.forEach(equipo => {
            const div = document.createElement("div");
            div.className = "espacioEquipo-body d-flex flex-column justify-content-between p-3 mb-3 border rounded";
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="h5 mb-0 fw-bold text-secondary">PC: ${equipo.posicion} (ID: ${equipo.idEquipo})</h4>
                </div>
                <div class="bg-light p-2 rounded-3 d-flex justify-content-around">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="estado-${equipo.idEquipo}" id="ok-${equipo.idEquipo}" value="ok" checked>
                        <label class="form-check-label text-success fw-semibold" for="ok-${equipo.idEquipo}">Sin problemas</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="estado-${equipo.idEquipo}" id="inc-${equipo.idEquipo}" value="incidencia">
                        <label class="form-check-label text-danger fw-semibold" for="inc-${equipo.idEquipo}">Hay incidencia</label>
                    </div>
                </div>
            `;
            contenedorEquipos.appendChild(div);
        });
    } catch (error) {
        alert("Error al cargar los equipos del salón.");
    }
});

cargarUbicaciones();