// VARIABLES
const formulario = document.getElementById("formPrestamo")
const btnRegistrarPrestamo = document.getElementById("btnRegistrarPrestamo")
const modalPrestamo = document.getElementById("modalPrestamo")
const btnCancelarPrestamo = document.getElementById("btnCancelarPrestamo")
const opciones = document.getElementById("listaDispositivos")
const tabla = document.getElementById("tablaPrestamos")
const cuerpoTabla = tabla.querySelector("tbody")

// FUNCIONES
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
        String(salon.tipo).toLowerCase() === "prestamo" || String(salon.id) === "1"
    )
    
    if (salonPrestamos) {
        return salonPrestamos.espacios || salonPrestamos.prestamos || []
    }
    return []
}

const cargarLista = () => {
    const espacios = cargarEspacios()
    
    const equiposLibres = espacios.filter(e => {
        const yaPrestado = e.prestado === true || e.prestado === "true"
        return !yaPrestado
    })

    opciones.innerHTML = `<option value="" selected>Elegir equipo</option>`
    
    if (equiposLibres.length === 0) {
        const opcionVacia = document.createElement("option")
        opcionVacia.value = ""
        opcionVacia.textContent = "No hay equipos disponibles"
        opcionVacia.disabled = true
        opciones.appendChild(opcionVacia)
    } else {
        equiposLibres.forEach((equipoLibre) => {
            const opcion = document.createElement("option")
            opcion.value = equipoLibre.id
            opcion.textContent = `Equipo ID: ${equipoLibre.id}`
            opciones.appendChild(opcion)
        })
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

const registrarAccion = (caso, responsable, afectado, nombreAfectado, idDispositivo) => {
    const registros = localStorage.getItem("registrosPrestamos")
    const registrosJSON = registros ? JSON.parse(registros) : []

    const hoy = new Date()
    const dia = String(hoy.getDate()).padStart(2, '0')
    const mes = String(hoy.getMonth() + 1).padStart(2, '0')
    const anio = hoy.getFullYear()

    const nuevoRegistro = {
        id: registrosJSON.length + 1,
        fecha: `${dia}/${mes}/${anio}`,
        caso: caso,
        responsable: responsable,
        afectado: afectado.trim(),
        nombreAfectado: nombreAfectado.trim(),
        idDispositivo: idDispositivo
    }

    registrosJSON.push(nuevoRegistro)
    localStorage.setItem("registrosPrestamos", JSON.stringify(registrosJSON))
}

const actualizarTabla = () => {
    if (!cuerpoTabla) return
    cuerpoTabla.innerHTML = ""
    const prestamos = cargarPrestamos()
    const prestamosActivos = prestamos.filter(prestamo => !prestamo.devuelto)

    prestamosActivos.forEach((prestamo) => {
        const fila = document.createElement("tr")
        const idFila = document.createElement("td")
        const filaPrestador = document.createElement("td")
        const filaPrestado = document.createElement("td")
        const filaNombrePrestado = document.createElement("td")
        const filaEquipo = document.createElement("td")
        const filaFechaInicio = document.createElement("td")
        const filaFechaFinal = document.createElement("td")
        const filaEstado = document.createElement("td")
        const filaAcciones = document.createElement("td")

        idFila.textContent = prestamo.id
        filaPrestador.textContent = prestamo.cedulaPrestador
        filaPrestado.textContent = prestamo.cedulaPrestado
        filaNombrePrestado.textContent = prestamo.nombrePrestado
        filaEquipo.textContent = prestamo.idEquipo

        const fInicio = new Date(prestamo.fechaInicio)
        const fFinal = new Date(prestamo.fechaDevolucion)

        filaFechaInicio.textContent = isNaN(fInicio.getTime()) ? "" : fInicio.toLocaleDateString('es-ES')
        filaFechaFinal.textContent = isNaN(fFinal.getTime()) ? "" : fFinal.toLocaleDateString('es-ES')

        filaEstado.textContent = prestamo.entregaAtrasada ? "Atrasado" : "Activo"

        const botonFinalizar = document.createElement("button")
        botonFinalizar.textContent = "Finalizar"
        botonFinalizar.classList.add("btn", "btn-danger", "btn-sm")

        botonFinalizar.addEventListener("click", () => {
            finalizarPrestamo(prestamo.id)
        })

        filaAcciones.appendChild(botonFinalizar)
        fila.appendChild(idFila)
        fila.appendChild(filaPrestador)
        fila.appendChild(filaPrestado)
        fila.appendChild(filaNombrePrestado)
        fila.appendChild(filaEquipo)
        fila.appendChild(filaFechaInicio)
        fila.appendChild(filaFechaFinal)
        fila.appendChild(filaEstado)
        fila.appendChild(filaAcciones)
        cuerpoTabla.appendChild(fila)
    })
}

const revisarAtrasados = () => {
    const prestamos = cargarPrestamos()
    const fechaActual = new Date()

    prestamos.forEach((prestamo) => {
        const fechaDevolucion = new Date(prestamo.fechaDevolucion)
        if (fechaActual > fechaDevolucion && !prestamo.devuelto) {
            prestamo.entregaAtrasada = true
        }
    })

    localStorage.setItem("prestamos", JSON.stringify(prestamos))
    actualizarTabla()
}

const finalizarPrestamo = (idPrestamo) => {
    if (!confirm("¿Estás seguro de marcar el préstamo como finalizado? La acción no puede deshacerse")) {
        return;
    }

    const prestamos = cargarPrestamos()
    const prestamo = prestamos.find(p => p.id === idPrestamo)

    if (prestamo) {
        prestamo.devuelto = true
        localStorage.setItem("prestamos", JSON.stringify(prestamos))

        const espacios = cargarEspacios()
        const espaciosModificados = espacios.map(e => {
            if (String(e.id) === String(prestamo.idEquipo)) {
                e.prestado = false
            }
            return e
        })

        const salonesEnviar = cargarSalones()
        salonesEnviar.forEach(salon => {
            if (String(salon.tipo).toLowerCase() === "prestamo" || String(salon.id) === "1") {
                if (salon.espacios) salon.espacios = espaciosModificados
                if (salon.prestamos) salon.prestamos = espaciosModificados
            }
        })

        localStorage.setItem("salones", JSON.stringify(salonesEnviar))

        const cedulaOperador = obtenerCedulaOperadorActivo()
        registrarAccion("devolucion", cedulaOperador, prestamo.cedulaPrestado, prestamo.nombrePrestado, prestamo.idEquipo)

        actualizarTabla()
    }
}

const usuarioValido = (cedulaPrestado) => {
    let valido = true
    const prestamos = cargarPrestamos()
    prestamos.forEach((prestamo) => {
        if (prestamo.cedulaPrestado === cedulaPrestado && !prestamo.devuelto) {
            valido = false
        }
    })
    return valido
}

const guardarPrestamo = (prestamo) => {
    const espacios = cargarEspacios()
    const espaciosModificados = espacios.map(espacio => {
        if (String(espacio.id) === String(prestamo.idEquipo)) {
            espacio.prestado = true
        }
        return espacio
    })

    const salones = cargarSalones()
    salones.forEach(salon => {
        if (String(salon.tipo).toLowerCase() === "prestamo" || String(salon.id) === "1") {
            if (salon.espacios) salon.espacios = espaciosModificados
            if (salon.prestamos) salon.prestamos = espaciosModificados
        }
    })
    localStorage.setItem("salones", JSON.stringify(salones))

    const prestamos = cargarPrestamos()
    prestamos.push(prestamo)
    localStorage.setItem("prestamos", JSON.stringify(prestamos))
}

revisarAtrasados()
actualizarTabla()

// EVENTOS
btnCancelarPrestamo.addEventListener("click", () => {
    formulario.reset()
    modalPrestamo.classList.replace("d-flex", "d-none")
})

btnRegistrarPrestamo.addEventListener("click", () => {
    modalPrestamo.classList.replace("d-none", "d-flex")
    cargarLista()
})

formulario.addEventListener("submit", (e) => {
    e.preventDefault()

    const inputNombrePrestado = document.getElementById("nombre-solicitante")
    const inputCedulaPrestado = document.getElementById("CI-Solicitante")
    const inputEquipoElegido = document.getElementById("listaDispositivos")
    const inputFechaDevolucion = document.getElementById("final")

    if (!inputEquipoElegido.value) {
        alert("Seleccione un equipo válido.")
        return
    }

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
        alert("La persona ya cuenta con un préstamo activo.")
    }
})