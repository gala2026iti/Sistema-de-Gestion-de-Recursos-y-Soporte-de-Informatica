const formulario = document.getElementById("formPrestamo")
const btnRegistrarPrestamo = document.getElementById("btnRegistrarPrestamo")
const modalPrestamo = document.getElementById("modalPrestamo")
const btnCancelarPrestamo = document.getElementById("btnCancelarPrestamo")
const opciones = document.getElementById("listaDispositivos")
const tabla = document.getElementById("tablaPrestamos")
const cuerpoTabla = tabla.querySelector("tbody")
const usuario = localStorage.getItem("usuario")
const usuarioJSON = JSON.parse(usuario)

const cargarComputadoras = () => {
    const computadoras = localStorage.getItem("equipos")
    if (computadoras === null) {
        return []
    } else {
        return JSON.parse(computadoras)
    }
}

const cargarLista = () => {
    const computadoras = cargarComputadoras()
    const computadorasLibres = computadoras.filter(computadora => computadora.equipoPrestado === false)

    opciones.innerHTML = `<option value="" selected>Elegir equipo</option>`
    computadorasLibres.forEach((compuLibre) => {
        if(compuLibre.ubicacion === "prestamo") {
            const opcion = document.createElement("option")
            opcion.value = compuLibre.id
            opcion.textContent = compuLibre.id
            opciones.appendChild(opcion)
        }
    })
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
    if (!confirm("¿Estas seguro de marcar el prestamo como finalizado? La acción no puede deshacerse")) {
        return
    }

    const prestamos = cargarPrestamos()
    const prestamo = prestamos.find(p => p.id === idPrestamo)
    
    if (prestamo) {
        prestamo.devuelto = true
        localStorage.setItem("prestamos", JSON.stringify(prestamos))
        
        const computadoras = cargarComputadoras()
        const computadorasModificadas = computadoras.map(c => {
            if (c.id === prestamo.idEquipo) {
                c.equipoPrestado = false
            }
            return c
        })
        localStorage.setItem("equipos", JSON.stringify(computadorasModificadas))
        
        registrarAccion("devolucion", usuarioJSON.cedula, prestamo.cedulaPrestado, prestamo.nombrePrestado, prestamo.idEquipo)
        
        actualizarTabla()
    }
}

const actualizarTabla = () => {
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
        
        if (prestamo.entregaAtrasada) {
            filaEstado.textContent = "Atrasado"
        } else {
            filaEstado.textContent = "Activo"
        }

        const botonFinalizar = document.createElement("button")
        botonFinalizar.textContent = "Finalizar"
        botonFinalizar.classList.add("btn", "btn-danger")
        
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
    const computadoras = cargarComputadoras()
    const computadorasModificadas = computadoras.map(computadora => {
        if (computadora.id === prestamo.idEquipo) {
            computadora.equipoPrestado = true
        }
        return computadora
    })

    localStorage.setItem("equipos", JSON.stringify(computadorasModificadas))

    const prestamos = cargarPrestamos()
    prestamos.push(prestamo)
    localStorage.setItem("prestamos", JSON.stringify(prestamos))
}

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

    const fechaSeleccionada = new Date(inputFechaDevolucion.value)
    const fechaActual = new Date()

    if (fechaSeleccionada <= fechaActual) {
        alert("La fecha de devolución debe ser posterior a la fecha y hora actual.")
        return
    }

    const contadorID = () => {
        let contador = localStorage.getItem("contador")
        if(contador === null || contador === undefined || contador === "") {
            contador = 1
        } else {
            contador = parseInt(contador) + 1
        }
        localStorage.setItem("contador", contador)
        return contador
    }

    const prestamo = {
        id: contadorID(),
        cedulaPrestado: inputCedulaPrestado.value,
        nombrePrestado: inputNombrePrestado.value,
        idEquipo: inputEquipoElegido.value,
        cedulaPrestador: usuarioJSON.cedula,
        fechaInicio: new Date().toISOString(),
        fechaDevolucion: inputFechaDevolucion.value,
        devuelto: false,
        entregaAtrasada: false
    }

    if (usuarioValido(prestamo.cedulaPrestado)) {
        guardarPrestamo(prestamo)
        registrarAccion("prestamo", prestamo.cedulaPrestador, prestamo.cedulaPrestado, prestamo.nombrePrestado, prestamo.idEquipo)

        alert("Prestamo registrado con exito")
        formulario.reset()
        modalPrestamo.classList.replace("d-flex", "d-none")
        actualizarTabla()
    } else {
        alert("La persona ya cuenta con un prestamo activo")
    }
})

revisarAtrasados()
actualizarTabla()