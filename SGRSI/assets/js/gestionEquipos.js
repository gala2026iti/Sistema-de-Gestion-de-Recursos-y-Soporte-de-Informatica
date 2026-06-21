const btnAbrirUbicaciones = document.getElementById("btn-abrir-ubicaciones")
const btnCerrarUbicaciones = document.getElementById("btn-cerrar-ubicaciones")
const barralateralUbicaciones = document.getElementById("barralateral-ubicaciones")

const btnAgregarL = document.getElementById("btnAgregarL")
const btnAgregarT = document.getElementById("btnAgregarT")

const formulario = document.getElementById("formEquipo")
const tabla = document.getElementById("tablaEquipos")
const cuerpoTabla = tabla.querySelector("tbody")

const modalEquipo = document.getElementById("modalEquipo")
const btnRegistrarEquipo = document.getElementById("btnRegistrarEquipo")
const btnCancelarEquipo = document.getElementById("btnCancelarEquipo")

const filtroEstado = document.getElementById("filtroEstado")
const filtroIncidencias = document.getElementById("filtroIncidencias")
const filtroIntervencion = document.getElementById("filtroIntervencion")

const listaLaboratorios = document.getElementById("listaLaboratorios")
const listaTalleres = document.getElementById("listaTalleres")

const optgroupLaboratorios = document.getElementById("optgroupLaboratorios")
const optgroupTalleres = document.getElementById("optgroupTalleres")

let modoEdicion = false
let equipoEditando = null
let filtroUbicacionActual = "todos"

const cargarTodosLosSalones = () => {
    const data = localStorage.getItem("salones")
    if (data === null || data === "" || data === undefined) {
        const estructuraInicial = [
            { tipo: "prestamo", id: "prestamo", espacios: [] }
        ]
        localStorage.setItem("salones", JSON.stringify(estructuraInicial))
        return estructuraInicial
    }
    return JSON.parse(data)
}

const guardarSalonesBase = (listaCompleta) => {
    localStorage.setItem("salones", JSON.stringify(listaCompleta))
    actualizarListaSalones()
    actualizarSelectUbicaciones()
}

const cargarSalones = (tipo) => {
    const todos = cargarTodosLosSalones()
    const filtrados = []
    for (let i = 0; i < todos.length; i++) {
        if (todos[i].tipo === tipo) {
            filtrados.push(todos[i])
        }
    }
    return filtrados
}

const guardarSalones = (tipo, listaModificada) => {
    const todos = cargarTodosLosSalones()
    const resultadoUnificado = []

    for (let i = 0; i < todos.length; i++) {
        if (todos[i].tipo !== tipo) {
            resultadoUnificado.push(todos[i])
        }
    }

    for (let i = 0; i < listaModificada.length; i++) {
        resultadoUnificado.push(listaModificada[i])
    }

    guardarSalonesBase(resultadoUnificado)
}

const obtenerUbicacionDeEquipo = (idEquipo) => {
    const todos = cargarTodosLosSalones()
    for (let i = 0; i < todos.length; i++) {
        const salon = todos[i]
        
        if (salon.tipo === "laboratorios" || salon.tipo === "talleres") {
            for (let j = 0; j < salon.espacios.length; j++) {
                if (salon.espacios[j] === idEquipo) {
                    let prefijo = salon.tipo === "laboratorios" ? "Laboratorio " : "Taller "
                    return {
                        tipo: salon.tipo,
                        nombreUbicacion: prefijo + salon.id,
                        posicion: j + 1,
                        prestado: false
                    }
                }
            }
        } else if (salon.tipo === "prestamo") {
            for (let j = 0; j < salon.espacios.length; j++) {
                const item = salon.espacios[j]
                if (item && item.id === idEquipo) {
                    return {
                        tipo: "prestamo",
                        nombreUbicacion: "prestamo",
                        posicion: 0,
                        prestado: item.prestado
                    }
                }
            }
        }
    }
    return {
        tipo: "ninguna",
        nombreUbicacion: "ninguna",
        posicion: 0,
        prestado: false
    }
}

