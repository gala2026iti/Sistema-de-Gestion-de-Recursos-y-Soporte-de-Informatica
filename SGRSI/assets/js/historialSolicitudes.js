// VARIABLES
const contenedorGeneral = document.getElementById("contenedorHistorial")

// FUNCIONES
const cargarHistorial = () => {
    const historial = localStorage.getItem("registroSolicitudes")
    if (historial === null || historial === undefined || historial === "") return [] //Verificamos que el contenido sea "valido"
    return JSON.parse(historial) //Lo pasamos a JSON
}

const buscarNombre = (cedulaUsuario) => {
    let usuarios = localStorage.getItem("usuarios")
    if (usuarios === null || usuarios === undefined || usuarios === "") usuarios = []
    else usuarios = JSON.parse(usuarios)

    const usuarioEncontrado = usuarios.find(u => u.usuario === cedulaUsuario)
    return usuarioEncontrado ? usuarioEncontrado.nombre : "N/A"
}

const actualizarVentana = () => {

    let listaDiaria = document.createElement("ul")
    listaDiaria.classList = "historial-lista mt-2 mb-3"

    const historialPrestamos = cargarHistorial()
    let fechaGrupo = ""

    historialPrestamos.forEach(e => {
        let fechaGrupoHistorial = e.fecha

        if (!(fechaGrupo === fechaGrupoHistorial)) {
            fechaGrupo = fechaGrupoHistorial

            const indicadorFecha = document.createElement("span")
            indicadorFecha.innerText = `Intervenciones el ${fechaGrupoHistorial}`
            indicadorFecha.classList = "fw-bold"

            contenedorGeneral.appendChild(indicadorFecha)

            listaDiaria = document.createElement("ul")
            listaDiaria.classList = "historial-lista mt-2 mb-3"


            contenedorGeneral.appendChild(indicadorFecha)
            contenedorGeneral.appendChild(listaDiaria)

        }

        const registro = document.createElement("li")
        registro.classList = "historial-contenido d-flex justify-content-between align-items-center"

        const contenedor = document.createElement("div")
        contenedor.classList = "d-flex flex-column"

        const textoAsunto = document.createElement("span")
        textoAsunto.classList = "fw-bold"

        let mensaje
        switch (e.modificacion) {
            case "creacion":
                mensaje = `El docente ${e.usuario} (${buscarNombre(e.usuario)}) Registró una nueva solicitud.\nID: ${e.idSolicitud}, Asunto: "${e.asunto}".`
                break;
            case "finalizacion":
                mensaje = `El usuario ${e.usuario} (${buscarNombre(e.usuario)}) Finalizó una solicitud.\nID: ${e.idSolicitud}, Asunto: "${e.asunto}".`
                break;
        }
        textoAsunto.innerText = mensaje

        const textoMensaje = document.createElement("span")
        textoMensaje.classList = "text.muted"
        textoMensaje.innerText = e.hora

        contenedor.appendChild(textoAsunto)
        contenedor.appendChild(textoMensaje)

        registro.appendChild(contenedor)

        listaDiaria.appendChild(registro)

    })
}

actualizarVentana() 
