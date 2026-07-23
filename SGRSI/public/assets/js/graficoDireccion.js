// VARIABLES
let instanciaGraficaEstados = ""
let instanciaGraficaIncidencias = ""

const filtroUbicacion = document.getElementById("filtroUbicacion")
const filtroEstado = document.getElementById("filtroEstado")
const filtroIncidencias = document.getElementById("filtroIncidencias")
const filtroIntervencion = document.getElementById("filtroIntervencion")


const cuerpoTabla = document.querySelector("#tablaEquipos tbody") //El # es para buscar por ID, para posteriormente agarrar su tbody hijo

// FUNCIONES
const cargarSalones = () => {
    const salones = localStorage.getItem("salones")
    if (salones === null || salones === undefined || salones === "") {
        return null
    }
    return JSON.parse(salones)
}

const obtenerEquipos = () => {
    const equipos = localStorage.getItem("equipos")
    if (equipos === null || equipos === undefined || equipos === "") {
        return null
    }
    return JSON.parse(equipos)
}

const calcularIncidencias = (idEquipo) => {
    const equipos = localStorage.getItem("tickets")
    if (!equipos) return 0
    const lista = JSON.parse(equipos)
    const buscado = idEquipo

    const incidenciasFiltradas = lista.filter(t => {
        const idTicket = t.equipoId
        return idTicket === buscado //El return es para decirle al filtro que filtre lo que dió verdadero
    })

    return incidenciasFiltradas.length
}

const encontrarUbicacion = (idEquipo) => {
    const salones = cargarSalones()
    const buscado = idEquipo

    for (let s of salones) {
        if (!(s.espacios === null || s.espacios === undefined || s.espacios === "")) { // Se verifica si el espacio existe y si esta vacio
            for (let j = 0; j < s.espacios.length; j++) { // Se recorren los espacios dentro del salon
                const equipo = s.espacios[j]

                if (equipo.id === buscado) {
                    if (s.tipo === "laboratorio") return { nombre: "Laboratorio " + s.id, posicion: j + 1, modo: "ubicacionSalon" }
                    if (s.tipo === "talleres") return { nombre: "Taller " + s.id, posicion: j + 1, modo: "ubicacionSalon" }
                    if (s.tipo === "prestamo") return { nombre: "prestamo", posicion: 0, modo: "prestamo", prestado: s.prestado }
                }
            }
        }
    }
    return { nombre: "ninguna", posicion: 0, modo: "ninguna" }
}

const poblarFiltroUbicaciones = () => {

    const salones = cargarSalones()
    salones.forEach(s => {
        if (s.tipo === "laboratorio" || s.tipo === "talleres") { //Verifica que el tipo de salon es
            const opcion = document.createElement("option")
            const tipoSalon = (s.tipo === "laboratorio" ? "Laboratorio " : "Taller ") + s.id //Añade su nombre correspondiente
            opcion.value = tipoSalon
            opcion.innerText = tipoSalon
            filtroUbicacion.appendChild(opcion)
        }
    })
}

const convertirFechaAEntero = (fechaTexto) => {
    const p = fechaTexto.split("/")
    if (p.length !== 3) return 0
    return parseInt(p[2] + p[1].padStart(2, "0") + p[0].padStart(2, "0")) //Se pasa la fecha a un numero tipo AAAAMMDD para poder ordenarlo
}