const desvincularEquipoDeSalones = (idEquipo, listaSalones) => {
    for (let i = 0; i < listaSalones.length; i++) {
        const salon = listaSalones[i]
        if (salon.tipo === "laboratorios" || salon.tipo === "talleres") {
            for (let j = 0; j < salon.espacios.length; j++) {
                if (salon.espacios[j] === idEquipo) {
                    salon.espacios[j] = null
                }
            }
            while (salon.espacios.length > 0 && salon.espacios[salon.espacios.length - 1] === null) {
                salon.espacios.pop()
            }
        } else if (salon.tipo === "prestamo") {
            const nuevosPrestamos = []
            for (let j = 0; j < salon.espacios.length; j++) {
                if (salon.espacios[j].id !== idEquipo) {
                    nuevosPrestamos.push(salon.espacios[j])
                }
            }
            salon.espacios = nuevosPrestamos
        }
    }
}

const asignarEquipoAUbicacion = (idEquipo, nuevaUbicacion, posicionDeseada) => {
    const todos = cargarTodosLosSalones()
    
    desvincularEquipoDeSalones(idEquipo, todos)

    if (nuevaUbicacion !== "ninguna" && nuevaUbicacion !== "prestamo") {
        for (let i = 0; i < todos.length; i++) {
            const salon = todos[i]
            let nombreUbicacionCompleta = (salon.tipo === "laboratorios" ? "Laboratorio " : "Taller ") + salon.id
            
            if (nombreUbicacionCompleta === nuevaUbicacion) {
                const indiceDestino = parseInt(posicionDeseada) - 1
                while (salon.espacios.length <= indiceDestino) {
                    salon.espacios.push(null)
                }
                salon.espacios[indiceDestino] = idEquipo
            }
        }
    } else if (nuevaUbicacion === "prestamo") {
        for (let i = 0; i < todos.length; i++) {
            if (todos[i].tipo === "prestamo") {
                todos[i].espacios.push({ id: idEquipo, prestado: false })
            }
        }
    }

    guardarSalonesBase(todos)
}


const agregarSalon = (tipo) => {
    const salones = cargarSalones(tipo)

    let nuevoId = 1
    let idEncontrado = false

    while (idEncontrado === false) {
        let existeId = false
        for (let i = 0; i < salones.length; i++) {
            if (salones[i].id === nuevoId) {
                existeId = true
            }
        }

        if (existeId === false) {
            idEncontrado = true
        } else {
            nuevoId = nuevoId + 1
        }
    }

    const nuevoSalon = {
        tipo: tipo,
        id: nuevoId,
        espacios: []
    }

    salones.push(nuevoSalon)

    salones.sort(function (a, b) {
        return a.id - b.id
    })

    guardarSalones(tipo, salones)
}

const eliminarSalon = (tipo, id) => {
    let mensajeConfirmacion = ""
    if (tipo === "laboratorios") {
        mensajeConfirmacion = "¿Estás seguro de que querés eliminar el laboratorio " + id + "?"
    } else {
        mensajeConfirmacion = "¿Estás seguro de que querés eliminar el taller " + id + "?"
    }

    if (confirm(mensajeConfirmacion)) {
        if (confirm("Esta acción va a desvincular todos los equipos asignados a este salón. ¿Querés continuar?")) {
            const salones = cargarSalones(tipo)
            const salonesFiltrados = []

            for (let i = 0; i < salones.length; i++) {
                if (salones[i].id !== id) {
                    salonesFiltrados.push(salones[i])
                }
            }

            guardarSalones(tipo, salonesFiltrados)

            let nombreUbicacionCompleta = tipo === "laboratorios" ? "Laboratorio " + id : "Taller " + id
            if (filtroUbicacionActual === nombreUbicacionCompleta) {
                filtroUbicacionActual = "todos"
            }
            actualizarTabla()
        }
    }
}

