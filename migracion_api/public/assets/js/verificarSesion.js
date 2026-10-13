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


function obtenerRolesPermitidos() {
    const rolPermitido =
        document.body.dataset.rolPermitido ?? "";

    const rolesPermitidos =
        document.body.dataset.rolesPermitidos ?? "";

    if (rolesPermitidos !== "") {
        return rolesPermitidos
            .split(",")
            .map(rol => rol.trim())
            .filter(rol => rol !== "");
    }

    if (rolPermitido !== "") {
        return [rolPermitido];
    }

    return [];
}


function mostrarElemento(elemento) {
    if (elemento) {
        elemento.classList.remove("d-none");
    }
}


function configurarVistaSegunRol(rolActual) {
    const menuAdministrador =
        document.getElementById("menuAdministrador");

    const menuAdministracion =
        document.getElementById("menuAdministracion");

    const menuTecnico =
        document.getElementById("menuTecnico");

    const filtroEstadoAdministrador =
        document.getElementById("filtroEstadoAdministrador");

    const botonAgregarLaboratorio =
        document.getElementById("btnAgregarLaboratorio");

    const botonAgregarTaller =
        document.getElementById("btnAgregarTaller");

    const botonRegistrarEquipo =
        document.getElementById("btnRegistrarEquipo");


    if (rolActual === "administrador") {
        mostrarElemento(menuAdministrador);
        mostrarElemento(menuAdministracion);
        mostrarElemento(filtroEstadoAdministrador);
        mostrarElemento(botonAgregarLaboratorio);
        mostrarElemento(botonAgregarTaller);
        mostrarElemento(botonRegistrarEquipo);

        document
            .querySelectorAll(".opcionAdministrador")
            .forEach(mostrarElemento);

        document
            .querySelectorAll(".columnaAdministrador")
            .forEach(mostrarElemento);
    }


    if (rolActual === "tecnico") {
        mostrarElemento(menuTecnico);
    }
}


async function verificarSesion() {
    try {
        const sesion = await obtenerSesion();

        const rolesPermitidos =
            obtenerRolesPermitidos();

        if (
            rolesPermitidos.length > 0 &&
            !rolesPermitidos.includes(sesion.rolActual)
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

        configurarVistaSegunRol(
            sesion.rolActual
        );

    } catch (error) {
        window.location.replace("../index.html");
    }
}


verificarSesion();