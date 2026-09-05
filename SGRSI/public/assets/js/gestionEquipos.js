// VARIABLES
let modoEdicion = false
let equipoEditando = null
let filtroUbicacionActual = "todos"

const btnAbrirUbicaciones = document.getElementById("btnAbrirUbicaciones")
const btnCerrarUbicaciones = document.getElementById("btnCerrarUbicaciones")
const barralateralUbicaciones = document.getElementById("barraLateralSalones")

const tituloFormulario = document.getElementById("tituloForm")

const btnAgregarL = document.getElementById("btnAgregarL")
const btnAgregarT = document.getElementById("btnAgregarT")

const modalEquipo = document.getElementById("modalEquipo")
const btnAbrirModalRegistrar = document.getElementById("btnRegistrarEquipo")

const btnGuardarEquipo = document.getElementById("btnGuardarEquipo")
const btnCancelarEquipo = document.getElementById("btnCancelarEquipo")
const btnModificarEquipo = document.getElementById("btnModificarEquipo")
const btnCancelarModificarEquipo = document.getElementById("btnCancelarModificarEquipo")

const contenedorLugar = document.getElementById("contenedorLugar")

const opcionSeleccionada = document.getElementById("ubicacion")

const botonesMover = document.querySelectorAll(".btnMoverEquipo")

const formEquipo = document.getElementById("formEquipo")

const campoIdPc = document.getElementById("idPC")
const campoUbicacion = document.getElementById("ubicacion")
const campoPosicion = document.getElementById("posicionPC")

if (btnAbrirModalRegistrar) {
    btnAbrirModalRegistrar.addEventListener("click", () => {

        if (btnGuardarEquipo) {
            btnGuardarEquipo.innerText = "Guardar PC"
        }

        if (modalEquipo) {
            modalEquipo.classList.remove("d-none")
            modalEquipo.classList.add("d-flex")
            campoIdPc.readOnly = false

            tituloFormulario.innerText = "Registro de PC"
            btnGuardarEquipo.innerText = "Guardar PC"
            formEquipo.action = "../../../app/controlador/recursos/procesarAltaEquipo.php";

        }
    })
}

botonesMover.forEach( boton => {
    boton.addEventListener("click", () => {

        if (modalEquipo) {
            modalEquipo.classList.remove("d-none")
            modalEquipo.classList.add("d-flex")

            tituloFormulario.innerText = "Mover PC"
            btnGuardarEquipo.innerText = "Guardar Cambios"

            inputTipoUbicacionOrigen = document.createElement("input")
            inputTipoUbicacionOrigen.type = "hidden"
            inputTipoUbicacionOrigen.name = "tipoUbicacionOrigen"
            inputTipoUbicacionOrigen.value = boton.dataset.tipoubicacion
            inputTipoUbicacionOrigen.id = "tipoUbicacionOrigen"

            if(!document.getElementById("tipoUbicacionOrigen")) formEquipo.appendChild(inputTipoUbicacionOrigen)

            formEquipo.action = "../../../app/controlador/recursos/procesarModificarEquipo.php";

            campoIdPc.value = boton.dataset.idequipo

            campoIdPc.readOnly = true
            if((boton.dataset.tipoubicacion == "laboratorio" || boton.dataset.tipoubicacion == "taller") && Number(boton.dataset.idubicacion) > 0){
            campoUbicacion.value = boton.dataset.tipoubicacion + " " + boton.dataset.idubicacion
            campoPosicion.value = boton.dataset.posicion  
            } else if (boton.dataset.tipoubicacion === "prestamo") {
            campoUbicacion.value = "prestamo"
            } else {
            campoUbicacion.value = "ninguna"
            }

        const resultado = opcionSeleccionada.value
        const tipoUbicacion = resultado.split(" ")[0]

        if(tipoUbicacion === "laboratorio") {
            if (contenedorLugar) contenedorLugar.classList.replace("d-none", "d-flex") //Como la compu no tiene salon, NO se oculta la opcion para elegir espacio del salon
        } else if(tipoUbicacion === "taller") {
            if (contenedorLugar) contenedorLugar.classList.replace("d-none", "d-flex") //Como la compu no tiene salon, NO se oculta la opcion para elegir espacio del salon
        } else {
            if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none") //Como la compu no tiene salon, se oculta la opcion para elegir espacio del salon
        }

        }
    })
})

if (btnCancelarEquipo) {
    btnCancelarEquipo.addEventListener("click", () => {
        formEquipo.reset();
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

if(opcionSeleccionada) {
    opcionSeleccionada.addEventListener("change", () => {
        const resultado = opcionSeleccionada.value
        const tipoUbicacion = resultado.split(" ")[0]

        if(tipoUbicacion === "laboratorio") {
            if (contenedorLugar) contenedorLugar.classList.replace("d-none", "d-flex") //Como la compu no tiene salon, NO se oculta la opcion para elegir espacio del salon
        } else if(tipoUbicacion === "taller") {
            if (contenedorLugar) contenedorLugar.classList.replace("d-none", "d-flex") //Como la compu no tiene salon, NO se oculta la opcion para elegir espacio del salon
        } else {
            if (contenedorLugar) contenedorLugar.classList.replace("d-flex", "d-none") //Como la compu no tiene salon, se oculta la opcion para elegir espacio del salon
        }
    })
}


