// VARIABLES
let modoEdicion = false
let equipoEditando = null
let filtroUbicacionActual = "todos"

const btnAbrirUbicaciones = document.getElementById("btn-abrir-ubicaciones")
const btnCerrarUbicaciones = document.getElementById("btn-cerrar-ubicaciones")
const barralateralUbicaciones = document.getElementById("barralateral-ubicaciones")

const btnAgregarL = document.getElementById("btnAgregarL")
const btnAgregarT = document.getElementById("btnAgregarT")

const formulario = document.getElementById("formEquipo")
const tabla = document.getElementById("tablaEquipos")
const cuerpoTabla = tabla ? tabla.querySelector("tbody") : null

const modalEquipo = document.getElementById("modalEquipo")
const btnAbrirModalRegistrar = document.getElementById("btnRegistrarEquipo")
const btnGuardarEquipo = document.getElementById("btnGuardarEquipo")
const btnCancelarEquipo = document.getElementById("btnCancelarEquipo")

const filtroEstado = document.getElementById("filtroEstado")
const filtroIncidencias = document.getElementById("filtroIncidencias")
const filtroIntervencion = document.getElementById("filtroIntervencion")

const listaLaboratorios = document.getElementById("listaLaboratorios")
const listaTalleres = document.getElementById("listaTalleres")

const optgroupLaboratorios = document.getElementById("optgroupLaboratorios")
const optgroupTalleres = document.getElementById("optgroupTalleres")

const inputIdPC = document.getElementById("idPC")
const selectUbicacion = document.getElementById("ubicacion")
const contenedorLugar = document.getElementById("contenedorLugar")
const inputPosicionPC = document.getElementById("posicionPC")

const linksFiltroUbicacion = document.querySelectorAll(".filtro-ubicacion-directa")

// FUNCIONES
const cargarTodosLosSalones = () => {
    const data = localStorage.getItem("salones")
    if (data === null || data === "" || data === undefined) {
        const estructuraInicial = [
            { tipo: "laboratorios", id: 1, espacios: [] },
            { tipo: "talleres", id: 1, espacios: [] },
            { tipo: "prestamo", id: "prestamo", prestamos: [] }
        ]
        localStorage.setItem("salones", JSON.stringify(estructuraInicial))
        return estructuraInicial
    }
    return JSON.parse(data)
}

const guardarTodosLosSalones = (lista) => {
    localStorage.setItem("salones", JSON.stringify(lista))
}

const cargarTodosLosEquipos = () => {
    const data = localStorage.getItem("equipos")
    if (data === null || data === "" || data === undefined) return []
    return JSON.parse(data)
}

const guardarTodosLosEquipos = (lista) => {
    localStorage.setItem("equipos", JSON.stringify(lista))
}

const obtenerTotalDeFallasDeUnEquipo = (idEquipo) => {
    const datos = localStorage.getItem("tickets")
    if (!datos) return 0
    const lista = JSON.parse(datos)
    const idLimpio = String(idEquipo).trim().toLowerCase()
    return lista.filter(t => {
        const idTicket = t.equipoId || t.idEquipo || ""
        const ticketLimpio = String(idTicket).trim().toLowerCase()
        return ticketLimpio === idLimpio || ticketLimpio === "pc-" + idLimpio
    }).length
}

const encontrarUbicacion = (idEquipo) => {
    const salones = cargarTodosLosSalones()
    const idBuscado = String(idEquipo).trim().toLowerCase()

    for (let s of salones) {
        const items = s.espacios || s.prestamos || []
        for (let j = 0; j < items.length; j++) {
            const item = items[j]
            let idEspacio = ""
            if (item && typeof item === "object") {
                idEspacio = item.id || item.codigo || ""
            } else {
                idEspacio = String(item)
            }
            
            if (String(idEspacio).trim().toLowerCase() === idBuscado) {
                if (s.tipo === "laboratorios") return { nombre: "Laboratorio " + s.id, posicion: item.posicion || j + 1, modo: "salon" }
                if (s.tipo === "talleres") return { nombre: "Taller " + s.id, posicion: item.posicion || j + 1, modo: "salon" }
                if (s.tipo === "prestamo") return { nombre: "prestamo", posicion: 0, modo: "prestamo" }
            }
        }
    }
    return { nombre: "ninguna", posicion: 0, modo: "ninguna" }
}

const transformarFechaAEntero = (fTexto) => {
    if (!fTexto) return 0
    const partes = String(fTexto).split("/")
    if (partes.length !== 3) return 0
    return parseInt(partes[2] + partes[1].padStart(2, "0") + partes[0].padStart(2, "0"))
}

