// VARIABLES
const usuarioIngresado = localStorage.getItem("usuario")
const usuarioIngresadoJSON = JSON.parse(usuarioIngresado)
const opcionesAdmin = document.getElementById("opcionesAdmin")

// FUNCIONES
if(usuarioIngresadoJSON.rol === "tecnico") {
    opcionesAdmin.style.display = "none"
}
