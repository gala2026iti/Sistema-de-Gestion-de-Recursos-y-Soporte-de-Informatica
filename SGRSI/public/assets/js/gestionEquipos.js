// VARIABLES
let modoEdicion = false
let equipoEditando = null
let filtroUbicacionActual = "todos"

const btnAbrirUbicaciones = document.getElementById("btnAbrirUbicaciones")
const btnCerrarUbicaciones = document.getElementById("btnCerrarUbicaciones")
const barralateralUbicaciones = document.getElementById("barraLateralSalones")

const btnAgregarL = document.getElementById("btnAgregarL")
const btnAgregarT = document.getElementById("btnAgregarT")

const formulario = document.getElementById("formEquipo")
const cuerpoTabla = document.querySelector("#tablaEquipos tbody")

const modalEquipo = document.getElementById("modalEquipo")
const btnAbrirModalRegistrar = document.getElementById("btnRegistrarEquipo")
const btnGuardarEquipo = document.getElementById("btnGuardarEquipo")
const btnCancelarEquipo = document.getElementById("btnCancelarEquipo")

const filtroEstado = document.getElementById("filtroEstado")
const filtroIncidencias = document.getElementById("filtroIncidencias")
const filtroIntervencion = document.getElementById("filtroIntervencion")

const listaLaboratorios = document.getElementById("listaLaboratorios")
const listaTalleres = document.getElementById("listaTalleres")

const grupoLaboratorios = document.getElementById("grupoLaboratorio")
const grupoTalleres = document.getElementById("grupoTalleres")

const inputIdPC = document.getElementById("idPC")
const selectUbicacion = document.getElementById("ubicacion")
const contenedorLugar = document.getElementById("contenedorLugar")
const inputPosicionPC = document.getElementById("posicionPC")

const linksFiltroUbicacion = document.querySelectorAll(".filtro-ubicacion-directa")

const obtenerCedulaLocal = () => {
    const usuario = localStorage.getItem("usuario")
    if (usuario === null || usuario === undefined || usuario === "") return "N/A"
    const usuarioLocalJSON = JSON.parse(usuario) 
    return usuarioLocalJSON.usuario

}

// FUNCIONES
const cargarSalones = () => {
    const salones = localStorage.getItem("salones")
    if (salones === null || salones === "" || salones === undefined) return []
    return JSON.parse(salones)
}

const guardarSalones = (lista) => {
    localStorage.setItem("salones", JSON.stringify(lista))
}


const eliminarSalon = (salon) => {
    const salonesViejos = cargarSalones()
    const salonesNuevos = []
    salonesViejos.forEach(s => {
        if (s === null || s === undefined || s === "") return //Los return vacios salen de la funcion si algo no se cumple, no rompe nada porque no retorna nada a nadie
        if (!(s.tipo === salon.tipo && s.id === salon.id)) {
            salonesNuevos.push(s)
        }
    })
    guardarSalones(salonesNuevos)
}

const recibirIDlibre = (tipoSalon) => {
    const salonesFiltrados = []
    const salones = cargarSalones()

    salones.forEach(s => {
        if (s.tipo === tipoSalon) salonesFiltrados.push(s)
    })

    let idLibre = 1
    while (salonesFiltrados.some(s => s.id === idLibre)) {
        idLibre++ //Si el id está ocupado, se suma 1 y se vuelve a validar
    }

    return idLibre //Cuando el id no existe, rompe el bucle y devuelve el numero en el que no existe
}

const cargarEquipos = () => {
    const equipos = localStorage.getItem("equipos")
    if (equipos === null || equipos === "" || equipos === undefined) return []
    return JSON.parse(equipos)
}

