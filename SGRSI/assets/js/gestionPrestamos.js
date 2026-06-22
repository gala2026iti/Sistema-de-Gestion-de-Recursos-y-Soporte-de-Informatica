const formulario = document.getElementById("formPrestamo")
const btnRegistrarPrestamo = document.getElementById("btnRegistrarPrestamo")
const modalPrestamo = document.getElementById("modalPrestamo")
const btnCancelarPrestamo = document.getElementById("btnCancelarPrestamo")
const opciones = document.getElementById("listaDispositivos")
const tabla = document.getElementById("tablaPrestamos")
const cuerpoTabla = tabla.querySelector("tbody")

const obtenerCedulaOperadorActivo = () => {
    const usuario = localStorage.getItem("usuario")
    if (!usuario) return "Desconocido"
    
    try {
        const usuarioJSON = JSON.parse(usuario)
        return usuarioJSON.usuario || usuarioJSON.cedula || "Desconocido"
    } catch (error) {
        console.error("Error al parsear el usuario de la sesión:", error)
        return "Desconocido"
    }
}

const cargarSalones = () => {
    const salones = localStorage.getItem("salones")
    if (salones === null || salones === undefined || salones === "") {
        return []
    } else {
        return JSON.parse(salones)
    }
}

const cargarEspacios = () => {
    const salones = cargarSalones()
    const salonPrestamos = salones.find(salon => 
        String(salon.tipo).toLowerCase() === "prestamo"
    )
    
    if (salonPrestamos) {
        return salonPrestamos.prestamos || salonPrestamos.espacios || []
    }
    return []
}

const cargarPrestamos = () => {
    const prestamos = localStorage.getItem("prestamos")
    if (prestamos === null || prestamos === undefined || prestamos === "") {
        return []
    } else {
        return JSON.parse(prestamos)
    }
}

const guardarPrestamo = (prestamo) => {
    const lista = cargarPrestamos()
    lista.push(prestamo)
    localStorage.setItem("prestamos", JSON.stringify(lista))
}

const registrarAccion = (tipoAccion, operador, cedula, nombre, idEquipo) => {
    const historial = localStorage.getItem("registroTickets")
    const lista = historial ? JSON.parse(historial) : []

    const nuevaAccion = {
        id: lista.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'),
        descripcionAccion: `Registro de ${tipoAccion}`,
        detalleOperador: `El operador ${operador} realizó un préstamo al usuario ${nombre} (C.I: ${cedula}) del equipo con ID: ${idEquipo}`
    }

    lista.push(nuevaAccion)
    localStorage.setItem("registroTickets", JSON.stringify(lista))
}

const usuarioValido = (cedula) => {
    const usuarios = localStorage.getItem("usuarios")
    if (!usuarios) return true
    const lista = JSON.parse(usuarios)
    return lista.some(u => String(u.cedula) === String(cedula))
}

const popularSelectDispositivos = () => {
    if (!opciones) return
    opciones.innerHTML = ""

    const defaultOpt = document.createElement("option")
    defaultOpt.value = ""
    defaultOpt.appendChild(document.createTextNode("Seleccione un dispositivo"))
    opciones.appendChild(defaultOpt)

    const equipos = cargarEspacios()
    equipos.forEach(eq => {
        const idReal = typeof eq === "object" && eq !== null ? (eq.id || eq.codigo) : String(eq)
        const opt = document.createElement("option")
        opt.value = idReal
        opt.appendChild(document.createTextNode(`Dispositivo ID: ${idReal}`))
        opciones.appendChild(opt)
    })
}

