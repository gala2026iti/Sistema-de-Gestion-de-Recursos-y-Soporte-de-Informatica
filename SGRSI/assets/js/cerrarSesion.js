btnSalir = document.getElementById("cerrarSesion")

btnSalir.addEventListener("click", () => {
    sessionStorage.setItem("usuario", "")
})

// esto es para limpiar la memoria del usuario que inició sesión antes
// sin esto, la info del usuario queda guardada en la memoria incluso
// despues de cerrar sesión