const guardarEquipos = (lista) => {
    localStorage.setItem("equipos", JSON.stringify(lista))
}
const validarDatosEquipo = (codigo, ubicacion, posicion) => {
    if (codigo === "") {
        return "El código del equipo es obligatorio."
    }
    const formatoID = /^[A-Za-z0-9]+$/

    if (!formatoID.test(codigo)) {
        return "El código del equipo solo puede contener letras y números."
    }
    if (codigo.length < 3 || codigo.length > 15) {
        return "El código del equipo debe tener entre 3 y 15 caracteres."
    }
    if (ubicacion === "") {
        return "Debe seleccionar una ubicación."
    }
    if (
        ubicacion !== "ninguna" &&
        ubicacion !== "prestamo"
    ) {
        if (isNaN(posicion)) {
            return "Debe ingresar una posición válida."
        }
        if (posicion <= 0) {
            return "La posición debe ser mayor a cero."
        }
    }
    return null
}
const cargarTickets = () => {
    const tickets = localStorage.getItem("tickets")
    if (tickets === null || tickets === undefined || tickets === "") return []
    return JSON.parse(tickets)
}

const totalFallasEquipo = (idEquipo) => {
    const tickets = cargarTickets()
    const idLimpio = idEquipo.trim().toLowerCase()

    return tickets.filter(t => { //Filtra los tickets que coincidan en ID de equipo
        const idTicket = t.equipoId
        const ticketLimpio = idTicket.trim().toLowerCase()
        return ticketLimpio === idLimpio
    }).length
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

const registrarHistorialEquipos = (idEquipo, detalle) => {
    const datos = localStorage.getItem("registroEquipos")
    let historial = datos ? JSON.parse(datos) : []

 

    historial.push({
        id: historial.length + 1,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        detalleEquipo: detalle,
        idEquipo: idEquipo
    })
    localStorage.setItem("registroEquipos", JSON.stringify(historial))
}

const registrarHistorialSalones = (salon, modificacion) => {
    const datos = localStorage.getItem("registroSalones")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        modificacion: modificacion,
        salonAfectado: salon
    })
    localStorage.setItem("registroEquipos", JSON.stringify(historial))
}

const encontrarUbicacion = (idEquipo) => {
    const salones = cargarSalones()
    const idBuscado = idEquipo.trim().toLowerCase()

    for (let s of salones) { //Recorre todos los salones, se usa este tipo de for y no forEach ya que el forEach presenta problemas con los return
        const espacios = s.espacios || []
        for (let j = 0; j < espacios.length; j++) { //Recorre todos los espacios de cada salon
            const espacio = espacios[j]
            let idEspacio = espacio.id

            if (idEspacio.trim().toLowerCase() === idBuscado) { //Al encontrar la posicion de la pc dentro de un salon, se verifica su tipo, si no se encuentra, se toma como que no esta asignado
                if (s.tipo === "laboratorio") return { nombre: "Laboratorio " + s.id, posicion: espacio.posicion, modo: "salon" }
                if (s.tipo === "taller") return { nombre: "Taller " + s.id, posicion: espacio.posicion, modo: "salon" }
                if (s.tipo === "prestamo") return { nombre: "prestamo", posicion: 0, modo: "prestamo" }
            }
        }
    }
    return { nombre: "Ningun lado", posicion: 0, modo: "ninguna" }
} 

const buscarNombre = (cedulaUsuario) => {
    let usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => u.usuario === cedulaUsuario)
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const transformarFechaAEntero = (fechaTexto) => {
    if (!fechaTexto) return 0
    const partes = (fechaTexto).split("/") //El metodo split parte un String en varias partes en un array, y su parametro es el delimitador
    if (partes.length !== 3) return 0
    return parseInt(partes[2] + partes[1].padStart(2, "0") + partes[0].padStart(2, "0")) //El metodo padStart añade ceros con el fin de que la longitud del numero coincida con lo esperado

    //Fuente sobre padStart: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/String/padStart

}

