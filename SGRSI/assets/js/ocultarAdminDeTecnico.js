const usuarioIngresado = localStorage.getItem("usuario")
const usuarioIngresadoJSON = JSON.parse(usuarioIngresado)

const opcionesAdmin = document.getElementById("opcionesAdmin")

if(usuarioIngresadoJSON.rol === "tecnico") {
    opcionesAdmin.style.display = "none"
}
