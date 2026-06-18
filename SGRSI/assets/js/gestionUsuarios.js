// VARIABLES 

// los modal diseñados en js separados seran insertados en un solo js
// que contemple esa pagina

// la estructura se tomo como referencia lo dicho por López, variables, funciones y al final eventos
// pero mas al final deje lo que se debe ejecutar al cargar la pagina, como el cargar la tabla

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

// sin la verificación de usuario en las tablas posteriores tira error
// por eso no se debe saltear el inicio de sesion
// la verificacion es para en la tabla de usuarios no puedas auto borrarte

// version vieja, si no hay usuario no debe dejar pasar pero lo hace

// const usuarioLocal = localStorage.getItem("usuario") 
// if (usuarioLocal === null || usuarioLocal === undefined) {
//     const usuarioLocalJSON = { usuario: "" }
// } else {
//     const usuarioLocalJSON = JSON.parse(usuarioLocal)
// }

// version nueva, va a ser incluida en un nuevo js en cada html

// const usuarioLocal = localStorage.getItem("usuario")
// if (usuarioLocal === null || usuarioLocal === undefined || usuarioLocal === "") {
//     const usuarioLocalJSON = { usuario: "" }
//     alert("Error: No hay usuario logueado")
//     window.location.href = "index.html"
// } else {
//     const usuarioLocalJSON = JSON.parse(usuarioLocal)
// }

// FUNCIONES     

const usuarioExistente = (cedula) => {
    const usuarios = cargarUsuarios() 
    return usuarios.some(usuario => usuario.usuario === cedula) 
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
    modalUsuario.classList.add("oculto") 
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

            modalUsuario.classList.remove("oculto") 
        }) 

        const accionesFila = document.createElement("td") 
        accionesFila.appendChild(btnModificar) 

        if (u.usuario !== usuarioLocalJSON.usuario) {
            if (u.activo) {
                const btnDesactivar = document.createElement("button") 
                btnDesactivar.textContent = "Desactivar" 
                btnDesactivar.className = "btn btn-danger" 
                btnDesactivar.addEventListener("click", () => {
                    if (confirm("¿Estás seguro de que deseas desactivar este usuario?")) {
                        if (confirm("ESTE USUARIO DEJARÁ DE SER ACCESIBLE, ¿QUERÉS CONTINUAR?")) { // no quiero quejas sobre el español
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

const guardarUsuario = (usuario) => {
    const usuarios = cargarUsuarios() 
    usuarios.push(usuario) 
    actualizarUsuarios(usuarios) 

    alert("Usuario registrado con exito!") 
    modalUsuario.classList.add("oculto") // ni idea de q hace esto, lo robe del js que daba funcionalidad a la ventana desplegable
    limpiarCampos() 
} 

const actualizarUsuarios = (usuarios) => {
    localStorage.setItem("usuarios", JSON.stringify(usuarios)) 
    actualizarTabla() 
} 

const cargarUsuarios = () => {
    const usuariosLocales = localStorage.getItem("usuarios") 
    if(usuariosLocales === null){
        return []
    }
    else {
        return JSON.parse(usuariosLocales)
    }
} 

const limpiarCampos = () => {
    formulario.reset() 
} 

const mostrarClave = () => {
    if (booleanMostrarClave) {
        inputClave.type = "text" 
        btnMostrarClave.innerText = "Ocultar Contraseña" 
    } else {
        inputClave.type = "password" 
        btnMostrarClave.innerText = "Mostrar Contraseña" 
    }
    booleanMostrarClave = !booleanMostrarClave 
} 

// EVENTOS

formulario.addEventListener("submit", function (e) {
    e.preventDefault() 

    const inputNombre = document.getElementById("nombre") 
    const inputCorreo = document.getElementById("correo") 
    const inputRol = document.getElementById("rol") 

    const usuario = {
        usuario: inputUsuario.value,
        nombre: inputNombre.value,
        correo: inputCorreo.value,
        clave: inputClave.value,
        rol: inputRol.value,
        activo: true
    } 

    if (modoEdicion) {  //la condición del js de López, donde se usaba bandera en la tabla para editar en la misma
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
    modoEdicion = false 
    usuarioEditando = null 
    limpiarCampos() 
    inputUsuario.readOnly = false 
    modalUsuario.classList.remove("oculto") 
}) 

cancelarUsuario.addEventListener("click", () => {
    modoEdicion = false 
    usuarioEditando = null 
    inputUsuario.readOnly = false 
    limpiarCampos() 
    modalUsuario.classList.add("oculto") 
}) 

btnMostrarClave.addEventListener("click", mostrarClave) 
filtroRol.addEventListener("change", actualizarTabla) 
filtroEstado.addEventListener("change", actualizarTabla) 

actualizarTabla()