const abrirModalParaEditar = (eq) => {
    modoEdicion = true
    equipoEditando = eq

    if (inputIdPC) {
        inputIdPC.value = eq.id
        inputIdPC.disabled = true //Desactiva el campo de edicion de ID, ya que el equipo ya existe
    }

    const ubic = encontrarUbicacion(eq.id)

    if (selectUbicacion) {
        if (ubic.modo === "prestamo") { //Si la compu es de prestamo, no tiene salon
            selectUbicacion.value = "prestamo"
            if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none") //Como la compu no tiene salon, se oculta la opcion para elegir espacio del salon
        } else if (ubic.modo === "salon") {
            const salones = cargarSalones()
            const sEncontrado = salones.find(s => "Laboratorio " + s.id === ubic.nombre || "Taller " + s.id === ubic.nombre)
            if (sEncontrado) {
                selectUbicacion.value = `${sEncontrado.tipo}-${sEncontrado.id}`
            }
            if (contenedorLugar) {
                contenedorLugar.classList.replace("d-none", "d-flex")
                if (inputPosicionPC) inputPosicionPC.value = ubic.posicion
            }
        } else {
            selectUbicacion.value = "ninguna" //Al no pertenecer a un salon, tampoco tiene lugar especifico
            if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none")
        }
    }

    if (btnGuardarEquipo) btnGuardarEquipo.innerText = "Guardar Cambios"
    if (modalEquipo) modalEquipo.classList.replace("d-none", "d-flex")
}

const actualizarDatosUbicaciones = () => {

    if (listaLaboratorios) listaLaboratorios.innerHTML = "" //Se vacian las listas para poder ingresar los mismos valores pero actualizados y organizados
    if (listaTalleres) listaTalleres.innerHTML = ""
    if (grupoLaboratorios) grupoLaboratorios.innerHTML = ""
    if (grupoTalleres) grupoTalleres.innerHTML = ""

    const salones = cargarSalones()
    if (salones) {
        salones.forEach(s => {

            let nombreSalon = s.tipo === "laboratorio" ? "Laboratorio" : "Taller"

            const li = document.createElement("li")
            li.className = `d-flex justify-content-between align-espacios-center`

            const a = document.createElement("a")
            a.href = "#" //La redireccion no lleva a ningun lado, buscando un id vacio
            a.className = "text-decoration-none p-1"
            a.innerText = `${nombreSalon} ${s.id}`

            a.addEventListener("click", (e) => { //Se le añade el evento para que modifique la tabla con la data correspondiente
                e.preventDefault()
                filtroUbicacionActual = `${nombreSalon} ${s.id}`
                actualizarTabla()
            })



            const btnBorrarSalon = document.createElement("button")
            btnBorrarSalon.innerText = "X"
            btnBorrarSalon.classList = "btn-cerrar-lateral" //Aunque el nombre no es el mas conveniente, funciona como boton de borrar, esta clase se tomo del boton X de ubicaciones
            btnBorrarSalon.style.color = "#ff9090"

            btnBorrarSalon.addEventListener("click", (e) => {
                if (!confirm("¿Estas seguro de que querés eliminar este salón?\nTodos los equipos dentro del mismo serán desasginados.")) return
                eliminarSalon(s) //Aunque este metodo solo se usa aca, se mantiene el atajo para mejor orden de código
                registrarHistorialSalones(`${nombreSalon} ${s.id}`, "eliminacion")


                if (filtroUbicacionActual === `${nombreSalon} ${s.id}`) filtroUbicacionActual = "todos"
                actualizarDatosUbicaciones()
                actualizarTabla()

            })

            li.appendChild(a)
            li.appendChild(btnBorrarSalon)
            if (s.tipo === "laboratorio") listaLaboratorios.appendChild(li)
            else listaTalleres.appendChild(li)

            const opcionSalon = document.createElement("option")
            opcionSalon.value = `${s.tipo}-${s.id}`
            opcionSalon.innerText = `${nombreSalon} ${s.id}`

            if (s.tipo === "laboratorio" && grupoLaboratorios) grupoLaboratorios.appendChild(opcionSalon)
            if (s.tipo === "taller" && grupoTalleres) grupoTalleres.appendChild(opcionSalon)
        })
    }
}

const ordenarSalones = () => {
    const salones = cargarSalones()

    const salonesLab = salones.filter(s => s.tipo === "laboratorio")
    const salonesLabOrdenados = salonesLab.sort((a, b) => a.id - b.id)

    const salonesTal = salones.filter(s => s.tipo === "taller")
    const salonesTalOrdenados = salonesTal.sort((a, b) => a.id - b.id)

    const salonesOrdenados = salonesLabOrdenados.concat(salonesTalOrdenados)

    guardarSalones(salonesOrdenados)

    //Fuente sobre concat: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Array/concat
}

