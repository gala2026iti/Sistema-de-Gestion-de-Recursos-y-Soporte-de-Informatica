// VARIABLES
const contenedorGeneral = document.getElementById("contenedor-historial")

// FUNCIONES
const cargarHistorial = () => {
    const historial = localStorage.getItem("registrosPrestamos")
    if (historial === null || historial === undefined || historial === "") {
        return []
    } else {
        return JSON.parse(historial)
    }
}

const encontrarNombre = (cedula) => {
    const usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") {
        return "Desconocido"
    } else {
        const usuariosJSON = JSON.parse(usuarios)
        const usuario = usuariosJSON.find(u => u.usuario === cedula)
        return usuario.nombre
    }
}

const mostrarHistorial = () => {
    contenedorGeneral.innerHTML = ""
    const historial = cargarHistorial()
    let ultimaFecha

    historial.forEach(registro => {
        const equipoInvolucrado = registro.idDispositivo
        const personaPrestada = registro.afectado
        const nombrePersonaPrestada = registro.nombreAfectado
        const registradorID = registro.responsable
        const registradorNombre = encontrarNombre(registradorID)

        if (ultimaFecha !== registro.fecha) {
            ultimaFecha = registro.fecha

            const fecha = document.createElement("span")
            fecha.innerHTML = `Intervenciones el ${ultimaFecha}`
            fecha.classList.add("fw-bold")

            listaDiaria = document.createElement("ul")
            listaDiaria.className = "historial-lista mt-2 mb-3"

            contenedorGeneral.appendChild(fecha)
            contenedorGeneral.appendChild(listaDiaria)
        }

        const campo = document.createElement("li")
        campo.className = "historial-contenido d-flex justify-content-between align-items-center"

        const contenedor = document.createElement("div")
        contenedor.classList.add("d-flex", "flex-column")

        const mensajePrestamo = document.createElement("span")
        mensajePrestamo.classList.add("fw-bold")

        if (registro.caso === "prestamo") {
            mensajePrestamo.innerHTML = `Se presto el equipo <span class="text-primary">${equipoInvolucrado}</span> a <span class="text-primary">${nombrePersonaPrestada} (${personaPrestada})</span>`
        } else if (registro.caso === "devolucion") {
            mensajePrestamo.innerHTML = `Se finalizo el prestamo del equipo <span class="text-primary">${equipoInvolucrado}</span> de <span class="text-primary">${nombrePersonaPrestada} (${personaPrestada})</span>`
        }

        const registrador = document.createElement("span")
        registrador.classList.add("text-muted")

        if (registro.caso === "prestamo") {
            registrador.innerHTML = `${registradorNombre} (${registradorID}) registro el prestamo`
        } else if (registro.caso === "devolucion") {
            registrador.innerHTML = `${registradorNombre} (${registradorID}) registro la finalización del prestamo`
        }

        const badgeId = document.createElement("span")
        badgeId.className = "badge bg-primary"
        badgeId.textContent = `#${registro.id}`

        contenedor.appendChild(mensajePrestamo)
        contenedor.appendChild(registrador)
        campo.appendChild(contenedor)
        campo.appendChild(badgeId)
        
        if (listaDiaria) {
            listaDiaria.appendChild(campo)
        }
    })
}

mostrarHistorial()