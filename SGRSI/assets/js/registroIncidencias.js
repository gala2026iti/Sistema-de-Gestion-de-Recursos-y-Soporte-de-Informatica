const selectSector = document.getElementById("sector");
const optgroupLaboratorios = document.getElementById("optgroupLaboratorios");
const optgroupTalleres = document.getElementById("optgroupTalleres");
const contenedorEquipos = document.getElementById("contenedor-equipos");

const formularioSalon = document.querySelector(".main-formulario form");

const modalIncidencia = document.getElementById("modal-incidencia");
const tituloModal = document.getElementById("titulo-modal");
const inputTipo = document.getElementById("tipo");
const inputAsunto = document.getElementById("asunto-modal");
const inputPersona = document.getElementById("persona-modal");
const txtDescripcion = document.getElementById("descripcion-modal");
const btnAceptarModal = document.getElementById("btnAceptarModal");
const btnCancelarModal = document.getElementById("btnCancelarModal");

let incidenciasTemporales = {};  
let pcActualId = null;           


const obtenerSalonesSistema = () => {
    const datos = localStorage.getItem("salones");
    if (datos === null || datos === undefined || datos === "") {
        return [];
    } else {
        return JSON.parse(datos);
    }
};

const popularSelectSalones = () => {
    optgroupLaboratorios.innerHTML = "";
    optgroupTalleres.innerHTML = "";

    const salones = obtenerSalonesSistema();

    salones.forEach(salon => {
        const option = document.createElement("option");
        option.value = salon.id; 
        option.textContent = `${salon.nombre || salon.tipo} ${salon.id}`;

        const tipoLimpio = String(salon.tipo).toLowerCase();
        if (tipoLimpio === "laboratorio" || tipoLimpio === "laboratorios") {
            optgroupLaboratorios.appendChild(option);
        } else if (tipoLimpio === "taller" || tipoLimpio === "talleres") {
            optgroupTalleres.appendChild(option);
        }
    });
};


const renderizarEquiposDelSalon = (idSalonSeleccionado) => {
    contenedorEquipos.innerHTML = "";

    if (idSalonSeleccionado === "") return;

    const salones = obtenerSalonesSistema();
    const salonEncontrated = salones.find(s => String(s.id) === String(idSalonSeleccionado));
    
    if (!salonEncontrated) return;

    const equipos = salonEncontrated.espacios || salonEncontrated.prestamos || [];

    if (equipos.length === 0) {
        contenedorEquipos.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="alert alert-info border-0 shadow-sm">No hay equipos registrados en este salón.</div>
            </div>
        `;
        return;
    }

    equipos.forEach((equipo, indice) => {
        const pcId = equipo.id || equipo.numeroBanco || `PC-${indice + 1}`;
        const tieneBorrador = incidenciasTemporales[pcId] !== undefined;

        const col = document.createElement("div");
        col.className = "col-12 col-md-6 col-lg-4 mb-3";

        col.innerHTML = `
            <div class="card shadow-sm h-100 border-1">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="h5 mb-0 fw-bold text-secondary">PC: ${pcId}</h4>
                        <span id="badge-${pcId}" class="badge ${tieneBorrador ? 'bg-danger' : 'bg-secondary'}" style="cursor:pointer;">
                            ${tieneBorrador ? 'Incidencia Pendiente' : 'Sin reportes'}
                        </span>
                    </div>
                    <div class="bg-light p-2 rounded-3 d-flex justify-content-around">
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="radio" name="estado-${pcId}" id="ok-${pcId}" value="ok" ${!tieneBorrador ? 'checked' : ''}>
                            <label class="form-check-label text-success fw-semibold" for="ok-${pcId}">Sin problemas</label>
                        </div>
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="radio" name="estado-${pcId}" id="inc-${pcId}" value="incidencia" ${tieneBorrador ? 'checked' : ''}>
                            <label class="form-check-label text-danger fw-semibold" for="inc-${pcId}">Hay incidencia</label>
                        </div>
                    </div>
                </div>
            </div>
        `;

        contenedorEquipos.appendChild(col);

        const radioOk = col.querySelector(`#ok-${pcId}`);
        const radioInc = col.querySelector(`#inc-${pcId}`);
        const badgeElement = col.querySelector(`#badge-${pcId}`);

        radioOk.addEventListener("change", () => {
            if (incidenciasTemporales[pcId] !== undefined) {
                delete incidenciasTemporales[pcId];
                badgeElement.textContent = "Sin reportes";
                badgeElement.className = "badge bg-secondary";
            }
        });

        radioInc.addEventListener("change", () => {
            abrirFormularioModal(pcId);
        });

        badgeElement.addEventListener("click", () => {
            if (incidenciasTemporales[pcId] !== undefined) {
                abrirFormularioModal(pcId);
            }
        });
    });
};

