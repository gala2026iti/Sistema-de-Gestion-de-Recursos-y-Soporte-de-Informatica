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

if(usuario.cedula == 12345678 && usuario.clave === "docenteITI"){ // Rol docente
    usuario.rol = "docente"
} else if (usuario.cedula == 23456789 && usuario.clave === "directorITI"){ // Rol director
    usuario.rol = "director"
} else if (usuario.cedula == 34567890 && usuario.clave === "adminITI") { // Rol administrador
    usuario.rol = "admin"
} else if (usuario.cedula == 87654321 && usuario.clave === "asistenteITI"){ // Rol tecnico
    usuario.rol = "asistente"
} else {
    alert("Usuario o Contraseña incorrectos")
}

if(usuario.rol === "docente" || usuario.rol === "director" || usuario.rol === "admin" || usuario.rol === "asistente"){
sessionStorage.setItem("rol", usuario.rol);
window.location.href = "formulario.html"
}
})