const actualizarListaSalones = () => {
    listaLaboratorios.innerHTML = ""
    listaTalleres.innerHTML = ""

    const laboratorios = cargarSalones("laboratorios")
    for (let i = 0; i < laboratorios.length; i++) {
        const laboratorio = laboratorios[i]
        const li = document.createElement("li")

        const enlace = document.createElement("a")
        enlace.href = "#"
        enlace.textContent = "Laboratorio " + laboratorio.id
        enlace.addEventListener("click", function (e) {
            e.preventDefault()
            filtroUbicacionActual = "Laboratorio " + laboratorio.id
            actualizarTabla()
        })

        const btnEliminar = document.createElement("button")
        btnEliminar.textContent = "X"
        btnEliminar.className = "btn-accion-salon"
        btnEliminar.addEventListener("click", function () {
            eliminarSalon("laboratorios", laboratorio.id)
        })

        li.appendChild(enlace)
        li.appendChild(btnEliminar)
        listaLaboratorios.appendChild(li)
    }

    const talleres = cargarSalones("talleres")
    for (let i = 0; i < talleres.length; i++) {
        const taller = talleres[i]
        const li = document.createElement("li")

        const enlace = document.createElement("a")
        enlace.href = "#"
        enlace.textContent = "Taller " + taller.id
        enlace.addEventListener("click", function (e) {
            e.preventDefault()
            filtroUbicacionActual = "Taller " + taller.id
            actualizarTabla()
        })

        const btnEliminar = document.createElement("button")
        btnEliminar.textContent = "X"
        btnEliminar.className = "btn-accion-salon"
        btnEliminar.addEventListener("click", function () {
            eliminarSalon("talleres", taller.id)
        })

        li.appendChild(enlace)
        li.appendChild(btnEliminar)
        listaTalleres.appendChild(li)
    }
}

const actualizarSelectUbicaciones = () => {
    optgroupLaboratorios.innerHTML = ""
    optgroupTalleres.innerHTML = ""

    const laboratorios = cargarSalones("laboratorios")
    for (let i = 0; i < laboratorios.length; i++) {
        const opt = document.createElement("option")
        opt.value = "Laboratorio " + laboratorios[i].id
        opt.textContent = "Laboratorio " + laboratorios[i].id
        optgroupLaboratorios.appendChild(opt)
    }

    const talleres = cargarSalones("talleres")
    for (let i = 0; i < talleres.length; i++) {
        const opt = document.createElement("option")
        opt.value = "Taller " + talleres[i].id
        opt.textContent = "Taller " + talleres[i].id
        optgroupTalleres.appendChild(opt)
    }
}

const cargarEquipos = () => {
    const equipos = localStorage.getItem("equipos")
    if(equipos === null || equipos === "" || equipos === undefined){
        return []
    } else {
        return JSON.parse(equipos)
    }
}

const guardarEquiposBase = (lista) => {
    localStorage.setItem("equipos", JSON.stringify(lista))
    actualizarTabla()
}

const posicionEstaOcupada = (salonNombre, posicion, idActual) => {
    const todos = cargarTodosLosSalones()
    for (let i = 0; i < todos.length; i++) {
        const salon = todos[i]
        let nombreUbicacionCompleta = (salon.tipo === "laboratorios" ? "Laboratorio " : "Taller ") + salon.id
        
        if (nombreUbicacionCompleta === salonNombre) {
            const indice = parseInt(posicion) - 1
            if (indice >= 0 && indice < salon.espacios.length) {
                const ocupante = salon.espacios[indice]
                if (ocupante !== null && ocupante !== idActual) {
                    return true
                }
            }
        }
    }
    return false
}

