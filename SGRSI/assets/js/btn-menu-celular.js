const btnMenu = document.getElementById("btn-menu");

const btnCerrar = document.getElementById("btn-cerrar");

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
