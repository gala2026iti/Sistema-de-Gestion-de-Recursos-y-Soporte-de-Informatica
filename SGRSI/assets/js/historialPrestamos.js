// VARIABLES
const contenedorGeneral = document.getElementById("contenedorHistorial")

// FUNCIONES
const cargarHistorial = () => {
    const historial = localStorage.getItem("registroPrestamos") //Obtenemos los prestamos del sistema
    if (historial === null || historial === undefined || historial === "") return [] //Verificamos que el contenido sea "valido"
    return JSON.parse(historial) //Lo pasamos a JSON
}

const actualizarVentana = () => {

    let listaDiaria = document.createElement("ul")
    listaDiaria.classList = "historial-lista mt-2 mb-3"

    const historialPrestamos = cargarHistorial()
        let fechaGrupo = ""

    historialPrestamos.forEach(e => {
        let fechaGrupoHistorial = e.fecha

        if(!(fechaGrupo === fechaGrupoHistorial)){
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

        const detalleCaso = document.createElement("span")
        detalleCaso.classList = "fw-bold"
        detalleCaso.innerText = e.detalleOperador
        
        const tipoCaso = document.createElement("span")
        tipoCaso.classList = "text.muted"
        tipoCaso.innerText = e.descripcionAccion

        contenedor.appendChild(detalleCaso)
        contenedor.appendChild(tipoCaso)

        registro.appendChild(contenedor)

        listaDiaria.appendChild(registro)

    })
}

actualizarVentana() 
