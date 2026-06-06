const rolUsuario = sessionStorage.getItem('rol')
sessionStorage.setItem("rol", rolUsuario);

let opcionesRespectivas

const registroIncidencias = document.getElementById('btnIncidencias')
const registroServicios = document.getElementById('btnServicios')

const cerrarSesion = document.getElementById('btnCerrarSesion')

const panelSoporteAdmin= document.getElementById('btnPanelSoporteA')
const panelSoporteTecnico = document.getElementById('btnPanelSoporteT')
const panelConsultas = document.getElementById('btnPanelConsultasD')


switch (rolUsuario){

case "docente":
opcionesRespectivas = [true, true, true, false, false, false]
break

case "director":
opcionesRespectivas = [true, true, true, false, false, true]

break
case "admin":
opcionesRespectivas = [true, true, true, true, false, false]

break
case "tecnico":
opcionesRespectivas = [true, true, true, false, true, false]

break    
}

if(!opcionesRespectivas[0]){
    registroIncidencias.remove()
}

if(!opcionesRespectivas[1]){
    registroServicios.remove()

}

if(!opcionesRespectivas[2]){
    cerrarSesion.remove()
}

if(!opcionesRespectivas[3]){
    panelSoporteAdmin.remove()
}

if(!opcionesRespectivas[4]){
    panelSoporteTecnico.remove()
}

if(!opcionesRespectivas[5]){
 panelConsultas.remove()
}

