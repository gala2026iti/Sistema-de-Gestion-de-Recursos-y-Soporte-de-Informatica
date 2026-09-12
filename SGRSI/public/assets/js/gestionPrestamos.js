const formulario = document.getElementById("formPrestamo")
const btnRegistrarPrestamo = document.getElementById("btnRegistrarPrestamo")
const modalPrestamo = document.getElementById("modalPrestamo")
const btnCancelarPrestamo = document.getElementById("btnCancelarPrestamo")
const opciones = document.getElementById("listaDispositivos")
const cuerpoTabla = document.querySelector("#tablaPrestamos tbody")


//EVENTOS
if (btnRegistrarPrestamo) {
    btnRegistrarPrestamo.addEventListener("click", () => {
        modalPrestamo.classList.replace("d-none", "d-flex")
    })
}

if (btnCancelarPrestamo) {
    btnCancelarPrestamo.addEventListener("click", () => {
        formulario.reset()
        modalPrestamo.classList.replace("d-flex", "d-none")
    })
}


