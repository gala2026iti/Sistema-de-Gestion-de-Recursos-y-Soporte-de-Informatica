const txtTituloTicket = document.getElementById("txt-titulo-ticket");
const txtIdTicket = document.getElementById("txt-id-ticket");
const txtMetaCreacion = document.getElementById("txt-meta-creacion");

const badgePendiente = document.getElementById("badge-pendiente");
const badgeEnProceso = document.getElementById("badge-en-proceso");
const badgeResuelto = document.getElementById("badge-resuelto");

const inputUsuarioAsignado = document.getElementById("usuario-asignado"); 
const btnAutoasignar = document.getElementById("btn-autoasignar");

const selectEstado = document.getElementById("select-estado"); 
const selectGravedad = document.getElementById("select-gravedad");
const inputSalon = document.getElementById("input-salon");
const inputPc = document.getElementById("input-pc");
const inputCategoria = document.getElementById("input-categoria");
const textareaContenido = document.getElementById("textarea-contenido");

const contenedorJustificacion = document.getElementById("contenedor-justificacion");
const textareaJustificacion = document.getElementById("textarea-justificacion");

const formControlTicket = document.getElementById("form-control-ticket");
const btnFinalizarTicket = document.getElementById("btn-finalizar-ticket");

const contenedorComentarios = document.getElementById("contenedor-comentarios");
const txtNuevoComentario = document.getElementById("txt-nuevo-comentario");
const btnGuardarComentario = document.getElementById("btn-guardar-comentario");

const queryParams = new URLSearchParams(window.location.search);
const ticketIdActual = queryParams.get("id");

const obtenerUsuarioFirmado = () => {
    const sesion = localStorage.getItem("usuario");
    if (sesion) return JSON.parse(sesion);
    return null;
};

const cargarColeccionTickets = () => {
    const datos = localStorage.getItem("tickets");
    if (!datos) return [];
    return JSON.parse(datos);
};

const persistirColeccionTickets = (lista) => {
    localStorage.setItem("tickets", JSON.stringify(lista));
};

const registrarEnHistorialGeneral = (asunto, detalle) => {
    const datos = localStorage.getItem("registroTickets");
    let historial = datos ? JSON.parse(datos) : [];
    
    historial.push({
        id: historial.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'),
        descripcionAccion: asunto,
        detalleOperador: detalle
    });
    localStorage.setItem("registroTickets", JSON.stringify(historial));
};

const renderizarInformacionTicket = () => {
    const lista = cargarColeccionTickets();
    const ticket = lista.find(t => String(t.id) === String(ticketIdActual));

    if (!ticket) {
        txtTituloTicket.textContent = "Ticket no encontrado";
        return;
    }

    txtTituloTicket.textContent = ticket.asunto + " ";
    const spanId = document.createElement("span");
    spanId.className = "fw-bold";
    spanId.textContent = `#${ticket.id}`;
    txtTituloTicket.appendChild(spanId);

    txtMetaCreacion.textContent = "";
    const txtDocenteNegrita = document.createElement("span");
    txtDocenteNegrita.className = "fw-bold";
    txtDocenteNegrita.textContent = ticket.docente || "Sistema";
    txtMetaCreacion.appendChild(txtDocenteNegrita);
    txtMetaCreacion.appendChild(document.createTextNode(` abrió esta incidencia el `));
    const txtFechaNegrita = document.createElement("span");
    txtFechaNegrita.className = "fw-bold";
    txtFechaNegrita.textContent = ticket.fechaCreacion || "N/A";
    txtMetaCreacion.appendChild(txtFechaNegrita);

    badgePendiente.className = "badge bg-secondary px-2 py-1 fs-6";
    badgeEnProceso.className = "badge bg-secondary px-2 py-1 fs-6";
    badgeResuelto.className = "badge bg-secondary px-2 py-1 fs-6";

    const estadoLimpio = String(ticket.estado).toLowerCase();
    
    if (ticket.resuelto === true || estadoLimpio === "resuelto") {
        badgeResuelto.className = "badge bg-success px-2 py-1 fs-6";
        btnFinalizarTicket.style.setProperty("display", "none", "important"); 
        if (selectEstado) selectEstado.disabled = true;
        
        if (contenedorJustificacion) contenedorJustificacion.classList.remove("d-none");
        if (textareaJustificacion) textareaJustificacion.value = ticket.justificacion || "Sin justificación registrada.";
    } else if (estadoLimpio === "en proceso") {
        badgeEnProceso.className = "badge bg-warning px-2 py-1 fs-6";
        btnFinalizarTicket.style.setProperty("display", "block", "important"); 
        if (selectEstado) selectEstado.disabled = false;
        if (contenedorJustificacion) contenedorJustificacion.classList.add("d-none");
    } else {
        badgePendiente.className = "badge bg-danger px-2 py-1 fs-6";
        btnFinalizarTicket.style.setProperty("display", "none", "important"); 
        if (selectEstado) selectEstado.disabled = false;
        if (contenedorJustificacion) contenedorJustificacion.classList.add("d-none");
    }

    const encargados = ticket.colaboradores && ticket.colaboradores.length > 0 
        ? ticket.colaboradores.join(", ") 
        : "Ninguno - Sin asignar";
    
    inputUsuarioAsignado.value = encargados;
    if (selectEstado) selectEstado.value = estadoLimpio;
    selectGravedad.value = String(ticket.gravedad).toLowerCase();
    inputSalon.value = ticket.salon || "No especificado";
    inputPc.value = ticket.equipoId || "N/A";
    inputCategoria.value = ticket.tipo || "General";
    textareaContenido.value = ticket.descripcion || "";

    contenedorComentarios.innerHTML = "";
    const comentarios = ticket.comentarios || [];
    
    comentarios.forEach(com => {
        const articulo = document.createElement("article");
        articulo.className = "card mb-4 shadow-sm";

        const divHeader = document.createElement("div");
        divHeader.className = "card-header d-flex justify-content-between align-items-center bg-light";
        
        const h3User = document.createElement("h3");
        h3User.className = "h6 m-0 fw-bold text-secondary";
        h3User.textContent = com.autor;

        const spanTiempo = document.createElement("span");
        spanTiempo.className = "text-muted small";
        spanTiempo.textContent = `el ${com.fecha}`;

        divHeader.appendChild(h3User);
        divHeader.appendChild(spanTiempo);

        const divBody = document.createElement("div");
        divBody.className = "card-body";
        
        const pContenido = document.createElement("p");
        pContenido.className = "card-text text-dark";
        pContenido.textContent = com.texto;

        divBody.appendChild(pContenido);
        articulo.appendChild(divHeader);
        articulo.appendChild(divBody);

        contenedorComentarios.appendChild(articulo);
    });
};

