const cedula = document.getElementById('cedula')
const clave = document.getElementById('clave')
const form = document.getElementById('formInicio')
const rol = document.getElementById('rol')

form.addEventListener("submit", function(e){

    e.preventDefault()

    const usuario = {
        cedula: cedula.value,
        clave: clave.value,
        rol: rol.value
    }

    sessionStorage.setItem("usuario", JSON.stringify(usuario));


if(usuario.cedula == 12345678 && usuario.clave === "docenteITI"){ // Rol docente
    usuario.rol = "docente"
    window.location.href = "homeDocente.html"

} else if (usuario.cedula == 23456789 && usuario.clave === "directorITI"){ // Rol director
    usuario.rol = "director"
    window.location.href = "homeDirector.html"

} else if (usuario.cedula == 34567890 && usuario.clave === "adminITI") { // Rol administrador
    usuario.rol = "admin"
    window.location.href = "homeAdmin.html"

} else if (usuario.cedula == 87654321 && usuario.clave === "asistenteITI"){ // Rol tecnico
    usuario.rol = "asistente"
    window.location.href = "homeAsistente.html"

} else {
    alert("Usuario o Contraseña incorrectos")

}
})