const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    const lista = cargarPrestamos()

    lista.forEach(p => {
        const tr = document.createElement("tr")

        const tdId = document.createElement("td")
        tdId.appendChild(document.createTextNode(p.id))

        const tdCedula = document.createElement("td")
        tdCedula.appendChild(document.createTextNode(p.cedulaPrestado))

        const tdNombre = document.createElement("td")
        tdNombre.appendChild(document.createTextNode(p.nombrePrestado))

        const tdEquipo = document.createElement("td")
        tdEquipo.appendChild(document.createTextNode(p.idEquipo))

        const tdFechaI = document.createElement("td")
        tdFechaI.appendChild(document.createTextNode(new Date(p.fechaInicio).toLocaleDateString('es-ES')))

        const tdFechaD = document.createElement("td")
        tdFechaD.appendChild(document.createTextNode(new Date(p.fechaDevolucion).toLocaleDateString('es-ES')))

        const tdEstado = document.createElement("td")
        tdEstado.appendChild(document.createTextNode(p.devuelto ? "Devuelto" : "Activo"))

        const tdAcciones = document.createElement("td")
        if (!p.devuelto) {
            const btnDevolver = document.createElement("button")
            btnDevolver.className = "btn btn-success btn-sm"
            btnDevolver.appendChild(document.createTextNode("Devolver"))
            btnDevolver.addEventListener("click", () => {
                p.devuelto = true
                const todos = cargarPrestamos()
                const index = todos.findIndex(t => t.id === p.id)
                if (index !== -1) {
                    todos[index].devuelto = true
                    localStorage.setItem("prestamos", JSON.stringify(todos))
                    actualizarTabla()
                }
            })
            tdAcciones.appendChild(btnDevolver)
        }

        tr.appendChild(tdId)
        tr.appendChild(tdCedula)
        tr.appendChild(tdNombre)
        tr.appendChild(tdEquipo)
        tr.appendChild(tdFechaI)
        tr.appendChild(tdFechaD)
        tr.appendChild(tdEstado)
        tr.appendChild(tdAcciones)

        cuerpoTabla.appendChild(tr)
    })
}

if (btnRegistrarPrestamo) {
    btnRegistrarPrestamo.addEventListener("click", () => {
        popularSelectDispositivos()
        modalPrestamo.classList.replace("d-none", "d-flex")
    })
}

if (btnCancelarPrestamo) {
    btnCancelarPrestamo.addEventListener("click", () => {
        formulario.reset()
        modalPrestamo.classList.replace("d-flex", "d-none")
    })
}

if (formulario) {
    formulario.addEventListener("submit", (e) => {
        e.preventDefault()

        const inputCedulaPrestado = document.getElementById("cedulaPrestado")
        const inputNombrePrestado = document.getElementById("nombrePrestado")
        const inputEquipoElegido = document.getElementById("listaDispositivos")
        const inputFechaDevolucion = document.getElementById("fechaDevolucion")

        const fechaSeleccionada = new Date(inputFechaDevolucion.value)
        const fechaActual = new Date()

        if (fechaSeleccionada <= fechaActual) {
            alert("La fecha de devolución debe ser posterior a la fecha y hora actual.")
            return
        }

        const contadorID = () => {
            let contador = localStorage.getItem("contador")
            if (contador === null || contador === undefined || contador === "") {
                contador = 1
            } else {
                contador = parseInt(contador) + 1
            }
            localStorage.setItem("contador", contador)
            return contador
        }

        const cedulaOperador = obtenerCedulaOperadorActivo()

        const prestamo = {
            id: contadorID(),
            cedulaPrestado: inputCedulaPrestado.value.trim(),
            nombrePrestado: inputNombrePrestado.value.trim(),
            idEquipo: inputEquipoElegido.value,
            cedulaPrestador: cedulaOperador, 
            fechaInicio: new Date().toISOString(),
            fechaDevolucion: inputFechaDevolucion.value,
            devuelto: false,
            entregaAtrasada: false
        }

        if (usuarioValido(prestamo.cedulaPrestado)) {
            guardarPrestamo(prestamo)
            registrarAccion("prestamo", cedulaOperador, prestamo.cedulaPrestado, prestamo.nombrePrestado, prestamo.idEquipo)

            alert("Préstamo registrado con éxito.")
            formulario.reset()
            modalPrestamo.classList.replace("d-flex", "d-none")
            actualizarTabla()
        } else {
            alert("Usuario no válido.")
        }
    })
}

actualizarTabla()