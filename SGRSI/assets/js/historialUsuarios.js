// VARIABLES
const contenedorGeneral = document.getElementById("contenedorHistorial")

// FUNCIONES
const cargarHistorial = () => {
    const historial = localStorage.getItem("registroUsuarios") //Obtenemos los usuarios del sistema
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

    const historialCambios = cargarHistorial()
    let fechaGrupo = ""

    historialCambios.forEach(e => {
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

        let cambioRealizado
        switch (e.modificacion) {
            case "modificacion":
                cambioRealizado = `El usuario ${e.ciActor} (${buscarNombre(e.ciActor)}) Modifico los datos del usuario ${e.ciModificado} (${buscarNombre(e.ciModificado)}).`
                break;
            case "activacion":
                cambioRealizado = `El usuario ${e.ciActor} (${buscarNombre(e.ciActor)}) Activo la cuenta del usuario ${e.ciModificado} (${buscarNombre(e.ciModificado)}).`
                break;
            case "desactivacion":
                cambioRealizado = `El usuario ${e.ciActor} (${buscarNombre(e.ciActor)}) Desactivo la cuenta del usuario ${e.ciModificado} (${buscarNombre(e.ciModificado)}).`
                break;
            case "creacion":
                cambioRealizado = `El usuario ${e.ciActor} (${buscarNombre(e.ciActor)}) Creó la cuenta del usuario ${e.ciModificado} (${buscarNombre(e.ciModificado)}).`
                break;

        }

        textoAsunto.innerText = cambioRealizado

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
