const formulario =
    document.getElementById("formPrestamo");

const btnRegistrarPrestamo =
    document.getElementById("btnRegistrarPrestamo");

const modalPrestamo =
    document.getElementById("modalPrestamo");

const btnCancelarPrestamo =
    document.getElementById("btnCancelarPrestamo");

btnCancelarPrestamo.addEventListener("click", () => {

    formulario.reset();

    modalPrestamo.classList.add("oculto");

});

btnRegistrarPrestamo.addEventListener("click", () => {

    modalPrestamo.classList.remove("oculto");

});