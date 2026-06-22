// VARIABLES
const selectSector = document.getElementById("sector")
const optgroupLaboratorios = document.getElementById("optgroupLaboratorios")
const optgroupTalleres = document.getElementById("optgroupTalleres")
const contenedorEquipos = document.getElementById("contenedor-equipos")

const formularioSalon = document.querySelector(".main-formulario form")

const modalIncidencia = document.getElementById("modal-incidencia")
const tituloModal = document.getElementById("titulo-modal")
const inputTipo = document.getElementById("tipo")
const inputAsunto = document.getElementById("asunto-modal")
const inputPersona = document.getElementById("persona-modal")
const txtDescripcion = document.getElementById("descripcion-modal")
const btnAceptarModal = document.getElementById("btnAceptarModal")
const btnCancelarModal = document.getElementById("btnCancelarModal")

let incidenciasTemporales = {}  
let pcActualId = null           

// FUNCIONES
const obtenerSalonesSistema = () => {
    const datos = localStorage.getItem("salones")
    if (datos === null || datos === undefined || datos === "") {
        return []
    } else {
        return JSON.parse(datos)
    }
}

const popularSelectSalones = () => {
    optgroupLaboratorios.innerHTML = ""
    optgroupTalleres.innerHTML = ""

    const salones = obtenerSalonesSistema()

    salones.forEach(salon => {
        const option = document.createElement("option")
        
        option.value = `${salon.tipo}-${salon.id}` 
        
        const tipoFormateado = String(salon.tipo).toLowerCase().includes("lab") ? "Laboratorio" : "Taller"
        option.textContent = `${tipoFormateado} ${salon.id}`

        const tipoLimpio = String(salon.tipo).toLowerCase()
        if (tipoLimpio === "laboratorio" || tipoLimpio === "laboratorios") {
            optgroupLaboratorios.appendChild(option)
        } else if (tipoLimpio === "taller" || tipoLimpio === "talleres") {
            optgroupTalleres.appendChild(option)
        }
    })
}

const renderizarEquiposDelSalon = (valorSeleccionado) => {
    contenedorEquipos.innerHTML = ""

    if (valorSeleccionado === "") return

    const partes = valorSeleccionado.split("-")
    const tipoSalon = partes[0]
    const idSalon = partes[1]

    const salones = obtenerSalonesSistema()
    
    const salonEncontrated = salones.find(s => String(s.id) === String(idSalon) && String(s.tipo) === String(tipoSalon))
    
    if (!salonEncontrated) return

    const equipos = salonEncontrated.espacios || salonEncontrated.prestamos || []

    equipos.forEach((equipo, indice) => {
        if (!equipo) return 

        const pcId = typeof equipo === "object" && equipo !== null 
            ? (equipo.id || equipo.numeroBanco) 
            : String(equipo)

        if (!pcId) return

        const tieneBorrador = incidenciasTemporales[pcId] !== undefined

        const col = document.createElement("div")
        col.className = "col-12 col-md-6 col-lg-4 mb-3"

        const card = document.createElement("div")
        card.className = "card shadow-sm h-100 border-1"

        const cardBody = document.createElement("div")
        cardBody.className = "card-body d-flex flex-column justify-content-between p-3"

        const headerDiv = document.createElement("div")
        headerDiv.className = "d-flex justify-content-between align-items-center mb-3"

        const h4 = document.createElement("h4")
        h4.className = "h5 mb-0 fw-bold text-secondary"
        h4.textContent = `PC: ${pcId}`


        headerDiv.appendChild(h4)

        const bgLightDiv = document.createElement("div")
        bgLightDiv.className = "bg-light p-2 rounded-3 d-flex justify-content-around"

        const checkInlineOk = document.createElement("div")
        checkInlineOk.className = "form-check form-check-inline mb-0"

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
        labelOk.textContent = "Sin problemas"

        checkInlineOk.appendChild(radioOk)
        checkInlineOk.appendChild(labelOk)

        const checkInlineInc = document.createElement("div")
        checkInlineInc.className = "form-check form-check-inline mb-0"

        const radioInc = document.createElement("input")
        radioInc.className = "form-check-input"
        radioInc.type = "radio"
        radioInc.name = `estado-${pcId}`
        radioInc.id = `inc-${pcId}`
        radioInc.value = "incidencia"
        radioInc.checked = tieneBorrador

        const labelInc = document.createElement("label")
        labelInc.className = "form-check-label text-danger fw-semibold"
        labelInc.htmlFor = `inc-${pcId}`
        labelInc.textContent = "Hay incidencia"

        checkInlineInc.appendChild(radioInc)
        checkInlineInc.appendChild(labelInc)

        bgLightDiv.appendChild(checkInlineOk)
        bgLightDiv.appendChild(checkInlineInc)

        cardBody.appendChild(headerDiv)
        cardBody.appendChild(bgLightDiv)

        card.appendChild(cardBody)
        col.appendChild(card)
        contenedorEquipos.appendChild(col)

        radioInc.addEventListener("change", () => {
            abrirFormularioModal(pcId)
        })

    })
}

