const claveBorradores = "borradorIncidenciasDocente";
localStorage.removeItem(claveBorradores);

// Posiciones de los datos guardados en cada array de borrador.
const indiceBorrador = {
  idUbicacion: 0,
  tipoUbicacion: 1,
  idEquipo: 2,
  estado: 3,
  tipoIncidencia: 4,
  asunto: 5,
  persona: 6,
  gravedad: 7,
  descripcion: 8,
};

const selectorUbicacion = document.getElementById("ubicacionSalon");
const dialogoIncidencia = document.getElementById("incidencia");
const formularioIncidencia = dialogoIncidencia.querySelector("form");
const formularioPrincipal = document.getElementById("formIncidencia");
const campoTipo = document.getElementById("tipo");
const campoAsunto = document.getElementById("asunto");
const campoPersona = document.getElementById("persona");
const campoGravedad = formularioIncidencia.querySelectorAll('input[name="gravedad"]');
const campoDescripcion = document.getElementById("descripcion");
const tituloIncidencia = document.getElementById("titulo");
const botonAceptar = document.getElementById("btnAceptar");
const botonCancelar = document.getElementById("btnCancelar");

let idEquipoActual = null;
let incidenciaAceptada = false;
let salidaConfirmada = false;

const leerBorradores = () => {
  const textoBorradores = localStorage.getItem(claveBorradores) || "[]";
  return JSON.parse(textoBorradores);
};

const guardarBorradores = (borradores) => {
  localStorage.setItem(claveBorradores, JSON.stringify(borradores));
};

const obtenerUbicacionSeleccionada = () => {
  const [tipoUbicacion, idUbicacion] = selectorUbicacion.value.split("-");
  return { idUbicacion, tipoUbicacion };
};

const buscarBorrador = (idEquipo) => {
  const { idUbicacion, tipoUbicacion } = obtenerUbicacionSeleccionada();
  const borradores = leerBorradores();

  return borradores.find((borrador) =>
    String(borrador[indiceBorrador.idUbicacion]) === String(idUbicacion) &&
    borrador[indiceBorrador.tipoUbicacion] === tipoUbicacion &&
    String(borrador[indiceBorrador.idEquipo]) === String(idEquipo)
  );
};

const guardarBorrador = (estado = "incidencia") => {
  if (idEquipoActual === null) return;

  const { idUbicacion, tipoUbicacion } = obtenerUbicacionSeleccionada();
  const gravedadSeleccionada = formularioIncidencia.querySelector(
    'input[name="gravedad"]:checked',
  );

  const borradorActual = [];
  borradorActual[indiceBorrador.idUbicacion] = idUbicacion;
  borradorActual[indiceBorrador.tipoUbicacion] = tipoUbicacion;
  borradorActual[indiceBorrador.idEquipo] = String(idEquipoActual);
  borradorActual[indiceBorrador.estado] = estado;
  borradorActual[indiceBorrador.tipoIncidencia] = campoTipo.value;
  borradorActual[indiceBorrador.asunto] = campoAsunto.value;
  borradorActual[indiceBorrador.persona] = campoPersona.value.trim();
  borradorActual[indiceBorrador.gravedad] = gravedadSeleccionada?.value || "";
  borradorActual[indiceBorrador.descripcion] = campoDescripcion.value;

  const borradores = leerBorradores();
  const indiceExistente = borradores.findIndex((borrador) =>
    String(borrador[indiceBorrador.idUbicacion]) === String(idUbicacion) &&
    borrador[indiceBorrador.tipoUbicacion] === tipoUbicacion &&
    String(borrador[indiceBorrador.idEquipo]) === String(idEquipoActual)
  );

  if (indiceExistente === -1) {
    borradores.push(borradorActual);
  } else {
    borradores[indiceExistente] = borradorActual;
  }

  guardarBorradores(borradores);
};

