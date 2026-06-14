const btnRegistrarUsuario =
    document.getElementById("btnRegistrarUsuario");

const modalUsuario =
    document.getElementById("modalUsuario");

btnRegistrarUsuario.addEventListener("click", () => {

    modalUsuario.classList.remove("oculto");

});
const btnCancelarUsuario =
    document.getElementById("btnCancelarUsuario");

btnCancelarUsuario.addEventListener("click", () => {

    document.querySelector("#modalUsuario form").reset();

    modalUsuario.classList.add("oculto");

});