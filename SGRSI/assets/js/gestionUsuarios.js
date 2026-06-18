/* 
El archivo ingresoSolicitudes.js fue el primer
js creado para el manejo de forms y tablas,
futuros js con el mismo funcionamiento
seran copias adaptadas al caso.

Sin embargo, gestionUsuarios.js implementa funcionamiento de
tablas y de filtrado, además de montón de nuevas implementaciones
que seran usadas como guia a futuros DOM.
*/

// VARIABLES 

let modoEdicion = false
let usuarioEditando = null
let booleanMostrarClave = false

const formulario = document.getElementById("formUsuario")
const tabla = document.getElementById("tablaUsuarios")
const cuerpoTabla = tabla.querySelector("tbody")

const modalUsuario = document.getElementById("modalUsuario");

const registrarUsuario = document.getElementById("btnRegistrarUsuario");
const cancelarUsuario = document.getElementById("btnCancelarUsuario");
const inputUsuario = document.getElementById("usuario")

const btnMostrarClave = document.getElementById("btnMostrarClave")
const inputClave = document.getElementById("clave")

const filtroRol = document.getElementById("filtroRol")
const filtroEstado = document.getElementById("filtroEstado")

const usuarioLocal = localStorage.getItem("usuario")
const usuarioLocalJSON = JSON.parse(usuarioLocal)


// FUNCIONES     



const usuarioExistente = (cedula) => {
    const usuarios = cargarUsuarios()
    for (const usuario of usuarios) {
        if (usuario.usuario === cedula) {
            return true
        }
    }
    return false
}

const modificarUsuario = (usuarioModificado) => {

    const usuarios = cargarUsuarios()

    for (const usuario of usuarios) {

        if (usuario.usuario === usuarioEditando) {

            usuario.nombre = usuarioModificado.nombre
            usuario.correo = usuarioModificado.correo
            usuario.clave = usuarioModificado.clave
            usuario.rol = usuarioModificado.rol

        }

    }

    actualizarUsuarios(usuarios)

    modoEdicion = false
    usuarioEditando = null

    limpiarCampos()
    modalUsuario.classList.add("oculto");


}

const verificarRol = (rol) => rol !== ""

const desactivarUsuario = (cedula) => {
    const usuarios = cargarUsuarios()

    for (const usuario of usuarios) {

        if (usuario.usuario === cedula) {
            usuario.activo = false
        }

    }

    actualizarUsuarios(usuarios)
}

const activarUsuario = (cedula) => {
    const usuarios = cargarUsuarios()

    for (const usuario of usuarios) {

        if (usuario.usuario === cedula) {
            usuario.activo = true
        }

    }

    actualizarUsuarios(usuarios)
}

const actualizarTabla = () => {
    const usuarios = cargarUsuarios()
    cuerpoTabla.innerHTML = ""

    let usuariosFiltrados = usuarios

    if (filtroRol.value !== "") {
        usuariosFiltrados = usuariosFiltrados.filter(u => u.rol === filtroRol.value)
    }

    if (filtroEstado.value !== "") {
        usuariosFiltrados = usuariosFiltrados.filter(u => u.activo === (filtroEstado.value === "activo"))
    }

    for (const u of usuariosFiltrados) {

        const fila = document.createElement("tr")

        const nombreFila = document.createElement("td")
        nombreFila.textContent = u.nombre

        const usuarioFila = document.createElement("td")
        usuarioFila.textContent = u.usuario

        const correoFila = document.createElement("td")
        correoFila.textContent = u.correo

        const rolFila = document.createElement("td")
        rolFila.textContent = u.rol

        const estadoFila = document.createElement("td")
        estadoFila.textContent = u.activo ? "Activo" : "De baja"

        const btnModificar = document.createElement("button")
        btnModificar.textContent = "Modificar"
        btnModificar.className = "btn btn-primary"
        btnModificar.addEventListener("click", () => {

            modoEdicion = true
            usuarioEditando = u.usuario

            inputUsuario.value = u.usuario
            inputUsuario.readOnly = true


            const nombreEditar = document.getElementById("nombre")
            nombreEditar.value = u.nombre

            const correoEditar = document.getElementById("correo")
            correoEditar.value = u.correo

            const claveEditar = document.getElementById("clave")
            claveEditar.value = u.clave

            const rolEditar = document.getElementById("rol")
            rolEditar.value = u.rol

            modalUsuario.classList.remove("oculto")

        })



        const accionesFila = document.createElement("td")
        accionesFila.appendChild(btnModificar)

        if(u.usuario !== usuarioLocalJSON.usuario){
        if (u.activo) {
            const btnDesactivar = document.createElement("button")
            btnDesactivar.textContent = "Desactivar"
            btnDesactivar.className = "btn btn-danger"
            btnDesactivar.addEventListener("click", () => {
                if (confirm("¿Estas seguro de que deseas desactivar este usuario?")) {
                    if (confirm("ESTE USUARIO DEJARÁ DE SER ACCESIBLE, ¿DESEAS CONTINUAR?")) {
                        desactivarUsuario(u.usuario)
                    }
                }
            })
            accionesFila.appendChild(btnDesactivar)

        } else {
            const btnActivar = document.createElement("button")
            btnActivar.textContent = "Activar"
            btnActivar.className = "btn btn-success"
            btnActivar.addEventListener("click", () => {
                if (confirm("¿Estas seguro de que deseas activar nuevamente este usuario?")) {
                    activarUsuario(u.usuario)
                }
            })
            accionesFila.appendChild(btnActivar)
        }
    }

        fila.appendChild(usuarioFila)
        fila.appendChild(nombreFila)
        fila.appendChild(correoFila)
        fila.appendChild(rolFila)
        fila.appendChild(estadoFila)
        fila.appendChild(accionesFila)
        cuerpoTabla.appendChild(fila)

    }

}

