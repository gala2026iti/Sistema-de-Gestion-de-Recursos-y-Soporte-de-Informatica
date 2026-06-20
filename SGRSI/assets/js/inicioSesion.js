// VARIABLES
const cedula = document.getElementById('cedula')
const clave = document.getElementById('clave')
const form = document.getElementById('formInicio')
const rol = document.getElementById('rol')

let usuarios = localStorage.getItem("usuarios")
if(usuarios === null || usuarios === undefined || usuarios === "") {
    usuarios= []
} else {
    usuarios = JSON.parse(usuarios)
}

// FUNCIONES
const usuarioExistente = (cedula) => {
    let existe = false
    usuarios.forEach(u => {
        if(u.usuario === cedula){
            existe = true
        }
    })
    return existe
}

const obtenerUsuario = (cedula) => {
    const usuarioObtenido = usuarios.find(u => u.usuario === cedula)
    return usuarioObtenido
}

// EVENTOS
form.addEventListener("submit", function(e){

    e.preventDefault()

    const usuarioLocal = {
        cedula: cedula.value.trim(),
        clave: clave.value.trim(),
        rol: rol.value
    }

    sessionStorage.setItem("usuario", JSON.stringify(usuarioLocal));


if(usuarioLocal.cedula == 12345678 && usuarioLocal.clave === "adminITI"){ // Rol administrador debug
// no creen cuentas con esta cedula, no va a funcionar por la prioridad debug
// no retes al sistema, capaz algun dia se revela y acaba con el mundo

    usuarioLocal.rol = "administrador"
    window.location.href = "homeAdmin.html"
}  else { // verificacion de usuario añadido al sistema manualmente por la cuenta de admin arriba o por algun otro admin
    if(usuarioExistente(usuarioLocal.cedula)){
      const usuarioLogueado = obtenerUsuario(usuarioLocal.cedula)

      if(String(usuarioLogueado.clave) === String(usuarioLocal.clave)){ //los pongo en string para evitar bugs de comparacion
        if(usuarioLogueado.activo){
        localStorage.setItem("usuario", JSON.stringify(usuarioLocal))

      if(usuarioLogueado.rol === "administrador"){
        window.location.href = "homeAdmin.html"
      } else if (usuarioLogueado.rol === "docente") {
        window.location.href = "homeDocente.html"
      } else if (usuarioLogueado.rol === "tecnico") {
        window.location.href = "homeAsistente.html"
      } else if (usuarioLogueado.rol === "direccion") {
        window.location.href = "homeDirector.html"
      }
    } else {
        alert("Usuario no disponible")
    }
    } else {
        alert("Usuario o Contraseña incorrectos")
    }

    }
    else {
    alert("Usuario o Contraseña incorrectos")
    }
}
})
