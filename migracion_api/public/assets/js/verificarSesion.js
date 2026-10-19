const API_SESION = "../index.php/sesion";

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

async function cambiarRol(rol) {
    const respuesta = await fetch(
        API_SESION, {
        method: "PATCH",

        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
        },

        body: JSON.stringify({
            rol: rol
        })
    }
    );

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(
            json.mensaje ??
            "No se pudo cambiar el rol."
        );
    }

    return json.datos;
}

function redirigirSegunRol(rol) {
    switch (rol) {

        case "administrador":
            window.location.replace("./homeAdmin.html");
            break;

        case "tecnico":
            window.location.replace("./homeTecnico.html");
            break;

        case "docente":
            window.location.replace("./homeDocente.html");
            break;
    }
}


function crearOpcionRol(rol) {
    const item = document.createElement("li");

    const enlace = document.createElement("a");

    enlace.href = "#";
    enlace.classList.add("opcion-cambio-rol");
    enlace.textContent = "Cambiar a " + nombreRol(rol);

    enlace.addEventListener("click", async function (evento) {
        evento.preventDefault();

        try {
            const resultado = await cambiarRol(rol);

            redirigirSegunRol(resultado.rolActual);

        } catch (error) {
            window.alert(error.message);
        }
    });

    item.appendChild(enlace);

    return item;
}

function configurarOpcionesRoles(sesion) {
    const opcionesRoles =
        document.getElementById("opcionesRoles");

    const cerrarSesion =
        document.getElementById("cerrarSesion");

    if (!opcionesRoles || !cerrarSesion) {
        return;
    }

    const itemCerrarSesion =
        cerrarSesion.closest("li");


    document
        .querySelectorAll(".opcion-cambio-rol")
        .forEach(function (opcion) {
            opcion.closest("li")?.remove();
        });


    const roles = [
        {
            nombre: "administrador",
            disponible: Boolean(sesion.administrador)
        },
        {
            nombre: "tecnico",
            disponible: Boolean(sesion.tecnico)
        },
        {
            nombre: "docente",
            disponible: Boolean(sesion.docente)
        }
    ];


    for (const rol of roles) {

        if (
            rol.disponible &&
            rol.nombre !== sesion.rolActual
        ) {
            opcionesRoles.insertBefore(
                crearOpcionRol(rol.nombre),
                itemCerrarSesion
            );
        }
    }
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
            datosUsuario.textContent = sesion.nombre + " - " + nombreRol(sesion.rolActual) + " 🡻";
        }
        configurarOpcionesRoles(sesion);
        configurarVistaSegunRol(sesion.rolActual);

    } catch (error) {
        window.location.replace("../index.html");
    }
}
verificarSesion();