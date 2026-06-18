/* 
El archivo ingresoSolicitudes.js fue el primer
js creado para el manejo de forms y tablas,
futuros js con el mismo funcionamiento
seran copias adaptadas al caso.

Sin embargo, gestionUsuarios.js implementa funcionamiento de
tablas y de filtrado, además de montón de nuevas implementaciones
que seran usadas como guia a futuros DOM.
*/

// VARIABLES 

const btnAbrirUbicaciones = document.getElementById("btn-abrir-ubicaciones")
const btnCerrarUbicaciones = document.getElementById("btn-cerrar-ubicaciones")
const barralateralUbicaciones = document.getElementById("barralateral-ubicaciones")

const btnAgregarL = document.getElementById("btnAgregarL")
const btnAgregarT = document.getElementById("btnAgregarT")

let modoEdicion = false
let equipoEditando = null

const formulario = document.getElementById("formEquipo")
const tabla = document.getElementById("tablaEquipos")
const cuerpoTabla = tabla.querySelector("tbody")

const guardarEquipo = document.getElementById("btnGuardarEquipo")
const cancelarEquipo = document.getElementById("btnCancelarEquipo")

const filtroEstado = document.getElementById("filtroEstado")
const filtroIncidencias = document.getElementById("filtronIncidencias")
const filtroIntervención = document.getElementById("filtroIntervencion")

const usuarioLocal = localStorage.getItem("usuario")
const usuarioLocalJSON = JSON.parse(usuarioLocal)

const listaLaboratorios = document.getElementById("listaLaboratorios")
const listaTalleres = document.getElementById("listaTalleres")

// FUNCIONES   

const actualizarLista = () => {

    listaLaboratorios.innerHTML = ""
    listaTalleres.innerHTML = ""

    const laboratorios = cargarSalones("laboratorios")
    const talleres = cargarSalones("talleres")

for (const laboratorio of laboratorios) {

    const li = document.createElement("li")

    const enlace = document.createElement("a")
    enlace.href = "#"
    enlace.textContent = `Laboratorio ${laboratorio.id}`

    const botonEliminar = document.createElement("button")
    botonEliminar.textContent = "X"
    botonEliminar.className = "btn-accion-salon"

    botonEliminar.addEventListener("click", () => {
        eliminarSalon("laboratorios", laboratorio.id)
    })

    li.appendChild(enlace)
    li.appendChild(botonEliminar)

    listaLaboratorios.appendChild(li)

}

for (const taller of talleres) {

    const li = document.createElement("li")

    const enlace = document.createElement("a")
    enlace.href = "#"
    enlace.textContent = `Taller ${taller.id}`

    const botonEliminar = document.createElement("button")
    botonEliminar.textContent = "X"
    botonEliminar.className = "btn-accion-salon"

    botonEliminar.addEventListener("click", () => {
        eliminarSalon("talleres", taller.id)
    })

    li.appendChild(enlace)
    li.appendChild(botonEliminar)

    listaTalleres.appendChild(li)

}
}

const cargarSalones = (tipo) => {
    if (tipo === "laboratorios") {
        const laboratorios = localStorage.getItem("laboratorios")
        if (laboratorios == null) {
            return []
        } else {
            return JSON.parse(laboratorios)
        }
    } else if (tipo === "talleres") {
        const talleres = localStorage.getItem("talleres")
        if (talleres == null) {
            return []
        } else {
            const talleresJSON = JSON.parse(talleres)
            return talleresJSON
        }
    }
}

const agregarSalon = (tipo) => {
    if (tipo === "laboratorios") {
        const laboratorio = {
            id: cargarSalones("laboratorios").length + 1,
            espacios: []
        }
        const laboratoriosActualizados = cargarSalones("laboratorios")
        laboratoriosActualizados.push(laboratorio)
        localStorage.setItem("laboratorios", JSON.stringify(laboratoriosActualizados))

    } else if (tipo === "talleres") {
        const taller = {
            id: cargarSalones("talleres").length + 1,
            espacios: []
        }
        const talleresActualizados = cargarSalones("talleres")
        talleresActualizados.push(taller)
        localStorage.setItem("talleres", JSON.stringify(talleresActualizados))
    }

    actualizarLista()
}

const desvincularCompus = (id) => {
    const computadoras = cargarComputadoras()

    const computadorasFiltradas = computadoras.filter(computadora => computadora.ubicacion === id)

    computadorasFiltradas.forEach(computadora => {
        computadora.ubicacion = null
    })

    localStorage.setItem("computadoras", JSON.stringify(computadoras))
}

const actualizarCamposSalon = () => {

}

const limpiarCampos = () => {
    formulario.reset()
}

const guardarCompu = (compu) => {
    const compus = (cargarComputadoras());
    compus.push(compu);
    localStorage.setItem("computadoras", JSON.stringify(compus));

    alert("Computadora registrada con éxito!")

    limpiarCampos()
    actualizarTabla()
}

const eliminarCompu = (id) => {
    const computadoras = cargarComputadoras()
    const computadorasFiltradas = computadoras.filter(computadora => computadora.id !== id)
    localStorage.setItem("computadoras", JSON.stringify(computadorasFiltradas))
}

const eliminarSalon = (tipo, id) => {

    if (confirm(`¿Estás seguro de que quieres eliminar este ${tipo === "laboratorios" ? "laboratorio" : "taller"}?`)) {
        if (confirm(`ESTA ACCIÓN DESVINCULARA LAS COMPUTADORAS DEL ${tipo === "laboratorios" ? "laboratorio" : "taller"}?, ¿DESEAS CONTINUAR?`)) {

            if (tipo === "laboratorios") {

                const laboratorios = cargarSalones("laboratorios")
                const laboratoriosFiltrados = laboratorios.filter(laboratorio => laboratorio.id !== id)
                localStorage.setItem("laboratorios", JSON.stringify(laboratoriosFiltrados))


            } else if (tipo === "talleres") {

                const talleres = cargarSalones("talleres")
                const talleresFiltrados = talleres.filter(taller => taller.id !== id)
                localStorage.setItem("talleres", JSON.stringify(talleresFiltrados))

            }

            desvincularCompus(id)
        }
    }

    actualizarLista()
}

/* La modificación de objetos consiste en obtener todo en una variable copia,
   pasarlo a JSON, filtrar las excepciones y despues
   resubir todo de vuelta
*/

// EVENTOS

formulario.addEventListener("submit", function (e) {
    e.preventDefault()

    const inputIDPC = document.getElementById("idPC")
    const inputSalon = document.getElementById("ubicacion")

    // hace falta añadir verificación de ubicación, pero antes, hay que añadir un sistema de implementación de salones
    // ya que sin los salones dinamicos no se puede hacer nada con el citado de salones o prestado de equipos

    const equipos = {
        id: inputIDPC.value,
        ubicacion: inputSalon.value,
        equipoPrestado: false,
        activo: true,
        incidencias: [],
    }

})

btnAbrirUbicaciones.addEventListener("click", function () {
    barralateralUbicaciones.classList.add("abierto");
})

btnCerrarUbicaciones.addEventListener("click", function () {
    barralateralUbicaciones.classList.remove("abierto");
})

btnAgregarL.addEventListener("click", () => {
    agregarSalon("laboratorios")
})

btnAgregarT.addEventListener("click", () => {
    agregarSalon("talleres")
})

actualizarLista()