const desactivarEquipo = (idEquipo) => {
    if (!confirm(`¿Está seguro de desactivar el dispositivo #${idEquipo}?\nEl equipo se desasignará de donde sea que se encuentre.`)) return

    let listaEquipos = cargarEquipos()

    listaEquipos.forEach(e => {
        if (e.id === idEquipo) e.activo = false
    })

    guardarEquipos(listaEquipos)
    registrarHistorialEquipos(idEquipo, `El usuario ${obtenerCedulaLocal()} (${buscarNombre(obtenerCedulaLocal())}) desactivo el equipo ${idEquipo}, removiendolo del ${encontrarUbicacion(idEquipo).nombre}.`)

    const salones = cargarSalones()
    salones.forEach(s => { //Este forEach se asegura de que en ningun salon quede rastro del equipo a borrar
        if (s.espacios) { //Verifica si el salon tiene espacios para no trabajr con null o array vacio
            s.espacios = s.espacios.filter(e => {
                const idItem = e.id
                return (idItem) !== (idEquipo)
            })
        }
    })
    guardarSalones(salones)
    actualizarTabla()
    alert("Dispositivo desactivado correctamente.")
}

const activarEquipo = (idEquipo) => {

    let listaEquipos = cargarEquipos()

    listaEquipos.forEach(e => {
        if (e.id === idEquipo) e.activo = true
    })

    guardarEquipos(listaEquipos)

    actualizarTabla()
    registrarHistorialEquipos(idEquipo, `El usuario ${obtenerCedulaLocal()} (${buscarNombre(obtenerCedulaLocal())}) activo el equipo ${idEquipo}.`)
    alert("Dispositivo activado correctamente.")

}

const agregarSalon = (tipo) => {
    const salones = cargarSalones()
    const filtrados = salones.filter(s => s.tipo === tipo)

    const nuevo = {
        tipo: tipo,
        id: recibirIDlibre(tipo),
        espacios: []
    }
    salones.push(nuevo)
    guardarSalones(salones)
    registrarHistorialSalones(`${tipo === "laboratorio" ? "Laboratorrio" : "Taller" } ${nuevo.id}`, "creacion")
    alert(`Se ha creado el ${tipo === "laboratorio" ? "Laboratorio" : "Taller"} #${nuevo.id}`)
    ordenarSalones()
    actualizarDatosUbicaciones()

}

ordenarEspacios = (espacios) => {
    const espaciosOrdenados = espacios.sort((a, b) =>
        a.posicion - b.posicion
    )
    return espaciosOrdenados
}