const abrirFormularioModal = (pcId) => {
    pcActualId = pcId;
    tituloModal.textContent = `Registro de incidencia - PC: ${pcId}`;

    const datosPrevios = incidenciasTemporales[pcId];

    if (datosPrevios !== undefined) {
        inputTipo.value = datosPrevios.tipo;
        inputAsunto.value = datosPrevios.asunto;
        inputPersona.value = datosPrevios.persona;
        txtDescripcion.value = datosPrevios.descripcion;
        
        const radioGravedad = modalIncidencia.querySelector(`input[name="gravedad"][value="${datosPrevios.gravedad}"]`);
        if (radioGravedad) {
            radioGravedad.checked = true;
        }
    } else {
        inputTipo.value = "";
        inputAsunto.value = "";
        inputPersona.value = "";
        txtDescripcion.value = "";
        
        const gravedades = modalIncidencia.querySelectorAll('input[name="gravedad"]');
        gravedades.forEach(radio => {
            radio.checked = false;
        });
    }

    modalIncidencia.classList.remove("oculto");
    modalIncidencia.classList.add("d-flex"); 
};

const cerrarFormularioModal = () => {
    modalIncidencia.classList.remove("d-flex");
    modalIncidencia.classList.add("oculto");
    pcActualId = null;
};


btnAceptarModal.addEventListener("click", () => {
    const tipo = inputTipo.value;
    const asunto = inputAsunto.value.trim();
    const persona = inputPersona.value.trim();
    const descripcion = txtDescripcion.value.trim();
    
    const gravedadRadio = modalIncidencia.querySelector('input[name="gravedad"]:checked');

    if (tipo === "" || asunto === "" || persona === "" || descripcion === "" || !gravedadRadio) {
        alert("Por favor, complete todos los campos del formulario de incidencias.");
        return;
    }

    incidenciasTemporales[pcActualId] = {
        tipo: tipo,
        asunto: asunto,
        persona: persona,
        gravedad: gravedadRadio.value,
        descripcion: descripcion
    };

    const badge = document.getElementById(`badge-${pcActualId}`);
    if (badge) {
        badge.textContent = "Incidencia Pendiente";
        badge.className = "badge bg-danger";
    }

    cerrarFormularioModal();
});

btnCancelarModal.addEventListener("click", () => {
    if (pcActualId && incidenciasTemporales[pcActualId] === undefined) {
        const radioOk = document.getElementById(`ok-${pcActualId}`);
        if (radioOk) {
            radioOk.checked = true;
        }
    }
    cerrarFormularioModal();
});

const registrarEnHistorialSistema = (descripcion, detalle) => {
    const datosHistorial = localStorage.getItem("registroTickets");
    let listaHistorial = [];
    
    if (datosHistorial !== null && datosHistorial !== undefined && datosHistorial !== "") {
        listaHistorial = JSON.parse(datosHistorial);
    }

    const nuevoRegistro = {
        id: listaHistorial.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'),
        descripcionAccion: descripcion,
        detalleOperador: detalle
    };

    listaHistorial.push(nuevoRegistro);
    localStorage.setItem("registroTickets", JSON.stringify(listaHistorial));
};


formularioSalon.addEventListener("submit", (e) => {
    e.preventDefault();

    const idSalonSeleccionado = selectSector.value;
    if (idSalonSeleccionado === "") {
        alert("Por favor, seleccione un salón para proceder.");
        return;
    }

    const confirmacion = confirm("¿Está seguro de enviar los datos de las incidencias del salón al sistema?");
    if (!confirmacion) return;

    const llavesIncidencias = Object.keys(incidenciasTemporales);

    if (llavesIncidencias.length > 0) {
        const datosTickets = localStorage.getItem("tickets");
        let listaTickets = [];
        if (datosTickets !== null && datosTickets !== undefined && datosTickets !== "") {
            listaTickets = JSON.parse(datosTickets);
        }

        let contadorID = listaTickets.length;

        const usuarioSesion = localStorage.getItem("usuario");
        let docenteId = "Docente Generico";
        if (usuarioSesion) {
            const uObj = JSON.parse(usuarioSesion);
            docenteId = uObj.usuario || uObj.cedula || docenteId;
        }

        const textoSalon = selectSector.options[selectSector.selectedIndex].text;

        llavesIncidencias.forEach(pcId => {
            contadorID = contadorID + 1;
            const info = incidenciasTemporales[pcId];

            const nuevoTicket = {
                id: contadorID,
                docente: docenteId,
                fechaCreacion: new Date().toLocaleDateString('es-ES'),
                salon: textoSalon,
                equipoId: pcId,
                tipo: info.tipo,
                asunto: info.asunto,
                usuarioPc: info.persona,
                gravedad: info.gravedad,
                descripcion: info.descripcion,
                estado: "pendiente",
                colaboradores: [],
                comentarios: [],
                justificacion: "",
                resuelto: false
            };

            listaTickets.push(nuevoTicket);

            const descripcionHistorial = info.asunto; 
            const detalleHistorial = `El docente ${docenteId} registro una incidencia sobre la PC: ${pcId} del ${textoSalon}`;
            
            registrarEnHistorialSistema(descripcionHistorial, detalleHistorial);
        });

        localStorage.setItem("tickets", JSON.stringify(listaTickets));
        alert(`Operación exitosa. Se registraron ${llavesIncidencias.length} tickets de incidencia.`);
    } else {
        alert("El estado del salón se ha registrado con éxito sin novedades.");
    }

    formularioSalon.reset();
    incidenciasTemporales = {};
    contenedorEquipos.innerHTML = "";
});

selectSector.addEventListener("change", (e) => {
    incidenciasTemporales = {}; 
    renderizarEquiposDelSalon(e.target.value);
});

popularSelectSalones();
contenedorEquipos.innerHTML = "";