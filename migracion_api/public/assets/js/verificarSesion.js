const API_SESION = "../index.php?ruta=sesion";

const datosUsuario = document.getElementById("datosUsuario");

async function obtenerSesion() {

    const respuesta = await fetch(API_SESION);

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(
            json.mensaje ?? "No se pudo comprobar la sesión."
        );
    }

    return json.datos;
}


function nombreRol(rol) {

    switch (rol) {

        case "administrador":
            return "Administrador";

        case "tecnico":
            return "Técnico";

        case "docente":
            return "Docente";

        default:
            return "";
    }
}


async function verificarSesion() {

    try {

        const sesion = await obtenerSesion();

        const rolPermitido =
            document.body.dataset.rolPermitido;

        if (
            rolPermitido &&
            sesion.rolActual !== rolPermitido
        ) {
            window.alert(
                "No tiene permiso para acceder a esta página."
            );

            window.location.replace("../index.html");

            return;
        }


        if (datosUsuario) {
            datosUsuario.textContent =
                sesion.nombre +
                " - " +
                nombreRol(sesion.rolActual) +
                " 🡻";
        }

    } catch (error) {
        window.location.replace("../index.html");
    }
}
verificarSesion();