btnAutoasignar.addEventListener("click", () => {
    const usuarioLogueado = obtenerUsuarioFirmado();
    const idUsuarioActual = usuarioLogueado ? (usuarioLogueado.usuario || "Técnico Genérico") : "Administrador Técnico";

    const lista = cargarColeccionTickets();
    const ticket = lista.find(t => String(t.id) === String(ticketIdActual));

    if (ticket) {
        if (!ticket.colaboradores) ticket.colaboradores = [];

        if (ticket.colaboradores.includes(idUsuarioActual)) {
            alert("Ya te encuentras asignado a este ticket.");
            return;
        }

        ticket.colaboradores.push(idUsuarioActual);

        persistirColeccionTickets(lista);
        renderizarInformacionTicket();
        registrarEnHistorialGeneral(ticket.asunto, `${idUsuarioActual} se unió como colaborador.`);
        alert("Te has asignado exitosamente.");
    }
});

formControlTicket.addEventListener("submit", (e) => {
    e.preventDefault();
    const lista = cargarColeccionTickets();
    const ticket = lista.find(t => String(t.id) === String(ticketIdActual));

    if (ticket) {
        const usuarioLogueado = obtenerUsuarioFirmado();
        const idUsuarioActual = usuarioLogueado ? (usuarioLogueado.usuario || "Técnico Genérico") : "Administrador Técnico";

        if (selectEstado) ticket.estado = selectEstado.value;
        ticket.gravedad = selectGravedad.value;
        
        persistirColeccionTickets(lista);
        renderizarInformacionTicket();
        registrarEnHistorialGeneral(ticket.asunto, `${idUsuarioActual} actualizó el estado a ${ticket.estado.toUpperCase()} y la gravedad a ${selectGravedad.value.toUpperCase()}.`);
        alert("Cambios guardados con éxito.");
    }
});

btnFinalizarTicket.addEventListener("click", () => {
    const justificacionPrevia = prompt("Por favor, introduzca una justificación detallada de cómo se resolvió la incidencia:");
    
    if (justificacionPrevia === null) return; 
    
    const justificacionLimpia = justificacionPrevia.trim();
    if (justificacionLimpia === "") {
        alert("Operación cancelada. La justificación de cierre es obligatoria y no puede enviarse vacía.");
        return;
    }

    const lista = cargarColeccionTickets();
    const ticket = lista.find(t => String(t.id) === String(ticketIdActual));

    if (ticket) {
        const usuarioLogueado = obtenerUsuarioFirmado();
        const idUsuarioActual = usuarioLogueado ? (usuarioLogueado.usuario || "Técnico Genérico") : "Administrador Técnico";

        ticket.resuelto = true;
        ticket.estado = "resuelto";
        ticket.justificacion = justificacionLimpia; 

        persistirColeccionTickets(lista);
        renderizarInformacionTicket();
        registrarEnHistorialGeneral(ticket.asunto, `${idUsuarioActual} finalizó el ticket. Resolución: ${justificacionLimpia}`);
        alert("Ticket finalizado y cerrado con éxito.");
    }
});

btnGuardarComentario.addEventListener("click", () => {
    const textoComentario = txtNuevoComentario.value.trim();
    if (textoComentario === "") {
        alert("El comentario no puede estar vacío.");
        return;
    }

    const lista = cargarColeccionTickets();
    const ticket = lista.find(t => String(t.id) === String(ticketIdActual));

    if (ticket) {
        const usuarioLogueado = obtenerUsuarioFirmado();
        const idUsuarioActual = usuarioLogueado ? (usuarioLogueado.usuario || "Técnico Genérico") : "Administrador Técnico";

        if (!ticket.comentarios) ticket.comentarios = [];

        ticket.comentarios.push({
            autor: idUsuarioActual,
            fecha: new Date().toLocaleDateString('es-ES'),
            texto: textoComentario
        });

        persistirColeccionTickets(lista);
        txtNuevoComentario.value = "";
        renderizarInformacionTicket();
        alert("Comentario registrado.");
    }
});

renderizarInformacionTicket();