const renderizarGraficas = (activos, inactivos, columnas, dataB) => {
    if (instanciaGraficaEstados) instanciaGraficaEstados.destroy() //Se debe verificar si la grafica existe, sino, tira error por no saber si existe, y si no existe... ¿sabes que pasa?... tira error :)
    const graficaEstados = document.getElementById("graficaEstados").getContext("2d") //El ultimo metodo es para habilitar el dibujo sobre la grafica
    instanciaGraficaEstados = new Chart(graficaEstados, { //Se crea un objeto con los parametros que requiere la grafica para funcionar
        type: 'bar',
        data: {
            labels: ['Operativos', 'Inactivos'],
            datasets: [{
                label: 'Cantidad de Equipos',
                data: [activos, inactivos],
                backgroundColor: ['#5fe0a440', '#f4576740'], //Ese hex ocupa rgba (a = opacidad)
                borderColor: ['#5fe0a4', '#f45767'], //Estos no tienen opacidad porque no pinta la vdd
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    })

    if (instanciaGraficaIncidencias) instanciaGraficaIncidencias.destroy()
    const graficaIncidencias = document.getElementById("graficaIncidencias").getContext("2d")
    instanciaGraficaIncidencias = new Chart(graficaIncidencias, {
        type: 'bar',
        data: {
            labels: columnas.length > 0 ? columnas : ["Sin Equipos"], //Si no hay equipos en el grupo seleccionado, muestra el mensaje
            datasets: [{
                label: 'Numero de Fallas',
                data: dataB.length > 0 ? dataB : [0], //Si no hay datos para utilizar, usa un array vacio
                backgroundColor: '#9cc4ff40',
                borderColor: '#6ea8ff',
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    })
}

const procesarYRenderizar = () => {
    cuerpoTabla.innerHTML = ""

    const listaEquipos = obtenerEquipos()
    let equiposFiltrados = []
    const seleccion = filtroUbicacion.value

    listaEquipos.forEach(eq => {
        const equipoID = eq.id
        const ubicacionPC = encontrarUbicacion(equipoID)
        if (seleccion === "todos" || ubicacionPC.nombre === seleccion) {
            equiposFiltrados.push(eq)
        }
    })

    if (filtroEstado.value !== "") {
        const activoBuscado = filtroEstado.value === "activo"
        equiposFiltrados = equiposFiltrados.filter(eq => {
            const esActivo = eq.activo
            return esActivo === activoBuscado
        })
    }

    if (filtroIncidencias.value !== "") {
        equiposFiltrados.sort((a, b) => {
            const incA = calcularIncidencias(a.id)
            const incB = calcularIncidencias(b.id)
            return filtroIncidencias.value === "menor" ? incA - incB : incB - incA
        })
    }

    if (filtroIntervencion.value !== "") { //Prevencion ante modificaciones en el html, esperemos sea util
        equiposFiltrados.sort((a, b) => {
            const fA = convertirFechaAEntero(a.ultimaIntervencion || a.fecha)
            const fB = convertirFechaAEntero(b.ultimaIntervencion || b.fecha)
            return filtroIntervencion.value === "reciente" ? fB - fA : fA - fB //Dependiendo del valor del filtro, compara el valor A con el valor B, ya que, si el resultado da positivo o negativo, el orden se invierte
        })
    }

    let activos = 0
    let inactivos = 0
    const columnas = []
    const datos = []

    equiposFiltrados.forEach(eq => {
        const equipoID = eq.id
        const ubicacionPC = encontrarUbicacion(equipoID)
        const incidencias = calcularIncidencias(equipoID)
        const esActivo = eq.activo

        esActivo ? activos++ : inactivos++ //Dependiendo del booleano, aumenta el valor de uno o del otro
        columnas.push("PC-" + equipoID)
        datos.push(incidencias)

        const tr = document.createElement("tr")

        const tdCodigo = document.createElement("td")
        tdCodigo.innerText = equipoID

        const tdEstado = document.createElement("td")
        tdEstado.innerText = esActivo ? "Activo" : "Inactivo"

        const tdTipoAsignacion = document.createElement("td")
        let textoAsignacion = "Sin asignar"
        if (ubicacionPC.modo === "prestamo") {
            textoAsignacion = ubicacionPC.prestado ? "Prestado" : "Disponible"
        } else if (ubicacionPC.modo === "ubicacionSalon") {
            textoAsignacion = "En salón"
        }
        tdTipoAsignacion.innerText = textoAsignacion //Para no modificar tantas veces el innerText, se asigna una vez que el valor final este decidido

        const tdDetalleUbicacion = document.createElement("td")
        const textoDetalle = ubicacionPC.modo === "ubicacionSalon" ? `${ubicacionPC.nombre} - Posición ${ubicacionPC.posicion}` : "N/A" //Si el equipo no esta asignado, no tiene posicion, por lo que es N/A
        tdDetalleUbicacion.innerText = textoDetalle

        const tdFecha = document.createElement("td")
        tdFecha.innerText = eq.ultimaIntervencion || "Sin registros"

        const tdIncidencias = document.createElement("td")
        tdIncidencias.innerText = incidencias

        tr.appendChild(tdCodigo)
        tr.appendChild(tdEstado)
        tr.appendChild(tdTipoAsignacion)
        tr.appendChild(tdDetalleUbicacion)
        tr.appendChild(tdFecha)
        tr.appendChild(tdIncidencias)

        cuerpoTabla.appendChild(tr)
    })

    renderizarGraficas(activos, inactivos, columnas, datos)
}

// EVENTOS
filtroUbicacion.addEventListener("change", procesarYRenderizar)
filtroEstado.addEventListener("change", procesarYRenderizar)
filtroIncidencias.addEventListener("change", procesarYRenderizar)
filtroIntervencion.addEventListener("change", procesarYRenderizar)

poblarFiltroUbicaciones()
procesarYRenderizar()
