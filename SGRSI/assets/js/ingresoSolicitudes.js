/* 
El archivo ingresoSolicitudes.js fue el primer
js creado para el manejo de forms y tablas,
futuros js con el mismo funcionamiento
seran copias adaptadas al caso.
*/

// VARIABLES 

const formulario = document.getElementById("formSolicitud")

// FUNCIONES 

const guardarSolicitud = (solicitud) => {

    const solicitudes = (cargarSolicitudes());
    solicitudes.push(solicitud);
    actualizarSolicitudes(solicitudes);

    solicitud.id = longitudID()
    alert("Solicitud registrada con Exito!")
    limpiarCampos()

}

const actualizarSolicitudes = (solicitudes) => {
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes))
}

const cargarSolicitudes = () => {
    const solicitudesLocales = localStorage.getItem("solicitudes");
    if (solicitudesLocales === null){
        return []
    }
    return JSON.parse(solicitudesLocales);
}

const longitudID = () => {
    const solicitudes = cargarSolicitudes();
    const longitud = solicitudes.length
    return longitud + 1;
}

const limpiarCampos = () => {
    formulario.reset()
}

const fechaValida = (fecha) => {
    const [anio, mes, dia] = fecha.split("-").map(Number);

    const fechaIngresada = new Date(anio, mes - 1, dia);
    const hoy = new Date();

    hoy.setHours(0, 0, 0, 0);

    return fechaIngresada >= hoy;
}

// EVENTOS

formulario.addEventListener("submit", function(e){
    e.preventDefault()

const entradaAsunto = document.getElementById("asunto")
const entradaDescripcion = document.getElementById("Area")
const entradaFecha = document.getElementById("fecha")
const usuario = JSON.parse(localStorage.getItem('usuario'))

const solicitud = {
    id: longitudID(),
    asunto: entradaAsunto.value,
    descripcion: entradaDescripcion.value,
    fecha: entradaFecha.value,
    creador: usuario.cedula,
    finalizada: false
}

if(fechaValida(solicitud.fecha)){

    guardarSolicitud(solicitud)

} else {
    alert("Error: Fecha Invalida")
}
})