const abrirModalParaEditar = (eq) => {
    modoEdicion = true
    equipoEditando = eq
    
    if (inputIdPC) {
        inputIdPC.value = eq.id || eq.codigo
        inputIdPC.disabled = true
    }

    const ubic = encontrarUbicacion(eq.id || eq.codigo)
    
    if (selectUbicacion) {
        if (ubic.modo === "prestamo") {
            selectUbicacion.value = "prestamo"
            if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none")
        } else if (ubic.modo === "salon") {
            const salones = cargarTodosLosSalones()
            const sEncontrado = salones.find(s => "Laboratorio " + s.id === ubic.nombre || "Taller " + s.id === ubic.nombre)
            if (sEncontrado) {
                selectUbicacion.value = `${sEncontrado.tipo}-${sEncontrado.id}`
            }
            if (contenedorLugar) {
                contenedorLugar.classList.replace("d-none", "d-flex")
                if (inputPosicionPC) inputPosicionPC.value = ubic.posicion
            }
        } else {
            selectUbicacion.value = "ninguna"
            if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none")
        }
    }

    if (btnGuardarEquipo) btnGuardarEquipo.textContent = "Guardar Cambios"
    if (modalEquipo) modalEquipo.classList.replace("d-none", "d-flex")
}

const eliminarEquipoDelSistema = (idEquipo) => {
    if (!confirm(`¿Está seguro de eliminar por completo el dispositivo #${idEquipo}?`)) return

    let listaEquipos = cargarTodosLosEquipos()
    listaEquipos = listaEquipos.filter(e => String(e.id || e.codigo) !== String(idEquipo))
    guardarTodosLosEquipos(listaEquipos)

    const salones = cargarTodosLosSalones()
    salones.forEach(s => {
        if (s.espacios) {
            s.espacios = s.espacios.filter(item => {
                const idItem = item && typeof item === "object" ? (item.id || item.codigo) : item
                return String(idItem) !== String(idEquipo)
            })
        }
        if (s.prestamos) {
            s.prestamos = s.prestamos.filter(item => {
                const idItem = item && typeof item === "object" ? (item.id || item.codigo) : item
                return String(idItem) !== String(idEquipo)
            })
        }
    })
    guardarTodosLosSalones(salones)
    actualizarTabla()
    alert("Dispositivo removido correctamente.")
}

const agregarSalon = (tipo) => {
    const salones = cargarTodosLosSalones()
    const filtrados = salones.filter(s => s.tipo === tipo)
    let mayorId = 0
    filtrados.forEach(s => {
        if (Number(s.id) > mayorId) mayorId = Number(s.id)
    })
    
    const nuevo = {
        tipo: tipo,
        id: mayorId + 1,
        espacios: []
    }
    salones.push(nuevo)
    guardarTodosLosSalones(salones)
    actualizarListasLateralesYSelects()
    alert(`Se ha creado el ${tipo === "laboratorios" ? "Laboratorio" : "Taller"} #${nuevo.id}`)
}

const actualizarListasLateralesYSelects = () => {
    if (listaLaboratorios) listaLaboratorios.innerHTML = ""
    if (listaTalleres) listaTalleres.innerHTML = ""
    if (optgroupLaboratorios) optgroupLaboratorios.innerHTML = ""
    if (optgroupTalleres) optgroupTalleres.innerHTML = ""

    const salones = cargarTodosLosSalones()

    salones.forEach(s => {
        if (s.tipo === "laboratorios") {
            if (listaLaboratorios) {
                const li = document.createElement("li")
                const a = document.createElement("a")
                a.href = "#"
                a.className = "text-decoration-none d-block p-1"
                a.appendChild(document.createTextNode(`Laboratorio ${s.id}`))
                a.addEventListener("click", (e) => {
                    e.preventDefault()
                    filtroUbicacionActual = `Laboratorio ${s.id}`
                    actualizarTabla()
                })
                li.appendChild(a)
                listaLaboratorios.appendChild(li)
            }

            if (optgroupLaboratorios) {
                const opt = document.createElement("option")
                opt.value = `laboratorios-${s.id}`
                opt.appendChild(document.createTextNode(`Laboratorio ${s.id}`))
                optgroupLaboratorios.appendChild(opt)
            }

        } else if (s.tipo === "talleres") {
            if (listaTalleres) {
                const li = document.createElement("li")
                const a = document.createElement("a")
                a.href = "#"
                a.className = "text-decoration-none d-block p-1"
                a.appendChild(document.createTextNode(`Taller ${s.id}`))
                a.addEventListener("click", (e) => {
                    e.preventDefault()
                    filtroUbicacionActual = `Taller ${s.id}`
                    actualizarTabla()
                })
                li.appendChild(a)
                listaTalleres.appendChild(li)
            }

            if (optgroupTalleres) {
                const opt = document.createElement("option")
                opt.value = `talleres-${s.id}`
                opt.appendChild(document.createTextNode(`Taller ${s.id}`))
                optgroupTalleres.appendChild(opt)
            }
        }
    })
}

