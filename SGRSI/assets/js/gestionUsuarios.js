// VARIABLES
let modoEdicion = false
let usuarioEditando = null
let booleanMostrarClave = false

const formulario = document.getElementById("formUsuario")
const tabla = document.getElementById("tablaUsuarios")
const cuerpoTabla = tabla.querySelector("tbody")
const modalUsuario = document.getElementById("modalUsuario")

const registrarUsuario = document.getElementById("btnRegistrarUsuario")
const cancelarUsuario = document.getElementById("btnCancelarUsuario")
const inputUsuario = document.getElementById("usuario")

const btnMostrarClave = document.getElementById("btnMostrarClave")
const inputClave = document.getElementById("clave")

const filtroRol = document.getElementById("filtroRol")
const filtroEstado = document.getElementById("filtroEstado") 

const usuarioLocal = localStorage.getItem("usuario")
const usuarioLocalJSON = JSON.parse(usuarioLocal)

// FUNCIONES     
const cargarUsuarios = () => {
    const usuariosLocales = localStorage.getItem("usuarios") 
    if (usuariosLocales === null || usuariosLocales === "" || usuariosLocales === undefined) {
        return []
    } else {
        return JSON.parse(usuariosLocales)
    }
} 

const usuarioExistente = (cedula) => {
    const usuarios = cargarUsuarios() 
    return usuarios.some(usuario => usuario.usuario === cedula) 
} 

const limpiarCampos = () => {
    formulario.reset() 
} 

const actualizarUsuarios = (usuarios) => {
    localStorage.setItem("usuarios", JSON.stringify(usuarios)) 
    actualizarTabla() 
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
    modalUsuario.classList.replace("d-flex", "d-none") 
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

const guardarUsuario = (usuario) => {
    const usuarios = cargarUsuarios() 
    usuarios.push(usuario) 
    actualizarUsuarios(usuarios) 

    alert("Usuario registrado con exito") 
    modalUsuario.classList.replace("d-flex", "d-none") 
    limpiarCampos() 
} 

const mostrarClave = () => {
    booleanMostrarClave = !booleanMostrarClave 
    if (booleanMostrarClave) {
        inputClave.type = "text" 
        btnMostrarClave.innerText = "Ocultar Contraseña" 
    } else {
        inputClave.type = "password" 
        btnMostrarClave.innerText = "Mostrar Contraseña" 
    }
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

    usuariosFiltrados.forEach(u => {
        const fila = document.createElement("tr") 

        const usuarioFila = document.createElement("td") 
        usuarioFila.textContent = u.usuario 

        const nombreFila = document.createElement("td") 
        nombreFila.textContent = u.nombre 

        const correoFila = document.createElement("td") 
        correoFila.textContent = u.correo 

        const rolFila = document.createElement("td") 
        rolFila.textContent = u.rol 

        const estadoFila = document.createElement("td") 
        estadoFila.textContent = u.activo ? "Activo" : "De baja" 

        const btnModificar = document.createElement("button") 
        btnModificar.textContent = "Modificar" 
        btnModificar.className = "btn btn-primary me-2" 
        btnModificar.addEventListener("click", () => {
            modoEdicion = true 
            usuarioEditando = u.usuario 

            inputUsuario.value = u.usuario 
            inputUsuario.readOnly = true 

            document.getElementById("nombre").value = u.nombre 
            document.getElementById("correo").value = u.correo 
            document.getElementById("clave").value = u.clave 
            document.getElementById("rol").value = u.rol 

            modalUsuario.classList.replace("d-none", "d-flex") 
        }) 

        const accionesFila = document.createElement("td") 
        accionesFila.appendChild(btnModificar) 

        if (u.usuario !== usuarioLocalJSON.cedula) {
            if (u.activo) {
                const btnDesactivar = document.createElement("button") 
                btnDesactivar.textContent = "Desactivar" 
                btnDesactivar.className = "btn btn-danger" 
                btnDesactivar.addEventListener("click", () => {
                    if (confirm("¿Estás seguro de que deseas desactivar este usuario?")) {
                        if (confirm("ESTE USUARIO DEJARÁ DE SER ACCESIBLE, ¿QUERÉS CONTINUAR?")) {
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
                    if (confirm("¿Estás seguro de que querés activar nuevamente este usuario?")) {
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
    }) 
} 

actualizarTabla()

// EVENTOS
formulario.addEventListener("submit", function (e) {
    e.preventDefault() 

    const inputNombre = document.getElementById("nombre") 
    const inputCorreo = document.getElementById("correo") 
    const inputRol = document.getElementById("rol") 

    const usuario = {
        usuario: inputUsuario.value.trim(),
        nombre: inputNombre.value.trim(),
        correo: inputCorreo.value.trim(),
        clave: inputClave.value.trim(),
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
                alert("Error: Rol invalido") 
            }
        }
    }
}) 

registrarUsuario.addEventListener("click", () => {
    modoEdicion = false 
    usuarioEditando = null 
    limpiarCampos() 
    inputUsuario.readOnly = false 
    modalUsuario.classList.replace("d-none", "d-flex") 
}) 

cancelarUsuario.addEventListener("click", () => {
    modoEdicion = false 
    usuarioEditando = null 
    inputUsuario.readOnly = false 
    limpiarCampos() 
    modalUsuario.classList.replace("d-flex", "d-none") 
}) 

btnMostrarClave.addEventListener("click", mostrarClave) 
filtroRol.addEventListener("change", actualizarTabla) 
filtroEstado.addEventListener("change", actualizarTabla)