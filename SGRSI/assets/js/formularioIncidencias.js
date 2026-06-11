document.querySelectorAll(".pc").forEach(pc => {

    const radioOk = pc.querySelector('input[value="ok"]');
    const radioError = pc.querySelector('input[value="incidencia"]');
    const incidencia = pc.querySelector(".incidencia");

    radioOk.addEventListener("change", () => {
        incidencia.style.display = "none";
    });

    radioError.addEventListener("change", () => {
        incidencia.style.display = "flex";
    });

});