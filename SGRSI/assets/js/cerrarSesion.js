const btnSalir = document.getElementById("cerrarSesion")

btnSalir.addEventListener("click", function(e) {
    e.preventDefault()
    localStorage.setItem("usuario", "")

    window.location.href = "index.html"
})

// Esto es para limpiar la memoria del usuario que inició sesión antes
// Sin esto, la info del usuario queda guardada en la memoria incluso
// Despues de cerrar sesión