const actualizarTabla = () => {
    if (!cuerpoTabla) return //Si no hay cuerpo de tabla (vaya saber porque) retorna nada, con el din de salir del método
    cuerpoTabla.innerHTML = "" //Y ta, de todos modos lo va a vaciar, asi que ta

    const listaEquipos = cargarEquipos()
    let filtrados = []

    listaEquipos.forEach(eq => {
        const idReal = eq.id
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
            const eqActivo = eq.activo
            return eqActivo === buscarActivo
        })
    }

            if (filtrados.length === 0) {
        const filaSinResultados = document.createElement("tr")
        const celdaSinResultados = document.createElement("td")

        celdaSinResultados.colSpan = 9 // colSpan es para que ocupe todas las columnas, porque sino queda solo en la primera y se ve re gagá

        celdaSinResultados.className = "text-center py-4 text-muted bg-light fw-semibold"
        celdaSinResultados.innerText = "No se encontraron Equipos."

        filaSinResultados.appendChild(celdaSinResultados)
        cuerpoTabla.appendChild(filaSinResultados)

    } else {

    if (filtroIncidencias && filtroIncidencias.value !== "") {
        filtrados.sort((a, b) => {
            const incA = totalFallasEquipo(a.id)
            const incB = totalFallasEquipo(b.id)
            return filtroIncidencias.value === "menor" ? incA - incB : incB - incA //los dos metodos posibles para filtrar son mayor y menor, en caso de ser uno, se realiza una operación que filtra de un sentido a otro, pero en caso contrario, se filtrara en el sentido inverso
        })
    }

    if (filtroIntervencion && filtroIntervencion.value !== "") {
        filtrados.sort((a, b) => {
            const fA = transformarFechaAEntero(a.ultimaIntervencion)
            const fB = transformarFechaAEntero(b.ultimaIntervencion)
            return filtroIntervencion.value === "reciente" ? fB - fA : fA - fB
        })
    }


    filtrados.forEach(eq => {
        const idReal = eq.id
        const ubic = encontrarUbicacion(idReal)
        const fallas = totalFallasEquipo(idReal)
        const esActivo = eq.activo

        const tr = document.createElement("tr")

        const tdId = document.createElement("td")
        tdId.innerText = idReal

        const tdDetalleUbic = document.createElement("td")
        const txtDetalle = ubic.modo === "salon" ? `${ubic.nombre} - Posicion ${ubic.posicion}` : (ubic.modo === "prestamo" ? "Dispositivo para préstamo" : "Sin asignar")
        tdDetalleUbic.innerText = txtDetalle

        const tdEstado = document.createElement("td")
        tdEstado.innerText = esActivo ? "Activo" : "Inactivo"

        const tdFallas = document.createElement("td")
        tdFallas.innerText = fallas

        const tdAcciones = document.createElement("td")

        const btnEditar = document.createElement("button")
        btnEditar.className = "btn btn-primary btn-sm me-1"
        btnEditar.innerText = "Editar"
        btnEditar.addEventListener("click", () => abrirModalParaEditar(eq))
        tdAcciones.appendChild(btnEditar)

        if (ubic.modo === "salon") {
            const btnQuitar = document.createElement("button")
            btnQuitar.className = "btn btn-warning btn-sm me-1"
            btnQuitar.innerText = "Quitar del salón"
            btnQuitar.addEventListener("click", () => {
                if (confirm(`¿Desea desvincular el dispositivo #${idReal} de su salón actual?`)) {
                    const salones = cargarSalones()
                    salones.forEach(s => {
                        if (s.espacios) {
                            s.espacios = s.espacios.filter(e => {
                                return (e.id) !== idReal
                            })
                        }
                    })
                    guardarSalones(salones)
                    actualizarTabla()
                    alert("El equipo se removió del salón y quedó sin asignación.")
                }
            })
            tdAcciones.appendChild(btnQuitar)
        }

        const btnVerIncidencias = document.createElement("button")
        btnVerIncidencias.className = "btn btn-danger btn-sm me-1 text-white"
        btnVerIncidencias.innerText = `Ver Incidencias`
        btnVerIncidencias.addEventListener("click", () => {
            window.location.href = `historialGeneral.php?equipoId=${idReal}`
        })
        tdAcciones.appendChild(btnVerIncidencias)

        const btnEliminar = document.createElement("button")
        if (eq.activo) {
            btnEliminar.className = "btn btn-danger btn-sm"
            btnEliminar.innerText = "Desactivar"
            btnEliminar.addEventListener("click", () => desactivarEquipo(idReal))
        } else {
            btnEliminar.className = "btn btn-success btn-sm"
            btnEliminar.innerText = "Activar"
            btnEliminar.addEventListener("click", () => activarEquipo(idReal))
        }
        tdAcciones.appendChild(btnEliminar)

        tr.appendChild(tdId)
        tr.appendChild(tdDetalleUbic)
        tr.appendChild(tdEstado)
        tr.appendChild(tdFallas)
        tr.appendChild(tdAcciones)

        cuerpoTabla.appendChild(tr)
    })
    }
}