const cargarBorradorEnFormulario = (borrador) => {
  campoTipo.value = borrador?.[indiceBorrador.tipoIncidencia] || "";
  campoAsunto.value = borrador?.[indiceBorrador.asunto] || "";
  campoPersona.value = borrador?.[indiceBorrador.persona] || "";
  campoDescripcion.value = borrador?.[indiceBorrador.descripcion] || "";

  campoGravedad.forEach((opcion) => {
    opcion.checked = opcion.value === (borrador?.[indiceBorrador.gravedad] || "");
  });
};

const abrirFormularioIncidencia = (idEquipo) => {
  idEquipoActual = idEquipo;
  incidenciaAceptada = false;
  tituloIncidencia.textContent = `Registro de incidencia - PC: ${idEquipo}`;

  cargarBorradorEnFormulario(buscarBorrador(idEquipo));
  guardarBorrador("incidencia");
  dialogoIncidencia.showModal();
};

const cerrarFormularioIncidencia = () => {
  dialogoIncidencia.close();
};

const confirmarCambioDeUbicacion = () => {
  if (localStorage.getItem(claveBorradores) === null) return true;

  const confirmado = window.confirm(
    "Si salís de esta página, se borrarán los borradores de incidencias. ¿Querés continuar?",
  );
  if (confirmado) localStorage.removeItem(claveBorradores);
  return confirmado;
};

window.addEventListener("beforeunload", (evento) => {
  if (!salidaConfirmada) evento.preventDefault();
});

document.addEventListener("change", (evento) => {
  const opcionEstado = evento.target;
  if (opcionEstado.type !== "radio") return;
  if (!opcionEstado.name.startsWith("estado-")) return;

  idEquipoActual = opcionEstado.name.replace("estado-", "");

  if (opcionEstado.value === "incidencia") {
    abrirFormularioIncidencia(idEquipoActual);
  } else {
    guardarBorrador(opcionEstado.value);
  }
});

formularioIncidencia.addEventListener("input", () => guardarBorrador());
formularioIncidencia.addEventListener("change", () => guardarBorrador());
formularioIncidencia.addEventListener("submit", (evento) => evento.preventDefault());

dialogoIncidencia.addEventListener("close", () => {
  if (!incidenciaAceptada && idEquipoActual !== null) {
    document.getElementById(`ok-${idEquipoActual}`).checked = true;
    guardarBorrador("ok");
  }
  idEquipoActual = null;
});

botonAceptar.addEventListener("click", () => {
  guardarBorrador("incidencia");
  incidenciaAceptada = true;
  cerrarFormularioIncidencia();
});

botonCancelar.addEventListener("click", cerrarFormularioIncidencia);

selectorUbicacion.addEventListener("change", () => {
  const opcionSeleccionada = selectorUbicacion.options[selectorUbicacion.selectedIndex];
  if (!opcionSeleccionada.value) return;

  const { idUbicacion } = obtenerUbicacionSeleccionada();
  const tipoUbicacion = opcionSeleccionada.classList.contains("opcion-laboratorio")
    ? "laboratorio"
    : "taller";

  if (confirmarCambioDeUbicacion()) {
    salidaConfirmada = true;
    window.location.href = `homeDocente.php?tipo=${tipoUbicacion}&ubicacion=${idUbicacion}`;
  } else {
    selectorUbicacion.selectedIndex = 0;
  }
});

formularioPrincipal.addEventListener("submit", (evento) => {
  evento.preventDefault();

  let campoReportes = formularioPrincipal.querySelector('input[name="equiposReportados"]');
  if (!campoReportes) {
    campoReportes = document.createElement("input");
    campoReportes.type = "hidden";
    campoReportes.name = "equiposReportados";
    formularioPrincipal.appendChild(campoReportes);
  }

  campoReportes.value = JSON.stringify(leerBorradores());
  salidaConfirmada = true;
  formularioPrincipal.submit();
});
