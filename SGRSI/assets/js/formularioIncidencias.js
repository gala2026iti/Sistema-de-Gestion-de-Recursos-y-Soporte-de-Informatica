// VARIABLES
const computadoras = document.querySelectorAll(".pc")

// FUNCIONES
const inicializarComportamientoPC = (pc) => {
    const radioOk = pc.querySelector('input[value="ok"]')
    const radioError = pc.querySelector('input[value="incidencia"]')
    const incidencia = pc.querySelector(".incidencia")

    if (!radioOk || !radioError || !incidencia) return

    radioOk.addEventListener("change", () => {
        incidencia.style.display = "none"
    })

    radioError.addEventListener("change", () => {
        incidencia.style.display = "flex"
    })
}

// EVENTOS
computadoras.forEach(pc => inicializarComportamientoPC(pc))