// VARIABLES
const selectSalon = document.getElementById("ubicacionSalon")
const grupoLaboratorios = document.getElementById("grupoLaboratorio")
const grupoTalleres = document.getElementById("grupoTalleres")
const contenedorEquipos = document.getElementById("contenedorEquipos")

const formularioSalon = document.getElementById("formIncidencia")

const campoIncidencia = document.getElementById("incidencia")
const campoTitulo = document.getElementById("titulo")
const campoTipo = document.getElementById("tipo")
const campoAsunto = document.getElementById("asunto")
const campoPersona = document.getElementById("persona")
const campoDescripcion = document.getElementById("descripcion")
const btnAceptar = document.getElementById("btnAceptar")
const btnCancelar = document.getElementById("btnCancelar")

let incidenciasTemporales = {}
let pcActualId = null

// FUNCIONES
const cargarSalones = () => {
    const datos = localStorage.getItem("salones")
    if (datos === null || datos === undefined || datos === "") return []
    return JSON.parse(datos)
}

const cargarOpciones = () => {
    grupoLaboratorios.innerHTML = ""
    grupoTalleres.innerHTML = ""

    const salones = cargarSalones()

    salones.forEach(salon => {
        const opcion = document.createElement("option")

        opcion.value = `${salon.tipo}-${salon.id}`

        const tipoFormateado = salon.tipo.toLowerCase().includes("laboratorio") ? "Laboratorio" : "Taller"
        opcion.innerText = `${tipoFormateado} ${salon.id}`

        const tipoLimpio = salon.tipo.toLowerCase()
        if (tipoLimpio === "laboratorio") {
            grupoLaboratorios.appendChild(opcion)
        } else if (tipoLimpio === "talleres") {
            grupoTalleres.appendChild(opcion)
        }
    })
}

const renderizarEquiposDelSalon = (valorSeleccionado) => {
    contenedorEquipos.innerHTML = ""

    if (valorSeleccionado === "") return

    const partes = valorSeleccionado.split("-")
    const tipoSalon = partes[0]
    const idSalon = partes[1]

    const salones = cargarSalones()

    const salonEncontrado = salones.find(s => String(s.id) === String(idSalon) && String(s.tipo) === String(tipoSalon)) //Para ver si es el salon correcto, deben coincidir el tipo y el id

    const equipos = salonEncontrado.espacios || []

    equipos.forEach((equipo, indice) => {
        if (equipo === null || equipo === undefined || equipo === "") return

        const pcId = equipo.id

        if (pcId === null || pcId === undefined || pcId === "") return

        const tieneBorrador = incidenciasTemporales[pcId] !== undefined //Verifica si ya se hizo un borrador sobre dicha incidencia, para mantenerlo en caso de missclick o arrepentimiento

        const columna = document.createElement("div")
        columna.className = "columna-12 columna-md-6 columna-lg-4 mb-3"

        const espacioEquipo = document.createElement("div")
        espacioEquipo.className = "espacioEquipo shadow-sm h-100 border-1"

        const cuerpoEspacio = document.createElement("div")
        cuerpoEspacio.className = "espacioEquipo-body d-flex flex-column justify-content-between p-3"

        const headerDiv = document.createElement("div")
        headerDiv.className = "d-flex justify-content-between align-items-center mb-3"

        const nombrePC = document.createElement("h4")
        nombrePC.className = "h5 mb-0 fw-bold text-secondary"
        nombrePC.innerText = `PC: ${pcId}`


        headerDiv.appendChild(nombrePC)

        const divFondo = document.createElement("div")
        divFondo.className = "bg-light p-2 rounded-3 d-flex justify-content-around"

        const divFormulario = document.createElement("div")
        divFormulario.className = "form-check form-check-inline mb-0"

        const radioOk = document.createElement("input")
        radioOk.className = "form-check-input"

        radioOk.type = "radio"
        radioOk.name = `estado-${pcId}`
        radioOk.id = `ok-${pcId}`
        radioOk.value = "ok"
        radioOk.checked = !tieneBorrador

        const labelOk = document.createElement("label")
        labelOk.className = "form-check-label text-success fw-semibold"
        labelOk.htmlFor = `ok-${pcId}`
        labelOk.innerText = "Sin problemas"

        divFormulario.appendChild(radioOk)
        divFormulario.appendChild(labelOk)

        const separadorInput = document.createElement("div")
        separadorInput.className = "form-check form-check-inline mb-0"

        const radioInc = document.createElement("input")
        radioInc.className = "form-check-input"
        radioInc.type = "radio"
        radioInc.name = `estado-${pcId}`
        radioInc.id = `inc-${pcId}`
        radioInc.value = "incidencia"
        radioInc.checked = tieneBorrador

        const labelInc = document.createElement("label")
        labelInc.className = "form-check-label text-danger fw-semibold"
        labelInc.innerText = "Hay incidencia"

        separadorInput.appendChild(radioInc)
        separadorInput.appendChild(labelInc)

        divFondo.appendChild(divFormulario)
        divFondo.appendChild(separadorInput)

        cuerpoEspacio.appendChild(headerDiv)
        cuerpoEspacio.appendChild(divFondo)

        espacioEquipo.appendChild(cuerpoEspacio)
        columna.appendChild(espacioEquipo)
        contenedorEquipos.appendChild(columna)

        radioInc.addEventListener("change", () => {
            abrirFormularioModal(pcId)
        })

    })
}

