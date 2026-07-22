// VARIABLES
let modoEdicion = false
let usuarioEditando = null
let booleanMostrarClave = false

const formulario = document.getElementById("formUsuario")
const cuerpoTabla = document.querySelector("#tablaUsuarios tbody")
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

const obtenerFecha = (dato) => {
    const fechaActual = new Date()
    const formatoFecha = fechaActual.getDate() + "/" + (fechaActual.getMonth() + 1) + "/" + fechaActual.getFullYear()
    const formatoHora = fechaActual.getHours() + ":" + fechaActual.getMinutes()

    if (dato === "fecha") {
        return formatoFecha
    } else {
        return formatoHora
    }
}

const usuarioExistente = (cedula) => {
    const usuarios = cargarUsuarios()
    return usuarios.some(usuario => usuario.usuario === cedula) //El comando some se usa para verificar si existe algun elemento en el array que coincida con la busqueda
}

const limpiarCampos = () => {
    formulario.reset()
}

const actualizarUsuarios = (usuarios) => {
    localStorage.setItem("usuarios", JSON.stringify(usuarios))
    actualizarTabla()
}

const registrarHistorialUsuarios = (ciActor, ciModificado, modificacion) => {
    const datos = localStorage.getItem("registroUsuarios")
    let historial = datos ? JSON.parse(datos) : []

    historial.push({
        id: historial.length + 1,
        fecha: obtenerFecha("fecha"),
        hora: obtenerFecha("hora"),
        ciActor: ciActor,
        ciModificado: ciModificado,
        modificacion: modificacion
    })
    localStorage.setItem("registroUsuarios", JSON.stringify(historial))
}

const modificarUsuario = (usuarioModificado) => {
    const usuarios = cargarUsuarios()

    usuarios.forEach(u => {
        if (u.usuario === usuarioEditando) {
            u.nombre = usuarioModificado.nombre
            u.correo = usuarioModificado.correo
            u.clave = usuarioModificado.clave
            u.rol = usuarioModificado.rol
        }
    })


    actualizarUsuarios(usuarios)
    modoEdicion = false
    usuarioEditando = null

    limpiarCampos()
    modalUsuario.classList.replace("d-flex", "d-none")
}

const verificarRol = (rol) => rol === "tecnico" || rol === "administrador" || rol === "docente" || rol === "direccion"

const validarFormulario = (usuario) => {
    if (!/^\d{8}$/.test(usuario.usuario)) {
     alert("La cédula debe contener exactamente 8 números.")
     return false }

    if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,30}$/.test(usuario.nombre)) {
        alert("El nombre solo puede contener letras y espacios.")
        return false }

    if (usuario.correo.length > 50) {
        alert("El correo supera el máximo de caracteres.")
        return false }

    if (!/^[^\s@]+@[^\s@]+\.com$/.test(usuario.correo)) {
        alert("Ingrese un correo válido terminado en .com")
        return false   }

    if (usuario.clave.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.")
        return false  }

    if (!verificarRol(usuario.rol)) {
        alert("Rol inválido.")
        return false }
return true
}

const desactivarUsuario = (cedula) => {
    const usuarios = cargarUsuarios()
    usuarios.forEach(u => {
        if (u.usuario === cedula) {
            u.activo = false
        }
    })

    actualizarUsuarios(usuarios)

}

const activarUsuario = (cedula) => {
    const usuarios = cargarUsuarios()
    usuarios.forEach(u => {
        if (u.usuario === cedula) {
            u.activo = true
        }
    })
    actualizarUsuarios(usuarios)
}

const guardarUsuario = (usuario) => {
    const usuarios = cargarUsuarios()
    usuarios.push(usuario)
    actualizarUsuarios(usuarios)

    alert("Usuario registrado con exito")
    registrarHistorialUsuarios(usuarioLocalJSON.usuario, usuario.usuario, "creacion")
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
        usuarioFila.innerText = u.usuario

        const nombreFila = document.createElement("td")
        nombreFila.innerText = u.nombre

        const correoFila = document.createElement("td")
        correoFila.innerText = u.correo

        const rolFila = document.createElement("td")
        rolFila.innerText = u.rol

        const estadoFila = document.createElement("td")
        estadoFila.innerText = u.activo ? "Activo" : "De baja"

        const btnModificar = document.createElement("button")
        btnModificar.innerText = "Modificar"
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

        if (String(u.usuario) !== String(usuarioLocalJSON.usuario)) { //Se asegura que el usuario que se quiere eliminar no sea de mismo usuario activo
            if (u.activo) {
                const btnDesactivar = document.createElement("button")
                btnDesactivar.innerText = "Desactivar"
                btnDesactivar.className = "btn btn-danger"
                btnDesactivar.addEventListener("click", () => {
                    if (confirm("¿Estás seguro de que deseas desactivar este usuario?")) {
                        if (confirm("ESTE USUARIO DEJARÁ DE SER ACCESIBLE, ¿QUERÉS CONTINUAR?")) {
                            desactivarUsuario(u.usuario)
                            registrarHistorialUsuarios(usuarioLocalJSON.usuario, u.usuario, "desactivacion")
                        }
                    }
                })
                accionesFila.appendChild(btnDesactivar)
            } else {
                const btnActivar = document.createElement("button")
                btnActivar.innerText = "Activar"
                btnActivar.className = "btn btn-success"
                btnActivar.addEventListener("click", () => {
                    if (confirm("¿Estás seguro de que querés activar nuevamente este usuario?")) {
                        activarUsuario(u.usuario)
                        registrarHistorialUsuarios(usuarioLocalJSON.usuario, u.usuario, "activacion")
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

    if (!validarFormulario(usuario)) {
    return
    }
    
    if (modoEdicion) {
        modificarUsuario(usuario)
        registrarHistorialUsuarios(usuarioLocalJSON.usuario, usuario.usuario, "modificacion")
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

actualizarTabla()
