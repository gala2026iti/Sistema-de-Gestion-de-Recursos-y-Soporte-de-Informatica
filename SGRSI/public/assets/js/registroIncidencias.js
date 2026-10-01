const selectorUbicacion = document.getElementById("ubicacionSalon");
const dialogoIncidencia = document.getElementById("incidencia");
const tituloIncidencia = document.getElementById("titulo");
const formularioIncidencia = dialogoIncidencia.querySelector("form");
const campoTipo = document.getElementById("tipo");
const campoAsunto = document.getElementById("asunto");
const campoPersona = document.getElementById("persona");
const campoDescripcion = document.getElementById("descripcion");
const botonAceptar = document.getElementById("btnAceptar");
const botonCancelar = document.getElementById("btnCancelar");
const formularioPrincipal = document.getElementById("formIncidencia");

let equipoActualId = null;
let incidenciaGuardada = false;
let salidaConfirmada = false;

const confirmarSalida = () => {
  if (localStorage.getItem("borradorIncidenciasDocente") != null) {
    const confirmada = window.confirm(
      "Si salís de esta página, se borrarán los borradores de incidencias. ¿Querés continuar?",
    );
    if (!confirmada) return false;

    localStorage.removeItem("borradorIncidenciasDocente");
  }
  salidaConfirmada = true;
  return true;
};

window.addEventListener("beforeunload", (e) => {
  if (salidaConfirmada) return;

  e.preventDefault();
});

document.addEventListener("submit", (e) => {
  if (e.target.id === "formIncidencia") return;
  if (!confirmarSalida()) e.preventDefault();
});

const leerBorradores = () => {
  try {
    const borradores = JSON.parse(
      localStorage.getItem("borradorIncidenciasDocente") || "[]",
    );
    return Array.isArray(borradores) ? borradores : [];
  } catch {
    return [];
  }
};

const obtenerUbicacion = () => selectorUbicacion?.value || "";

const guardarBorrador = (estado = "incidencia") => {
  if (equipoActualId === null || equipoActualId === undefined) return;

  const ubicacion = obtenerUbicacion();
  const gravedad =
    formularioIncidencia?.querySelector('input[name="gravedad"]:checked')
      ?.value || "";
  const fila = [
    ubicacion,
    String(equipoActualId),
    estado,
    campoTipo.value ?? "",
    campoAsunto.value ?? "",
    campoPersona.value ?? "",
    gravedad,
    campoDescripcion.value ?? "",
  ];

  const borradores = leerBorradores();
  const indice = borradores.findIndex(
    (borrador) =>
      borrador[0] === ubicacion &&
      String(borrador[1]) === String(equipoActualId),
  );

  if (indice === -1) borradores.push(fila);
  else borradores[indice] = fila;

  localStorage.setItem(
    "borradorIncidenciasDocente",
    JSON.stringify(borradores),
  );
};

const obtenerBorrador = (idEquipo) =>
  leerBorradores().find(
    (borrador) =>
      Array.isArray(borrador) &&
      borrador[0] === obtenerUbicacion() &&
      String(borrador[1]) === String(idEquipo),
  );

const abrirFormularioIncidencia = (idEquipo) => {
  if (!dialogoIncidencia || !formularioIncidencia) return;

  equipoActualId = idEquipo;
  incidenciaGuardada = false;
  const borrador = obtenerBorrador(idEquipo) || [];
  tituloIncidencia.textContent = `Registro de incidencia - PC: ${idEquipo}`;

  campoTipo.value = borrador[3] ?? "";
  campoAsunto.value = borrador[4] ?? "";
  campoPersona.value = borrador[5] ?? "";
  campoDescripcion.value = borrador[7] ?? "";

  formularioIncidencia
    .querySelectorAll('input[name="gravedad"]')
    .forEach((r) => {
      r.checked = r.value === (borrador[6] || "");
    });

  guardarBorrador("incidencia");
  if (!dialogoIncidencia.open) dialogoIncidencia.showModal();
};

const cerrarFormularioIncidencia = () => {
  if (dialogoIncidencia?.open) dialogoIncidencia.close();
};

dialogoIncidencia?.addEventListener("close", () => {
  if (!incidenciaGuardada && equipoActualId !== null) {
    const rSinIncidencia = document.getElementById(`ok-${equipoActualId}`);
    if (rSinIncidencia) rSinIncidencia.checked = true;

    guardarBorrador("ok");
  }

  equipoActualId = null;
});

document.addEventListener("change", (e) => {
  const r = e.target;
  if (r.type !== "radio") return;

  const coincidencia = r.name.match(/^estado-(.+)$/);
  if (!coincidencia) return;

  const idEquipo = coincidencia[1];
  if (r.value === "incidencia") {
    abrirFormularioIncidencia(idEquipo);
    return;
  }

  equipoActualId = idEquipo;
  guardarBorrador(r.value);
});

formularioIncidencia.addEventListener("input", () => guardarBorrador());
formularioIncidencia.addEventListener("change", () => guardarBorrador());

botonAceptar.addEventListener("click", () => {
  guardarBorrador("incidencia");
  incidenciaGuardada = true;
  cerrarFormularioIncidencia();
});

botonCancelar.addEventListener("click", () => {
  cerrarFormularioIncidencia();
});

document
  .getElementById("formIncidencia")
  .addEventListener("submit", (e) => e.preventDefault());

selectorUbicacion.addEventListener("change", () => {
  const opcionSeleccionada =
    selectorUbicacion.options[selectorUbicacion.selectedIndex];
  if (!opcionSeleccionada?.value) return;

  const tipo = opcionSeleccionada.classList.contains("opcion-laboratorio")
    ? "laboratorio"
    : opcionSeleccionada.classList.contains("opcion-taller")
      ? "taller"
      : null;

  if (tipo) {
    if (confirmarSalida()) {
      window.location.href = `homeDocente.php?tipo=${tipo}&ubicacion=${opcionSeleccionada.value}`;
    } else {
      selectorUbicacion.selectedIndex = 0;
    }
  }
});

formularioPrincipal.addEventListener("submit", (e) => {
  e.preventDefault();
  const input = document.createElement("input");

  input.type = "hidden";
  input.name = "equiposReportados";
  input.value = localStorage.getItem("borradorIncidenciasDocente");

  formularioPrincipal.appendChild(input);
    formularioPrincipal.submit();

});