// EVENTOS
if (formulario) {
    formulario.addEventListener("submit", (e) => {
        let salonViejo = null

        e.preventDefault()

        const codigo = inputIdPC ? inputIdPC.value.trim() : ""
        const lugarVal = selectUbicacion ? selectUbicacion.value : ""
        const posicionVal = inputPosicionPC ? parseInt(inputPosicionPC.value) : ""

        const errorValidacion = validarDatosEquipo(
        codigo,
        lugarVal,
        posicionVal
    )
if (errorValidacion !== null) {
    alert("Error: " + errorValidacion)
    return
}

        if (codigo === "" || lugarVal === "") {
            alert("Error: Complete todos los campos requeridos del formulario.")
            return
        }

        let listaEquipos = cargarEquipos()

        const salones = cargarSalones()

        if (!(lugarVal === "ninguna" || lugarVal === "prestamo" || lugarVal === "")) {
            const salonEspecifico = salones.find(s => `${s.tipo}-${s.id}` === lugarVal)
            const salonOcupado = salonEspecifico.espacios.some(e => {
                const esElMismoEquipo = modoEdicion && e.id === codigo
                return e.posicion === posicionVal && !esElMismoEquipo
            })
            if (salonOcupado) {
                alert("Error: La posicion elegida ya esta ocupada por otro equipo.")
                return
            }
        }

        if (!modoEdicion) {
            const existe = listaEquipos.some(e => e.id === codigo) //El metodo some busca algun valor en el array que coincida con lo buscado
            if (existe) {
                alert("Error: El código de este equipo ya se encuentra registrado.")
                return //Sale de la función principal

                //Fuente del metodo some(): https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Array/some
            }

            const nuevoEq = {
                id: codigo,
                activo: true,
                ultimaIntervencion: new Date().toLocaleDateString('es-ES'),
                fecha: obtenerFecha("fecha"),
                hora: obtenerFecha("hora"),
            }

            listaEquipos.push(nuevoEq)
        } else {
            const eq = listaEquipos.find(e => e.id === equipoEditando.id)
            if (eq) { //Si el equipo existe, reemplaza su fecha de última intervención
                salonViejo = encontrarUbicacion(eq.id).nombre
                eq.ultimaIntervencion = new Date().toLocaleDateString('es-ES')

            }
        }

        guardarEquipos(listaEquipos)

        salones.forEach(s => {
            if (s.espacios) {
                s.espacios = s.espacios.filter(e => {
                    return e.id !== codigo
                })
            }
        })

        if (lugarVal === "prestamo") {
            let pSalon = salones.find(s => s.tipo === "prestamo")
            if (!pSalon) { //Si no existe el salon de prestamos, el sistema lo crea
                pSalon = {
                    tipo: "prestamo",
                    id: 1,
                    espacios: []
                }
                salones.push(pSalon)
            }
            pSalon.espacios.push({ id: codigo, prestado: false })



        } else if (lugarVal !== "ninguna") {
            const partes = lugarVal.split("-")
            const tipoS = partes[0]
            const idS = parseInt(partes[1])
            const salonBuscado = salones.find(s => s.tipo === tipoS && s.id === idS)
            if (salonBuscado) {
                if (!salonBuscado.espacios) salonBuscado.espacios = []
                salonBuscado.espacios.push({ id: codigo, posicion: posicionVal })
                salonBuscado.espacios = ordenarEspacios(salonBuscado.espacios)
            }
        }

        guardarSalones(salones)
        if(modoEdicion){
            if(salonViejo !== encontrarUbicacion(codigo).nombre) registrarHistorialEquipos(codigo, `El usuario ${obtenerCedulaLocal()} (${buscarNombre(obtenerCedulaLocal())}) modificó la ubicación del equipo ${codigo}, el equipo se movió de ${salonViejo} a ${encontrarUbicacion(codigo).nombre}.`)
        } else {
            registrarHistorialEquipos(codigo, `El usuario ${obtenerCedulaLocal()} (${buscarNombre(obtenerCedulaLocal())}) registró el equipo ${codigo}, asignado a ${encontrarUbicacion(codigo).nombre}.`)
        }
        modoEdicion = false
        equipoEditando = null
        formulario.reset()
        if (inputIdPC) inputIdPC.disabled = false
        if (btnGuardarEquipo) btnGuardarEquipo.innerText = "Guardar PC"
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
        if (btnGuardarEquipo) btnGuardarEquipo.innerText = "Guardar PC"
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

if (btnAgregarL) btnAgregarL.addEventListener("click", () => agregarSalon("laboratorio"))
if (btnAgregarT) btnAgregarT.addEventListener("click", () => agregarSalon("taller"))

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
        const ubicacionAtributo = link.getAttribute("salones-ubicacion")
        if (ubicacionAtributo) {
            filtroUbicacionActual = ubicacionAtributo
            actualizarTabla()
        }
    })
})

cargarSalones()
actualizarDatosUbicaciones()
actualizarTabla()