const actualizarTabla = () => {
    if (!cuerpoTabla) return
    cuerpoTabla.innerHTML = ""
    
    const listaEquipos = cargarTodosLosEquipos()
    let filtrados = []

    listaEquipos.forEach(eq => {
        const idReal = eq.id || eq.codigo
        const ubic = encontrarUbicacion(idReal)
        if (filtroUbicacionActual === "todos") {
            filtrados.push(eq)
        } else if (filtroUbicacionActual === "prestamo" && ubic.modo === "prestamo") {
            filtrados.push(eq)
        } else if (filtroUbicacionActual === "ninguna" && ubic.modo === "ninguna") {
            filtrados.push(eq)
        } else if (ubic.nombre === filtroUbicacionActual) {
            filtrados.push(eq)
        }
    })

    if (filtroEstado && filtroEstado.value !== "") {
        const buscarActivo = filtroEstado.value === "activo"
        filtrados = filtrados.filter(eq => {
            const eqActivo = eq.activo === true || String(eq.activo).toLowerCase() === "activo" || String(eq.activo).toLowerCase() === "true"
            return eqActivo === buscarActivo
        })
    }

    if (filtroIncidencias && filtroIncidencias.value !== "") {
        filtrados.sort((a, b) => {
            const incA = obtenerTotalDeFallasDeUnEquipo(a.id || a.codigo)
            const incB = obtenerTotalDeFallasDeUnEquipo(b.id || b.codigo)
            return filtroIncidencias.value === "menor" ? incA - incB : incB - incA
        })
    }

    if (filtroIntervencion && filtroIntervencion.value !== "") {
        filtrados.sort((a, b) => {
            const fA = transformarFechaAEntero(a.ultimaIntervencion || a.fecha)
            const fB = transformarFechaAEntero(b.ultimaIntervencion || b.fecha)
            return filtroIntervencion.value === "reciente" ? fB - fA : fA - fB
        })
    }

    filtrados.forEach(eq => {
        const idReal = eq.id || eq.codigo
        const ubic = encontrarUbicacion(idReal)
        const fallas = obtenerTotalDeFallasDeUnEquipo(idReal)
        const esActivo = eq.activo === true || String(eq.activo).toLowerCase() === "activo" || String(eq.activo).toLowerCase() === "true"

        const tr = document.createElement("tr")

        const tdId = document.createElement("td")
        tdId.appendChild(document.createTextNode(idReal))

        const tdDetalleUbic = document.createElement("td")
        const txtDetalle = ubic.modo === "salon" ? `${ubic.nombre} - Banco ${ubic.posicion}` : (ubic.modo === "prestamo" ? "Dispositivo para préstamo" : "Sin asignar")
        tdDetalleUbic.appendChild(document.createTextNode(txtDetalle))

        const tdEstado = document.createElement("td")
        tdEstado.appendChild(document.createTextNode(esActivo ? "Activo" : "Inactivo"))

        const tdFallas = document.createElement("td")
        tdFallas.appendChild(document.createTextNode(fallas))

        const tdAcciones = document.createElement("td")
        const btnEditar = document.createElement("button")
        btnEditar.className = "btn btn-primary me-2"
        btnEditar.appendChild(document.createTextNode("Editar"))
        btnEditar.addEventListener("click", () => abrirModalParaEditar(eq))

        const btnEliminar = document.createElement("button")
        btnEliminar.className = "btn btn-danger me-2"
        btnEliminar.appendChild(document.createTextNode("Eliminar"))
        btnEliminar.addEventListener("click", () => eliminarEquipoDelSistema(idReal))

        tdAcciones.appendChild(btnEditar)
        tdAcciones.appendChild(btnEliminar)

        tr.appendChild(tdId)
        tr.appendChild(tdDetalleUbic)
        tr.appendChild(tdEstado)
        tr.appendChild(tdFallas)
        tr.appendChild(tdAcciones)

        cuerpoTabla.appendChild(tr)
    })
}

// ARRANQUE INICIAL
cargarTodosLosSalones()
actualizarListasLateralesYSelects()
actualizarTabla()