const obtenerPrimeraPosicionLibre = (salonNombre) => {
    if (salonNombre === "ninguna" || salonNombre === "prestamo") {
        return 0
    }
    const todos = cargarTodosLosSalones()
    for (let i = 0; i < todos.length; i++) {
        const salon = todos[i]
        let nombreUbicacionCompleta = (salon.tipo === "laboratorios" ? "Laboratorio " : "Taller ") + salon.id
        
        if (nombreUbicacionCompleta === salonNombre) {
            for (let j = 0; j < salon.espacios.length; j++) {
                if (salon.espacios[j] === null) {
                    return j + 1
                }
            }
            return salon.espacios.length + 1
        }
    }
    return 1
}

const removerDeSalonIndividual = (id) => {
    const todos = cargarTodosLosSalones()
    desvincularEquipoDeSalones(id, todos)
    guardarSalonesBase(todos)
    actualizarTabla()
}

const activarEquipo = (id) => {
    const equipos = cargarEquipos()
    for (let i = 0; i < equipos.length; i++) {
        if (equipos[i].id === id) {
            equipos[i].activo = true
        }
    }
    guardarEquiposBase(equipos)
}

const desactivarEquipo = (id) => {
    const equipos = cargarEquipos()
    for (let i = 0; i < equipos.length; i++) {
        if (equipos[i].id === id) {
            equipos[i].activo = false
        }
    }
    guardarEquiposBase(equipos)
}

const modificarEquipo = (equipoModificado) => {
    asignarEquipoAUbicacion(equipoEditando, equipoModificado.ubicacion, equipoModificado.posicion)

    const equipos = cargarEquipos()
    for (let i = 0; i < equipos.length; i++) {
        if (equipos[i].id === equipoEditando) {
            equipos[i].ultimaIntervencion = equipoModificado.ultimaIntervencion
        }
    }
    guardarEquiposBase(equipos)
    
    modoEdicion = false
    equipoEditando = null
    modalEquipo.classList.replace("d-flex", "d-none")
    formulario.reset()
}

const mapearFechaParaOrdenar = (stringFecha) => {
    if (!stringFecha) {
        return 0
    }
    const partes = stringFecha.split("/")
    if (partes.length !== 3) {
        return 0
    }
    const dia = partes[0].padStart(2, "0")
    const mes = partes[1].padStart(2, "0")
    const anio = partes[2]
    return parseInt(anio + mes + dia)
}

