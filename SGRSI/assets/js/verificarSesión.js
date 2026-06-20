const usuarioLocal = localStorage.getItem("usuario")

if (usuarioLocal === null || usuarioLocal === undefined || usuarioLocal === "") {
    const usuarioLocalJSON = { usuario: "" }
    alert("Error: No hay usuario logueado")
    window.location.href = "index.html"
} else {
    const usuarioLocalJSON = JSON.parse(usuarioLocal)
}