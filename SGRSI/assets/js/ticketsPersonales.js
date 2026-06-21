const columnaPendiente = document.getElementById("columna-pendiente");
const columnaEnProceso = document.getElementById("columna-en-proceso");
const columnaResuelto = document.getElementById("columna-resuelto");

const obtenerOperadorLogueado = () => {
    const sesion = localStorage.getItem("usuario");
    if (sesion) {
        return JSON.parse(sesion);
    }
    return null;
};

const cargarTicketsGlobales = () => {
    const datos = localStorage.getItem("tickets");
    if (!datos) return [];
    return JSON.parse(datos);
};

const renderizarTableroKanban = () => {
    columnaPendiente.innerHTML = "";
    columnaEnProceso.innerHTML = "";
    columnaResuelto.innerHTML = "";

    const usuario = obtenerOperadorLogueado();
    const idUsuarioActual = usuario ? (usuario.usuario || "Técnico Genérico") : "Administrador Técnico";
    
    const todosLosTickets = cargarTicketsGlobales();

    const ticketsResueltosUsuario = [];

    todosLosTickets.forEach(ticket => {
        if (ticket.colaboradores && ticket.colaboradores.includes(idUsuarioActual)) {
            const estadoLimpio = String(ticket.estado).toLowerCase();

            if (ticket.resuelto === true || estadoLimpio === "resuelto") {
                ticketsResueltosUsuario.push(ticket);
            } else {
                const tr = document.createElement("tr");
                const td = document.createElement("td");
                td.className = "ticket-marcado";

                const enlace = document.createElement("a");
                enlace.href = `detalleTicket.html?id=${ticket.id}`;
                enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2";
                
                const textoAsunto = document.createTextNode(ticket.asunto);
                enlace.appendChild(textoAsunto);
                td.appendChild(enlace);
                tr.appendChild(td);

                if (estadoLimpio === "en proceso") {
                    columnaEnProceso.appendChild(tr);
                } else {
                    columnaPendiente.appendChild(tr);
                }
            }
        }
    });

    ticketsResueltosUsuario.sort((a, b) => Number(b.id) - Number(a.id));

    const ultimosDiezResueltos = ticketsResueltosUsuario.slice(0, 10);

    ultimosDiezResueltos.forEach(ticket => {
        const tr = document.createElement("tr");
        const td = document.createElement("td");
        td.className = "ticket-marcado";

        const enlace = document.createElement("a");
        enlace.href = `detalleTicket.html?id=${ticket.id}`;
        enlace.className = "text-decoration-none text-dark d-block w-100 h-100 py-2";
        
        const textoAsunto = document.createTextNode(ticket.asunto);
        enlace.appendChild(textoAsunto);
        td.appendChild(enlace);
        tr.appendChild(td);

        columnaResuelto.appendChild(tr);
    });
};

renderizarTableroKanban();