const abrirFormularioModal = (pcId) => {
    pcActualId = pcId
    tituloModal.textContent = `Registro de incidencia - PC: ${pcId}`

    const datosPrevios = incidenciasTemporales[pcId]

    if (datosPrevios !== undefined) {
        inputTipo.value = datosPrevios.tipo
        inputAsunto.value = datosPrevios.asunto
        inputPersona.value = datosPrevios.persona
        txtDescripcion.value = datosPrevios.descripcion
        
        const radioGravedad = modalIncidencia.querySelector(`input[name="gravedad"][value="${datosPrevios.gravedad}"]`)
        if (radioGravedad) {
            radioGravedad.checked = true
        }
    } else {
        inputTipo.value = ""
        inputAsunto.value = ""
        inputPersona.value = ""
        txtDescripcion.value = ""
        
        const gravedades = modalIncidencia.querySelectorAll('input[name="gravedad"]')
        gravedades.forEach(radio => {
            radio.checked = false
        })
    }

    modalIncidencia.classList.remove("oculto")
    modalIncidencia.classList.add("d-flex") 
}

const cerrarFormularioModal = () => {
    modalIncidencia.classList.remove("d-flex")
    modalIncidencia.classList.add("oculto")
    pcActualId = null
}

const registrarEnHistorialSistema = (descripcion, detalle) => {
    const datosHistorial = localStorage.getItem("registroTickets")
    let listaHistorial = []
    
    if (datosHistorial !== null && datosHistorial !== undefined && datosHistorial !== "") {
        listaHistorial = JSON.parse(datosHistorial)
    }

    const nuevoRegistro = {
        id: listaHistorial.length + 1,
        fecha: new Date().toLocaleDateString('es-ES'),
        descripcionAccion: descripcion,
        detalleOperador: detalle
    }

    listaHistorial.push(nuevoRegistro)
    localStorage.setItem("registroTickets", JSON.stringify(listaHistorial))
}

popularSelectSalones()

// EVENTOS
btnAceptarModal.addEventListener("click", () => {
    const tipo = inputTipo.value
    const asunto = inputAsunto.value.trim()
    const persona = inputPersona.value.trim()
    const descripcion = txtDescripcion.value.trim()
    
    const gravedadRadio = modalIncidencia.querySelector('input[name="gravedad"]:checked')

    if (tipo === "" || asunto === "" || persona === "" || descripcion === "" || !gravedadRadio) {
        alert("Complete todos los campos del formulario de incidencias")
        return
    }

    incidenciasTemporales[pcActualId] = {
        tipo: tipo,
        asunto: asunto,
        persona: persona,
        gravedad: gravedadRadio.value,
        descripcion: descripcion
    }


    cerrarFormularioModal()
})

btnCancelarModal.addEventListener("click", () => {
    if (pcActualId && incidenciasTemporales[pcActualId] === undefined) {
        const radioOk = document.getElementById(`ok-${pcActualId}`)
        if (radioOk) {
            radioOk.checked = true
        }
    }
    cerrarFormularioModal()
})

formularioSalon.addEventListener("submit", (e) => {
    e.preventDefault()

    const idSalonSeleccionado = selectSector.value
    if (idSalonSeleccionado === "") {
        alert("Selecciona un salon para continuar")
        return
    }

    const confirmacion = confirm("¿Está seguro de enviar los datos de las incidencias del salón al sistema?")
    if (!confirmacion) return

    const llavesIncidencias = Object.keys(incidenciasTemporales)

    if (llavesIncidencias.length > 0) {
        const datosTickets = localStorage.getItem("tickets")
        let listaTickets = []
        if (datosTickets !== null && datosTickets !== undefined && datosTickets !== "") {
            listaTickets = JSON.parse(datosTickets)
        }

        let contadorID = listaTickets.length

        const usuarioSesion = localStorage.getItem("usuario")
        let docenteId = "Docente Generico"
        if (usuarioSesion) {
            const uObj = JSON.parse(usuarioSesion)
            docenteId = uObj.usuario || uObj.cedula || docenteId
        }

        const textoSalon = selectSector.options[selectSector.selectedIndex].text

        llavesIncidencias.forEach(pcId => {
            contadorID = contadorID + 1
            const info = incidenciasTemporales[pcId]

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
            }

            listaTickets.push(nuevoTicket)

            const descripcionHistorial = info.asunto 
            const detalleHistorial = `El docente ${docenteId} registro una incidencia sobre la PC: ${pcId} del ${textoSalon}`
            
            registrarEnHistorialSistema(descripcionHistorial, detalleHistorial)
        })

        localStorage.setItem("tickets", JSON.stringify(listaTickets))
        alert(`Operación exitosa, se registraron ${llavesIncidencias.length} incidencias`)
    } else {
        alert("El estado del salon se regsitro con exito, sin novedades")
    }

    formularioSalon.reset()
    selectSector.value = ""
    incidenciasTemporales = {}
    contenedorEquipos.innerHTML = ""
})

selectSector.addEventListener("change", (e) => {
    incidenciasTemporales = {} 
    renderizarEquiposDelSalon(e.target.value)
})