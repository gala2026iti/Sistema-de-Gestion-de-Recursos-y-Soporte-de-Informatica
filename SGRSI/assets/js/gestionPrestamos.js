const formulario = document.getElementById("formPrestamo")
const btnRegistrarPrestamo = document.getElementById("btnRegistrarPrestamo")
const modalPrestamo = document.getElementById("modalPrestamo")
const btnCancelarPrestamo = document.getElementById("btnCancelarPrestamo")
const opciones = document.getElementById("listaDispositivos")
const cuerpoTabla = document.querySelector("#tablaPrestamos tbody")

const obtenerCedulaLocal = () => {
    const usuario = localStorage.getItem("usuario")
    if (usuario === null || usuario === undefined || usuario === "") return "N/A"
    const usuarioLocalJSON = JSON.parse(usuario) 
    return usuarioLocalJSON.usuario

}

const cargarSalones = () => {
    const salones = localStorage.getItem("salones")
    if (salones === null || salones === undefined || salones === "") return []
    return JSON.parse(salones)

}

const cargarEspacios = () => {
    const salones = cargarSalones()
    const salonPrestamos = salones.find(salon =>
        salon.tipo.toLowerCase() === "prestamo"
    )

    if (salonPrestamos) {
        return salonPrestamos.espacios || []
    }
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

const registrarHistorial = (modificacion, ciPrestador, ciPrestado, nombrePrestado, idEquipo) => {
    const historial = localStorage.getItem("registroPrestamos")
    const lista = historial ? JSON.parse(historial) : []

    const nuevaAccion = {
        id: lista.length + 1,
        ciPrestador: ciPrestador,
        ciPrestado: ciPrestado,
        nombrePrestado: nombrePrestado,
        modificacion: modificacion,
        idEquipo: idEquipo,
        fecha: new Date().toLocaleDateString('es-ES'),
        hora: new Date().toLocaleTimeString('es-ES')
    }

    lista.push(nuevaAccion)
    localStorage.setItem("registroPrestamos", JSON.stringify(lista))
}

const guardarSalonPrestamo = (salon) => {
    const salones = cargarSalones()
    const salonesActulizados = []
    salones.forEach(s => {
        if (s.tipo === "prestamo") s = salon
        salonesActulizados.push(s)
    })

    localStorage.setItem("salones", JSON.stringify(salonesActulizados))

}

const modificarPrestamoEquipo = (idEq, booleano) => {
    const salones = cargarSalones()
    const salonPrestamos = cargarSalones().find(s => s.tipo === "prestamo")
    const equiposPrestamo = salonPrestamos.espacios
    const equiposPrestamoModificados = []
    equiposPrestamo.forEach(e => {
        if (String(e.id) === String(idEq)) e.prestado = booleano
        equiposPrestamoModificados.push(e)
    })
    salonPrestamos.espacios = equiposPrestamoModificados

    guardarSalonPrestamo(salonPrestamos)

}
const usuarioValido = (cedula) => {
    return (cedula.trim() >= 10000000 && cedula.trim() <= 99999999)
}

const equipoPrestable = (idEquipo) => {
    const salonPrestamos = cargarSalones().find(s => s.tipo === "prestamo")
    const equiposPrestamo = salonPrestamos.espacios

    const equipoEncontrado = equiposPrestamo.find(e => String(e.id) === String(idEquipo))

    if (!equipoEncontrado || equipoEncontrado.prestado === true) {
        return false
    }

    const todosLosEquipos = JSON.parse(localStorage.getItem("equipos") || "[]")
    const infoGlobal = todosLosEquipos.find(eq => String(eq.id) === String(idEquipo))
    if (infoGlobal && infoGlobal.activo === false) {
        return false
    }
    const todosLosTickets = JSON.parse(localStorage.getItem("tickets") || "[]")
    const tieneTicketAbierto = todosLosTickets.some(t =>
        String(t.equipoId) === String(idEquipo) &&
        (t.estado === "pendiente" || t.estado === "en proceso")
    )
    if (tieneTicketAbierto) {
        return false
    }

    return true
}

const opcionesDispositivos = () => {
    if (!opciones) return
    opciones.innerHTML = ""

    const seleccionDefault = document.createElement("option")
    seleccionDefault.value = ""
    seleccionDefault.innerText = "Seleccione un dispositivo"
    opciones.appendChild(seleccionDefault)

    let equipos = cargarEspacios()
    if(!equipos) equipos = []
    equipos.forEach(eq => {
        if (equipoPrestable(eq.id)) {
            const idReal = eq.id
            const opt = document.createElement("option")
            opt.value = idReal
            opt.innerText = `PC ID: ${idReal}`
            opciones.appendChild(opt)
        }
    })
}


const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    const lista = cargarPrestamos()

    lista.forEach(p => {
        if (!p.devuelto) {
            const tr = document.createElement("tr")

            const tdId = document.createElement("td")
            tdId.innerText = p.id

            const tdciPrestador = document.createElement("td")
            tdciPrestador.innerText = p.ciPrestador

            const tdCedula = document.createElement("td")
            tdCedula.innerText = p.ciPrestado

            const tdNombre = document.createElement("td")
            tdNombre.innerText = p.nombrePrestado

            const tdEquipo = document.createElement("td")
            tdEquipo.innerText = p.idEquipo
            ""
            const tdFechaI = document.createElement("td")
            tdFechaI.innerText = new Date(p.fechaInicio).toLocaleDateString('es-ES')

            const tdFechaD = document.createElement("td")
            tdFechaD.innerText = new Date(p.fechaDevolucion).toLocaleDateString('es-ES')

            const tdEstado = document.createElement("td")
            tdEstado.innerText = p.devuelto ? "Devuelto" : "Activo"

            const tdAcciones = document.createElement("td")
            if (!p.devuelto) {
                const btnDevolver = document.createElement("button")
                btnDevolver.className = "btn btn-success btn-sm"
                btnDevolver.innerText = "Devolver"
                btnDevolver.addEventListener("click", () => {
                    if (!confirm("¿Estas seguro de que deseas finalizar este préstamo?\nEsta opción no se puede deshacer"))
                        p.devuelto = true

                    const todos = cargarPrestamos()
                    const index = todos.findIndex(t => t.id === p.id)
                    const ciPrestador = obtenerCedulaLocal()


                    if (index !== -1) {
                        todos[index].devuelto = true
                        localStorage.setItem("prestamos", JSON.stringify(todos))
                        modificarPrestamoEquipo(p.idEquipo, false)
                        registrarHistorial("devolucion", ciPrestador, p.ciPrestado, p.nombrePrestado, p.idEquipo)

                        actualizarTabla()
                    }
                })
                tdAcciones.appendChild(btnDevolver)
            }

            tr.appendChild(tdId)
            tr.appendChild(tdciPrestador)
            tr.appendChild(tdCedula)
            tr.appendChild(tdNombre)
            tr.appendChild(tdEquipo)
            tr.appendChild(tdFechaI)
            tr.appendChild(tdFechaD)
            tr.appendChild(tdEstado)
            tr.appendChild(tdAcciones)

            cuerpoTabla.appendChild(tr)
        }
    })

}

