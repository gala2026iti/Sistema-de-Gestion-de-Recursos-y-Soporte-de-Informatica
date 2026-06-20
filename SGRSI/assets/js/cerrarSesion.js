btnSalir = document.getElementById("cerrarSesion")

btnSalir.addEventListener("click", () => {
    sessionStorage.setItem("usuario", "")
})

// Esto es para limpiar la memoria del usuario que inició sesión antes
// Sin esto, la info del usuario queda guardada en la memoria incluso
// Despues de cerrar sesión