// VARIABLES
const btnMenu = document.getElementById("btnMenu")
const btnCerrar = document.getElementById("btnCerrar")
const menu = document.querySelector(".nav-menu")

// EVENTOS
btnMenu.addEventListener("click", function () {
    menu.classList.add("activo")
    btnMenu.style.display = "none"
    if (btnCerrar) btnCerrar.style.display = "flex"
})


btnMenu.addEventListener("click", function () {
    menu.classList.add("activo")
    btnMenu.style.display = "none"
    btnCerrar.style.display = "flex"
})

btnCerrar.addEventListener("click", function () {
    menu.classList.remove("activo")
    btnCerrar.style.display = "none"
    btnMenu.style.display = "flex"
})

btnCerrar.style.display = "none"

function comprobarPantalla() {
    if (window.innerWidth > 1046) {
        btnMenu.style.display = "none"
        btnCerrar.style.display = "none"
        menu.classList.remove("activo")
    } else {
        btnMenu.style.display = "flex"
    }
}

comprobarPantalla()
window.addEventListener("resize", comprobarPantalla)