const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    const cabeceraTr = tabla.querySelector("thead tr")

    cabeceraTr.innerHTML = `
        <th>Codigo (ID Global)</th> 
        <th>Estado</th>
        <th>Asignación / Préstamo</th>
        <th>Ubicación Física</th>
        <th>Ultima intervencion</th>
        <th>Incidencias</th>
        <th>Acciones</th>
    `

    const equipos = cargarEquipos()
    let equiposFiltrados = []

    for (let i = 0; i < equipos.length; i++) {
        const equipo = equipos[i]
        const infoUbicacion = obtenerUbicacionDeEquipo(equipo.id)

        if (filtroUbicacionActual === "todos") {
            equiposFiltrados.push(equipo)
        } else if (filtroUbicacionActual === "prestamo" && infoUbicacion.nombreUbicacion === "prestamo") {
            equiposFiltrados.push(equipo)
        } else if (infoUbicacion.nombreUbicacion === filtroUbicacionActual) {
            equiposFiltrados.push(equipo)
        }
    }

    if (filtroEstado.value !== "") {
        const resultado = []
        for (let i = 0; i < equiposFiltrados.length; i++) {
            if (filtroEstado.value === "activo" && equiposFiltrados[i].activo === true) {
                resultado.push(equiposFiltrados[i])
            } else if (filtroEstado.value === "inactivo" && equiposFiltrados[i].activo === false) {
                resultado.push(equiposFiltrados[i])
            }
        }
        equiposFiltrados = resultado
    }

    if (filtroIncidencias.value !== "") {
        for (let i = 0; i < equiposFiltrados.length - 1; i++) {
            for (let j = 0; j < equiposFiltrados.length - i - 1; j++) {
                const totalA = equiposFiltrados[j].incidencias.length
                const totalB = equiposFiltrados[j + 1].incidencias.length

                let intercambiar = false
                if (filtroIncidencias.value === "menor" && totalA > totalB) {
                    intercambiar = true
                } else if (filtroIncidencias.value === "mayor" && totalA < totalB) {
                    intercambiar = true
                }

                if (intercambiar === true) {
                    const temp = equiposFiltrados[j]
                    equiposFiltrados[j] = equiposFiltrados[j + 1]
                    equiposFiltrados[j + 1] = temp
                }
            }
        }
    }

    if (filtroIntervencion.value !== "") {
        for (let i = 0; i < equiposFiltrados.length - 1; i++) {
            for (let j = 0; j < equiposFiltrados.length - i - 1; j++) {
                const fechaA = mapearFechaParaOrdenar(equiposFiltrados[j].ultimaIntervencion)
                const fechaB = mapearFechaParaOrdenar(equiposFiltrados[j + 1].ultimaIntervencion)

                let intercambiar = false
                if (filtroIntervencion.value === "reciente" && fechaA < fechaB) {
                    intercambiar = true
                } else if (filtroIntervencion.value === "antiguo" && fechaA > fechaB) {
                    intercambiar = true
                }

                if (intercambiar === true) {
                    const temp = equiposFiltrados[j]
                    equiposFiltrados[j] = equiposFiltrados[j + 1]
                    equiposFiltrados[j + 1] = temp
                }
            }
        }
    }

    for (let i = 0; i < equiposFiltrados.length; i++) {
        const eq = equiposFiltrados[i]
        const infoUbicacion = obtenerUbicacionDeEquipo(eq.id)

        const fila = document.createElement("tr")

        const tdCodigo = document.createElement("td")
        tdCodigo.textContent = eq.id

        const tdEstado = document.createElement("td")
        tdEstado.textContent = eq.activo ? "Activo" : "Inactivo"

        const tdPrestamo = document.createElement("td")
        if (infoUbicacion.nombreUbicacion === "ninguna") {
            tdPrestamo.textContent = "Sin asignar"
        } else if (infoUbicacion.nombreUbicacion === "prestamo") {
            tdPrestamo.textContent = infoUbicacion.prestado ? "Prestado" : "No prestado"
        } else {
            tdPrestamo.textContent = "En salón"
        }

        const tdFisica = document.createElement("td")
        if (infoUbicacion.nombreUbicacion !== "ninguna" && infoUbicacion.nombreUbicacion !== "prestamo") {
            tdFisica.textContent = infoUbicacion.nombreUbicacion + " - " + infoUbicacion.posicion
        } else {
            tdFisica.textContent = "N/A"
        }

        const tdIntervencion = document.createElement("td")
        tdIntervencion.textContent = eq.ultimaIntervencion

        const tdIncidencias = document.createElement("td")
        tdIncidencias.textContent = eq.incidencias.length

        const tdAcciones = document.createElement("td")

        const btnModificar = document.createElement("button")
        btnModificar.textContent = "Modificar"
        btnModificar.className = "btn btn-primary btn-sm me-2"
        btnModificar.addEventListener("click", function () {
            modoEdicion = true
            equipoEditando = eq.id

            const inputIDPC = document.getElementById("idPC")
            inputIDPC.value = eq.id
            inputIDPC.readOnly = true

            const selectUbicacion = document.getElementById("ubicacion")
            selectUbicacion.value = infoUbicacion.nombreUbicacion

            evaluarVisibilidadLugar(infoUbicacion.nombreUbicacion)

            const inputLugar = document.getElementById("posicionPC")
            if (infoUbicacion.nombreUbicacion !== "ninguna" && infoUbicacion.nombreUbicacion !== "prestamo") {
                inputLugar.value = infoUbicacion.posicion
            } else {
                inputLugar.value = ""
            }

            modalEquipo.classList.replace("d-none", "d-flex")
        })

        let btnEstado
        if (eq.activo === true) {
            btnEstado = document.createElement("button")
            btnEstado.textContent = "Desactivar"
            btnEstado.className = "btn btn-danger btn-sm me-2"
            btnEstado.addEventListener("click", function () {
                if (confirm("¿Estás seguro de que queres desactivar este equipo?")) {
                    desactivarEquipo(eq.id)
                }
            })
        } else {
            btnEstado = document.createElement("button")
            btnEstado.textContent = "Activar"
            btnEstado.className = "btn btn-success btn-sm me-2"
            btnEstado.addEventListener("click", function () {
                if (confirm("¿Estás seguro de que queres activar este equipo?")) {
                    activarEquipo(eq.id)
                }
            })
        }

        const btnIncidencias = document.createElement("button")
        btnIncidencias.textContent = "Ver Incidencias"
        btnIncidencias.className = "btn btn-warning btn-sm me-2"
        btnIncidencias.addEventListener("click", function () {
            localStorage.setItem("idEquipoIncidencias", eq.id)
            alert("por el momento no hay funcion, pero debe llevar a la pagina de incidencias")
        })

        tdAcciones.appendChild(btnModificar)
        tdAcciones.appendChild(btnEstado)
        tdAcciones.appendChild(btnIncidencias)

        if (infoUbicacion.nombreUbicacion !== "ninguna" && infoUbicacion.nombreUbicacion !== "prestamo") {
            const btnQuitar = document.createElement("button")
            btnQuitar.textContent = "Quitar del salón"
            btnQuitar.className = "btn btn-secondary btn-sm"
            btnQuitar.addEventListener("click", function () {
                if (confirm("¿Querés remover este equipo del salón? Regresará al estado inicial.")) {
                    removerDeSalonIndividual(eq.id)
                }
            })
            tdAcciones.appendChild(btnQuitar)
        }

        fila.appendChild(tdCodigo)
        fila.appendChild(tdEstado)
        fila.appendChild(tdPrestamo)
        fila.appendChild(tdFisica)
        fila.appendChild(tdIntervencion)
        fila.appendChild(tdIncidencias)
        tdAcciones.className = "text-nowrap"
        fila.appendChild(tdAcciones)

        cuerpoTabla.appendChild(fila)
    }
}

