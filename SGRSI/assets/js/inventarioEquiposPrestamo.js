const tabla = document.getElementById("tablaEquipos");
const cuerpoTabla = tabla.querySelector("tbody");

const cargarPrestamos = () => {
    const prestamos = localStorage.getItem("prestamos")

    if (prestamos === null || prestamos === undefined || prestamos === "") {
        return []
    } else {
        return JSON.parse(prestamos)
    }
}

const obtenerPrestamista = (idEquipo) => {
    const prestamos = cargarPrestamos()
    const prestamoEquipo = prestamos.find(p => p.idEquipo === idEquipo && p.devuelto === false)
    if (prestamoEquipo) {
        return `${prestamoEquipo.nombrePrestado} (${prestamoEquipo.cedulaPrestado})`
    } else {
        return "No asignado"
    }
}

const cargarEquiposFiltrados = () => {
    const equipos = localStorage.getItem("equipos")
    if (equipos === null || equipos === undefined || equipos === "") {
        return []
    } else {
        const equiposJSON = JSON.parse(equipos)
        return equiposJSON.filter(e => e.ubicacion === "prestamo")

    }
}

const actualizarTabla = () => {
    cuerpoTabla.innerHTML = ""
    const equiposPrestamo = cargarEquiposFiltrados()

    equiposPrestamo.forEach(e => {
        const fila = document.createElement("tr")
        const idEquipo = document.createElement("td")
        idEquipo.innerText = e.id

        const estadoEquipo = document.createElement("td")
        estadoEquipo.innerText = e.activo ? "Activo" : "Inactivo"

        const prestamistaEquipo = document.createElement("td")
        prestamistaEquipo.innerText = obtenerPrestamista(e.id)

        const opciones = document.createElement("td")

        const botonIncidencias = document.createElement("button")
        botonIncidencias.addEventListener("click", () => {
            localStorage.setItem("idEquipoIncidencias", e.id)
            alert("aca debe añadir lo que pasa al prsionar el boton, se guardo el id del equipo en idEquipoIncidencias")
        })

        botonIncidencias.textContent = "Ver Incidencias"
        botonIncidencias.classList.add("btn", "btn-warning")

        opciones.appendChild(botonIncidencias)
        fila.appendChild(idEquipo)
        fila.appendChild(estadoEquipo)
        fila.appendChild(prestamistaEquipo)
        fila.appendChild(opciones)

        cuerpoTabla.appendChild(fila)

    })
}

actualizarTabla()