// EVENTOS
if (formulario) {
    formulario.addEventListener("submit", (e) => {
        e.preventDefault()

        const codigo = inputIdPC ? inputIdPC.value.trim() : ""
        const lugarVal = selectUbicacion ? selectUbicacion.value : ""
        const posicionVal = inputPosicionPC ? parseInt(inputPosicionPC.value) : 1

        if (codigo === "" || lugarVal === "") {
            alert("Complete todos los campos obligatorios del formulario.")
            return
        }

        let listaEquipos = cargarTodosLosEquipos()

        if (!modoEdicion) {
            const existe = listaEquipos.some(e => String(e.id || e.codigo) === codigo)
            if (existe) {
                alert("El código de este equipo ya se encuentra registrado.")
                return
            }

            const nuevoEq = {
                id: codigo,
                codigo: codigo,
                activo: true,
                ultimaIntervencion: new Date().toLocaleDateString('es-ES'),
                fecha: new Date().toLocaleDateString('es-ES')
            }
            listaEquipos.push(nuevoEq)
        } else {
            const eq = listaEquipos.find(e => String(e.id || e.codigo) === String(equipoEditando.id || equipoEditando.codigo))
            if (eq) {
                eq.ultimaIntervencion = new Date().toLocaleDateString('es-ES')
            }
        }

        guardarTodosLosEquipos(listaEquipos)

        const salones = cargarTodosLosSalones()
        salones.forEach(s => {
            if (s.espacios) {
                s.espacios = s.espacios.filter(item => {
                    const idItem = item && typeof item === "object" ? (item.id || item.codigo) : item
                    return String(idItem) !== codigo
                })
            }
            if (s.prestamos) {
                s.prestamos = s.prestamos.filter(item => {
                    const idItem = item && typeof item === "object" ? (item.id || item.codigo) : item
                    return String(idItem) !== codigo
                })
            }
        })

        if (lugarVal === "prestamo") {
            const pSalon = salones.find(s => s.tipo === "prestamo")
            if (pSalon) {
                if (!pSalon.prestamos) pSalon.prestamos = []
                pSalon.prestamos.push({ id: codigo, prestado: false })
            }
        } else if (lugarVal !== "ninguna") {
            const partes = lugarVal.split("-")
            const tipoS = partes[0]
            const idS = parseInt(partes[1])
            const target = salones.find(s => s.tipo === tipoS && Number(s.id) === idS)
            if (target) {
                if (!target.espacios) target.espacios = []
                target.espacios.push({ id: codigo, posicion: posicionVal || 1, prestado: false })
            }
        }

        guardarTodosLosSalones(salones)

        modoEdicion = false
        equipoEditando = null
        formulario.reset()
        if (inputIdPC) inputIdPC.disabled = false
        if (btnGuardarEquipo) btnGuardarEquipo.textContent = "Guardar PC"
        if (modalEquipo) modalEquipo.classList.replace("d-flex", "d-none")
        if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none")
        
        actualizarTabla()
        alert("Operación completada con éxito.")
    })
}

if (btnAbrirModalRegistrar) {
    btnAbrirModalRegistrar.addEventListener("click", () => {
        modoEdicion = false
        equipoEditando = null
        if (formulario) formulario.reset()
        if (inputIdPC) inputIdPC.disabled = false
        if (btnGuardarEquipo) btnGuardarEquipo.textContent = "Guardar PC"
        if (modalEquipo) modalEquipo.classList.replace("d-none", "d-flex")
        if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none")
    })
}

if (btnCancelarEquipo) {
    btnCancelarEquipo.addEventListener("click", () => {
        modoEdicion = false
        equipoEditando = null
        if (formulario) formulario.reset()
        if (inputIdPC) inputIdPC.disabled = false
        if (modalEquipo) modalEquipo.classList.replace("d-flex", "d-none")
        if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none")
    })
}

if (btnAgregarL) btnAgregarL.addEventListener("click", () => agregarSalon("laboratorios"))
if (btnAgregarT) btnAgregarT.addEventListener("click", () => agregarSalon("talleres"))

if (filtroEstado) filtroEstado.addEventListener("change", () => actualizarTabla())
if (filtroIncidencias) filtroIncidencias.addEventListener("change", () => actualizarTabla())
if (filtroIntervencion) filtroIntervencion.addEventListener("change", () => actualizarTabla())

if (selectUbicacion) {
    selectUbicacion.addEventListener("change", (e) => {
        if (contenedorLugar) {
            if (e.target.value !== "ninguna" && e.target.value !== "prestamo" && e.target.value !== "") {
                contenedorLugar.classList.replace("d-none", "d-flex")
            } else {
                contenedorLugar.classList.replace("d-flex", "d-none")
            }
        }
    })
}

if (btnAbrirUbicaciones) {
    btnAbrirUbicaciones.addEventListener("click", () => {
        if (barralateralUbicaciones) barralateralUbicaciones.classList.add("abierto")
    })
}

if (btnCerrarUbicaciones) {
    btnCerrarUbicaciones.addEventListener("click", () => {
        if (barralateralUbicaciones) barralateralUbicaciones.classList.remove("abierto")
    })
}

linksFiltroUbicacion.forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault()
        const ubicacionAtributo = link.getAttribute("data-ubicacion")
        if (ubicacionAtributo) {
            filtroUbicacionActual = ubicacionAtributo
            actualizarTabla()
        }
    })
})