const evaluarVisibilidadLugar = (valorUbicacion) => {
    const contenedorLugar = document.getElementById("contenedorLugar")
    const inputLugar = document.getElementById("posicionPC")

    if (valorUbicacion !== "" && valorUbicacion !== "ninguna" && valorUbicacion !== "prestamo") {
        contenedorLugar.classList.replace("d-none", "d-flex")
        inputLugar.required = true
    } else {
        contenedorLugar.classList.replace("d-flex", "d-none")
        inputLugar.required = false
        inputLugar.value = ""
    }
}

document.getElementById("ubicacion").addEventListener("change", function () {
    evaluarVisibilidadLugar(this.value)
})

formulario.addEventListener("submit", function (e) {
    e.preventDefault()

    const inputIDPC = document.getElementById("idPC")
    const inputSalon = document.getElementById("ubicacion")
    const inputLugar = document.getElementById("posicionPC")

    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()

    const equipos = cargarEquipos()
    let posicionFinal = 0

    if (inputSalon.value !== "ninguna" && inputSalon.value !== "prestamo") {
        if (inputLugar.value !== "") {
            posicionFinal = parseInt(inputLugar.value)

            if (posicionFinal <= 0) {
                alert("Error: El lugar dentro del salon debe ser mayor a 0.")
                return
            }

            if (posicionEstaOcupada(inputSalon.value, posicionFinal, inputIDPC.value)) {
                alert("Error: El Lugar ingresado ya está ocupado por otra computadora.")
                return
            }
        } else {
            posicionFinal = obtenerPrimeraPosicionLibre(inputSalon.value)
        }
    }

    if (modoEdicion === true) {
        const equipoDataModificada = {
            ubicacion: inputSalon.value,
            posicion: posicionFinal,
            ultimaIntervencion: formatoFecha
        }
        modificarEquipo(equipoDataModificada)
    } else {
        let yaExiste = false
        for (let i = 0; i < equipos.length; i++) {
            if (equipos[i].id === inputIDPC.value) {
                yaExiste = true
            }
        }

        if (yaExiste === true) {
            alert("Error: Este codigo de equipo ya esta en uso")
            return
        }

        asignarEquipoAUbicacion(inputIDPC.value.trim(), inputSalon.value, posicionFinal)

        const equipoData = {
            id: inputIDPC.value.trim(),
            activo: true,
            incidencias: [],
            ultimaIntervencion: formatoFecha
        }

        equipos.push(equipoData)
        guardarEquiposBase(equipos)
        modalEquipo.classList.replace("d-flex", "d-none")
        formulario.reset()
        document.getElementById("contenedorLugar").classList.replace("d-flex", "d-none")
    }
})

