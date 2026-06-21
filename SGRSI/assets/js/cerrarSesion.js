const btnSalir = document.getElementById("cerrarSesion")

if (btnSalir) {
    btnSalir.addEventListener("click", function(e) {
        e.preventDefault()
        
        const urlActual = window.location.pathname // dato de la url, para ver si contiene alguna carpeta donde estemos
        let rutaSalida = "index.html"

        if (urlActual.includes("administracion-tecnico") || urlActual.includes("direccion")) {
            rutaSalida = "../index.html"
        }

        localStorage.setItem("usuario", "")
        window.location.href = rutaSalida
    })
}

// esto es para limpiar la memoria del usuario que inició sesión antes
// sin esto, la info del usuario queda guardada en la memoria incluso
// despues de cerrar sesión