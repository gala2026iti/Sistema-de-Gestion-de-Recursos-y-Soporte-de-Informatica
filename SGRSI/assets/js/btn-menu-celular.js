// VARIABLES
const btnMenu = document.getElementById("btn-menu")
const btnCerrar = document.getElementById("btn-cerrar")
const menu = document.querySelector(".nav-menu")

// EVENTOS
if (btnMenu && menu) {
    btnMenu.addEventListener("click", function () {
        menu.classList.add("activo")
        btnMenu.style.display = "none"
        if (btnCerrar) btnCerrar.style.display = "flex"
    })
}

if (btnCerrar && menu) {
    btnCerrar.addEventListener("click", function () {
        menu.classList.remove("activo")
        btnCerrar.style.display = "none"
        if (btnMenu) btnMenu.style.display = "flex"
    })
}