const enlacesModos = barralateralUbicaciones.querySelectorAll(".filtro-ubicacion-directa")
for (let i = 0; i < enlacesModos.length; i++) {
    enlacesModos[i].addEventListener("click", function (e) {
        e.preventDefault()
        const tipoUbicacion = enlacesModos[i].getAttribute("data-ubicacion")
        filtroUbicacionActual = tipoUbicacion
        actualizarTabla()
    })
}

btnAbrirUbicaciones.addEventListener("click", function () {
    barralateralUbicaciones.classList.add("abierto")
})

btnCerrarUbicaciones.addEventListener("click", function () {
    barralateralUbicaciones.classList.remove("m-0", "abierto")
    barralateralUbicaciones.classList.add("cerrado")
})

btnRegistrarEquipo.addEventListener("click", function () {
    modoEdicion = false
    equipoEditando = null
    formulario.reset()
    document.getElementById("contenedorLugar").classList.replace("d-flex", "d-none")

    const inputIDPC = document.getElementById("idPC")
    inputIDPC.readOnly = false
    modalEquipo.classList.replace("d-none", "d-flex")
})

btnCancelarEquipo.addEventListener("click", function () {
    modoEdicion = false
    equipoEditando = null
    formulario.reset()
    modalEquipo.classList.replace("d-flex", "d-none")
    document.getElementById("contenedorLugar").classList.replace("d-flex", "d-none")
})

btnAgregarL.addEventListener("click", function () {
    agregarSalon("laboratorios")
})

btnAgregarT.addEventListener("click", function () {
    agregarSalon("talleres")
})

filtroEstado.addEventListener("change", function () {
    actualizarTabla()
})

filtroIncidencias.addEventListener("change", function () {
    actualizarTabla()
})

filtroIntervencion.addEventListener("change", function () {
    actualizarTabla()
})

const linksEstaticos = barralateralUbicaciones.getElementsByTagName("a")
for (let i = 0; i < linksEstaticos.length; i++) {
    if (linksEstaticos[i].textContent === "Dispositivos para prestar") {
        linksEstaticos[i].addEventListener("click", function (e) {
            e.preventDefault()
            filtroUbicacionActual = "prestamo"
            actualizarTabla()
        })
    }
    if (linksEstaticos[i].textContent === "Todos los Dispositivos registrados en el sistema") {
        linksEstaticos[i].addEventListener("click", function (e) {
            e.preventDefault()
            filtroUbicacionActual = "todos"
            actualizarTabla()
        })
    }
}

actualizarListaSalones()
actualizarSelectUbicaciones()
actualizarTabla()