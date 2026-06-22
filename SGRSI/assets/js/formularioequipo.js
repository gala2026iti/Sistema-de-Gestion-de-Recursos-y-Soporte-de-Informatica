// VARIABLES
const btnRegistrarEquipo = document.getElementById("btnRegistrarEquipo")
const modalEquipo = document.getElementById("modalEquipo")
const btnCancelarEquipo = document.getElementById("btnCancelarEquipo")

// EVENTOS
if (btnRegistrarEquipo && modalEquipo) {
    btnRegistrarEquipo.addEventListener("click", () => {
        modalEquipo.classList.remove("oculto")
    })
}

if (btnCancelarEquipo && modalEquipo) {
    btnCancelarEquipo.addEventListener("click", () => {
        modalEquipo.classList.add("oculto")
    })
}