const guardarUsuario = (usuario) => {

    const usuarios = (cargarUsuarios());
    usuarios.push(usuario);
    actualizarUsuarios(usuarios);

    alert("Usuario registrado con éxito!")
    modalUsuario.classList.add("oculto");
    limpiarCampos()
    actualizarTabla()


}

const actualizarUsuarios = (usuarios) => {
    localStorage.setItem("usuarios", JSON.stringify(usuarios))
    actualizarTabla()

}

const cargarUsuarios = () => {
    const usuariosLocales = localStorage.getItem("usuarios");
    if (usuariosLocales === null) {
        return []
    }
    return JSON.parse(usuariosLocales);
}

const limpiarCampos = () => {
    formulario.reset()
}

const mostrarClave = () => {
    if(booleanMostrarClave) {
        inputClave.type = "text"
                btnMostrarClave.innerText = "Ocultar Contraseña"
    } else {
        inputClave.type = "password"
        btnMostrarClave.innerText = "Mostrar Contraseña"
    }  booleanMostrarClave = !booleanMostrarClave

}

// EVENTOS

formulario.addEventListener("submit", function (e) {
    e.preventDefault()

    const inputNombre = document.getElementById("nombre")
    const inputUsuario = document.getElementById("usuario")
    const inputCorreo = document.getElementById("correo")
    const inputClave = document.getElementById("clave")
    const inputRol = document.getElementById("rol")

    const usuario = {
        usuario: inputUsuario.value,
        nombre: inputNombre.value,
        correo: inputCorreo.value,
        clave: inputClave.value,
        rol: inputRol.value,
        activo: true
    }
    if (modoEdicion) {

        modificarUsuario(usuario)


    } else {

        if (usuarioExistente(usuario.usuario)) {
            alert("Error: El usuario ya existe")
        } else {
            if (verificarRol(usuario.rol)) {
                guardarUsuario(usuario)
            } else {
                alert("Error: Rol inválido")
            }
        }

    }
})

registrarUsuario.addEventListener("click", () => {
    modalUsuario.classList.remove("oculto")
    inputUsuario.readOnly = false

})

cancelarUsuario.addEventListener("click", () => {
    document.querySelector("#modalUsuario form").reset();
    modalUsuario.classList.add("oculto");
});

cancelarUsuario.addEventListener("click", () => {

    modoEdicion = false
    usuarioEditando = null

    document.getElementById("usuario").readOnly = false

    formulario.reset()
    modalUsuario.classList.add("oculto")

})

btnMostrarClave.addEventListener("click", mostrarClave)

filtroRol.addEventListener("change", actualizarTabla)

filtroEstado.addEventListener("change", actualizarTabla)

actualizarTabla()