//EVENTOS
if (btnRegistrarPrestamo) {
    btnRegistrarPrestamo.addEventListener("click", () => {
        opcionesDispositivos()
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

        const inputciPrestado = document.getElementById("ciPrestado")
        const inputNombrePrestado = document.getElementById("nombrePrestado")
        const inputEquipoElegido = document.getElementById("listaDispositivos")
        const inputFechaDevolucion = document.getElementById("final")

        const fechaSeleccionada = new Date(inputFechaDevolucion.value)
        const fechaActual = new Date()

        if (fechaSeleccionada <= fechaActual) {
            alert("Error: La fecha de devolución debe ser posterior a la fecha y hora actual.")
            return
        }

        const contadorID = () => {
            let prestamos = cargarPrestamos()
            contador = prestamos.length
            if (contador === null || contador === undefined || contador === "") {
                contador = 0
            } else {
                contador = contador + 1
            }
            return contador
        }

        const ciPrestador = obtenerCedulaLocal() || "N/A"

        const prestamo = {
            id: contadorID(),
            ciPrestado: inputciPrestado.value.trim(),
            nombrePrestado: inputNombrePrestado.value.trim(),
            idEquipo: inputEquipoElegido.value,
            ciPrestador: ciPrestador,
            fechaInicio: new Date().toISOString(),
            fechaDevolucion: inputFechaDevolucion.value,
            devuelto: false,
            entregaAtrasada: false
        }

        if (usuarioValido(prestamo.ciPrestado)) {
            guardarPrestamo(prestamo)
            registrarHistorial("prestamo", ciPrestador, prestamo.ciPrestado, prestamo.nombrePrestado, prestamo.idEquipo)

            alert("Préstamo registrado con éxito.")
            modificarPrestamoEquipo(prestamo.idEquipo, true)
            formulario.reset()
            modalPrestamo.classList.replace("d-flex", "d-none")
            actualizarTabla()
        } else {
            alert("Error: Usuario no válido.")
        }
    })
}

actualizarTabla()