const abrirFormularioModal = (pcId) => {
    pcActualId = pcId
    campoTitulo.innerText = `Registro de incidencia - PC: ${pcId}`

    const datosPrevios = incidenciasTemporales[pcId]

    if (datosPrevios !== undefined && datosPrevios !== null && datosPrevios !== "") {
        campoTipo.value = datosPrevios.tipo
        campoAsunto.value = datosPrevios.asunto
        campoPersona.value = datosPrevios.persona
        campoDescripcion.value = datosPrevios.descripcion

        const radioGravedad = campoIncidencia.querySelector(`input[type="radio"]`)
        if (radioGravedad) {
            radioGravedad.checked = true
        }
    } else {
        campoTipo.value = ""
        campoAsunto.value = ""
        campoPersona.value = ""
        campoDescripcion.value = ""

        const gravedades = campoIncidencia.querySelectorAll('input[name="gravedad"]') //Ese selector es especifico para agarrar todos los objetos input cuyo name sea gravedad
        // Recurso: https://stackoverflow.com/questions/15148659/how-can-i-use-queryselector-on-to-pick-an-input-element-by-name

        gravedades.forEach(radio => {
            radio.checked = false
        })
    }

    campoIncidencia.classList.remove("oculto") //Clase propia encargada de esconder ventanas
    campoIncidencia.classList.add("d-flex")
}

const buscarNombre = (cedulaUsuario) => {
    let usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => u.usuario === cedulaUsuario)
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const obtenerFecha = (dato) => {
    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()
    const formatoHora = fechaActual.getHours() + ":" + fechaActual.getMinutes()

    if(dato === "fecha"){
        return formatoFecha
    } else {
        return formatoHora
    }
}

const cerrarFormularioModal = () => {
    campoIncidencia.classList.remove("d-flex")
    campoIncidencia.classList.add("oculto")
    pcActualId = null
}

const registrarHistorial = (asunto, detalle, idEquipo, idTicket) => {
    const datos = localStorage.getItem("registroTickets")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        detalleTicket: detalle,
        idTicket: idTicket
    })
    localStorage.setItem("registroTickets", JSON.stringify(historial))
}

cargarOpciones()

// EVENTOS
btnAceptar.addEventListener("click", () => {
    const tipo = campoTipo.value
    const asunto = campoAsunto.value.trim()
    const persona = campoPersona.value.trim()
    const descripcion = campoDescripcion.value.trim()

    const gravedadRadio = campoIncidencia.querySelector('input[name="gravedad"]:checked')

    if (tipo === "" || asunto === "" || persona === "" || descripcion === "" || !gravedadRadio) {
        alert("Error: Complete todos los campos del formulario de incidencias")

    } else {

        incidenciasTemporales[pcActualId] = {
            tipo: tipo,
            asunto: asunto,
            persona: persona,
            gravedad: gravedadRadio.value,
            descripcion: descripcion
        }

        cerrarFormularioModal()
    }
})

btnCancelar.addEventListener("click", () => {
    if (pcActualId && incidenciasTemporales[pcActualId] === undefined) { //Verifica si el pc tiene incidencias registradas
        const radioOk = document.getElementById(`ok-${pcActualId}`) //Busca su radio de Sin incidencias
        if (radioOk) {
            radioOk.checked = true //Marca que no hay incidencias
        }
    }
    cerrarFormularioModal()
})

formularioSalon.addEventListener("submit", (e) => {
    e.preventDefault()

    const idSalonSeleccionado = selectSalon.value
    if (idSalonSeleccionado === "" || idSalonSeleccionado === null || idSalonSeleccionado === undefined) {
        alert("Error: Selecciona un salon para continuar")

    } else {

        const confirmacion = confirm("¿Está seguro de enviar los datos de las incidencias del salón al sistema?")
        if (confirmacion) {

            const llavesIncidencias = Object.keys(incidenciasTemporales) 

            if (llavesIncidencias.length > 0) {
                const datosTickets = localStorage.getItem("tickets")
                let listaTickets = []
                if (!(datosTickets === null || datosTickets === undefined || datosTickets === "")) listaTickets = JSON.parse(datosTickets)

                let contadorID = listaTickets.length

                const usuarioSesion = localStorage.getItem("usuario")
                let docenteId = "N/A"
                if (usuarioSesion) {
                    const uObj = JSON.parse(usuarioSesion)
                    docenteId = uObj.usuario
                }

const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()
    const formatoHora = fechaActual.getHours() + ":" + fechaActual.getMinutes()

                const textoSalon = selectSalon.options[selectSalon.selectedIndex].text

                llavesIncidencias.forEach(pcId => {
                    contadorID = contadorID + 1
                    const info = incidenciasTemporales[pcId]

                    const nuevoTicket = {
                        id: contadorID,
                        docente: docenteId,
                        fechaCreacion: formatoFecha,
                        horaCreacion: formatoHora,
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
                        justificacion: undefined
                    }

                    listaTickets.push(nuevoTicket)

                    const asuntoHistorial = info.asunto
                    const detalleHistorial = `El docente ${nuevoTicket.docente} (${buscarNombre(nuevoTicket.docente)}) registró una incidencia sobre la PC: ${pcId} del ${textoSalon}`

                    registrarHistorial(asuntoHistorial, detalleHistorial, pcId, nuevoTicket.id)
                })

                localStorage.setItem("tickets", JSON.stringify(listaTickets))
                alert(`Se registraron ${llavesIncidencias.length} incidencias`)
            } else {
                alert("El estado del salon se regsitro con exito")
            }

            formularioSalon.reset()
            selectSalon.value = ""
            incidenciasTemporales = {}
            contenedorEquipos.innerHTML = ""
        }
    }
})


selectSalon.addEventListener("change", (e) => {
    incidenciasTemporales = {}
    renderizarEquiposDelSalon(e.target.value) //Del evento change, se agarra el valor correspondiente a la opcion que eligió el usuario
    //Basicamente, le dice al metodo que salon se eligio, para poder mostrar la info correspondiente al mismo
})