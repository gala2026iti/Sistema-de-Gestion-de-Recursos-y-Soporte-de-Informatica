document.addEventListener("DOMContentLoaded", function () {
    const barralateral = document.getElementById("barralateral-ubicaciones");
    const btnAbrir = document.getElementById("btn-abrir-ubicaciones");
    const btnCerrar = document.getElementById("btn-cerrar-ubicaciones");

    btnAbrir.addEventListener("click", function () {
        barralateral.classList.add("abierto");
    });

    btnCerrar.addEventListener("click", function () {
        barralateral.classList.remove("abierto");
    });
});