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

const menu = document.querySelector(".nav-menu");

btnMenu.addEventListener("click", function () {

    menu.classList.add("activo");

    btnMenu.style.display = "none";

    btnCerrar.style.display = "flex";
});

btnCerrar.addEventListener("click", function () {

    menu.classList.remove("activo");

    btnCerrar.style.display = "none";

    btnMenu.style.display = "flex";
});

btnCerrar.style.display ="none";



function comprobarPantalla() {
    if (window.innerWidth > 1046) {
        btnMenu.style.display = "none";
        btnCerrar.style.display = "none";
        menu.classList.remove("activo");
    } else {
        if (btnCerrar.style.display == "none"){
            btnMenu.style.display = "flex";
        }
    }
}

comprobarPantalla();
window.addEventListener("resize", comprobarPantalla);
