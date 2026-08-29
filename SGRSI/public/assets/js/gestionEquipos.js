// VARIABLES
let modoEdicion = false
let equipoEditando = null
let filtroUbicacionActual = "todos"

const btnAbrirUbicaciones = document.getElementById("btnAbrirUbicaciones")
const btnCerrarUbicaciones = document.getElementById("btnCerrarUbicaciones")
const barralateralUbicaciones = document.getElementById("barraLateralSalones")

const btnAgregarL = document.getElementById("btnAgregarL")
const btnAgregarT = document.getElementById("btnAgregarT")


const modalEquipo = document.getElementById("modalEquipo")
const btnAbrirModalRegistrar = document.getElementById("btnRegistrarEquipo")
const btnGuardarEquipo = document.getElementById("btnGuardarEquipo")
const btnCancelarEquipo = document.getElementById("btnCancelarEquipo")

if (btnAbrirModalRegistrar) {
    btnAbrirModalRegistrar.addEventListener("click", () => {
        modoEdicion = false
        equipoEditando = null
        if (btnGuardarEquipo) btnGuardarEquipo.innerText = "Guardar PC"
        if (modalEquipo) modalEquipo.classList.replace("d-none", "d-flex")
    })
}

if (btnCancelarEquipo) {
    btnCancelarEquipo.addEventListener("click", () => {
        modoEdicion = false
        equipoEditando = null
        if (modalEquipo) modalEquipo.classList.replace("d-flex", "d-none")
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


