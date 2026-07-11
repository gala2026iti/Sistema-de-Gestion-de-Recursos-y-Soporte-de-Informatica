//VARIABLES
const btnSalir = document.getElementById("cerrarSesion")

// EVENTOS
btnSalir.addEventListener("click", function (e) {
    e.preventDefault()

    const urlActual = window.location.pathname
    let rutaSalida = "index.html"

    if (urlActual.includes("administracion-tecnico") || urlActual.includes("direccion")) {

            rutaSalida = "../index.html"
        }

        localStorage.setItem("usuario", "")
        window.location.href = rutaSalida
    })

