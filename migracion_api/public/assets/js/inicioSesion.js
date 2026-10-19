const API_LOGIN = "./index.php/login";

const formularioLogin = document.getElementById("formInicio");

const entradaCedula = document.getElementById("cedula");
const entradaClave = document.getElementById("clave");

const botonMostrarClave = document.getElementById("btnMostrarClave");
const mensajeError = document.getElementById("mensajeError");


async function loguear(cedula, clave) {

    const credenciales = {
        cedula: cedula,
        clave: clave
    };

    const respuesta = await fetch(
        API_LOGIN,
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(credenciales)
        }
    );

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(
            json.mensaje ?? "No se pudo iniciar sesión."
        );
    }

    return json.datos;
}


function mostrarClave() {

    if (entradaClave.type === "password") {

        entradaClave.type = "text";
        botonMostrarClave.innerText = "Ocultar Contraseña";

    } else {

        entradaClave.type = "password";
        botonMostrarClave.innerText = "Mostrar Contraseña";
    }
}


function mostrarError(mensaje) {

    mensajeError.textContent = mensaje;
    mensajeError.classList.remove("d-none");
}


function ocultarError() {

    mensajeError.textContent = "";
    mensajeError.classList.add("d-none");
}


async function gestionarLogin(eventoFormulario) {

    eventoFormulario.preventDefault();

    ocultarError();

    try {

        const sesion = await loguear(
            entradaCedula.value.trim(),
            entradaClave.value
        );

        if (!sesion.csrfToken) {
            throw new Error(
                "La API no devolvió el token CSRF."
            );
        }

        if (!sesion.rolActual) {
            throw new Error(
                "La API no devolvió el rol del usuario."
            );
        }

        // Guardamos el token mientras dure la pestaña.
        sessionStorage.setItem(
            "csrfToken",
            sesion.csrfToken
        );

        window.alert(
            "Sesión iniciada exitosamente."
        );


        switch (sesion.rolActual) {

            case "administrador":

                window.location.replace(
                    "./paginas/homeAdmin.html"
                );

                break;


            case "tecnico":

                window.location.replace(
                    "./paginas/homeTecnico.html"
                );

                break;


            case "docente":

                window.location.replace(
                    "./paginas/homeDocente.html"
                );

                break;


            default:

                throw new Error(
                    "El usuario no tiene un rol válido."
                );
        }

    } catch (error) {

        mostrarError(error.message);
    }
}


formularioLogin.addEventListener(
    "submit",
    gestionarLogin
);

botonMostrarClave.addEventListener(
    "click",
    mostrarClave
);