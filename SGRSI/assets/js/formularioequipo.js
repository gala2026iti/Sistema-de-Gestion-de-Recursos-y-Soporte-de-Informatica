const btnRegistrarEquipo =
    document.getElementById("btnRegistrarEquipo");

const modalEquipo =
    document.getElementById("modalEquipo");

const btnCancelarEquipo =
    document.getElementById("btnCancelarEquipo");

btnRegistrarEquipo.addEventListener("click", () => {

    modalEquipo.classList.remove("oculto");

});

btnCancelarEquipo.addEventListener("click", () => {

    modalEquipo.classList.add("oculto");

});