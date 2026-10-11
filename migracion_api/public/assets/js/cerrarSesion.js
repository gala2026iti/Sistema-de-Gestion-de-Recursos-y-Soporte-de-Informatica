const API_LOGOUT = "../index.php?ruta=logout";

const botonCerrarSesion =
    document.getElementById("cerrarSesion");


async function cerrarSesion() {

    const respuesta = await fetch(
        API_LOGOUT,
        {
            method: "POST",
            headers: {
                "X-CSRF-Token":
                    sessionStorage.getItem("csrfToken") ?? ""
            }
        }
    );

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(
            json.mensaje ??
            "No se pudo cerrar la sesión."
        );
    }

    return json.datos;
}


async function gestionarCierreSesion(evento) {

    evento.preventDefault();

    try {

        await cerrarSesion();

        sessionStorage.removeItem("csrfToken");

        window.location.replace("../index.html");

    } catch (error) {

        window.alert(error.message);
    }
}


botonCerrarSesion.addEventListener(
    "click",
    gestionarCierreSesion
);