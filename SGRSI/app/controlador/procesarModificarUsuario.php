<?php

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosUsuario.php";

/*
 * Solamente se permite POST.
 */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Recuperamos los datos enviados por el formulario.
 */
$cedula = trim($_POST["cedula"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");

$clave = $_POST["clave"] ?? "";
$confirmarClave = $_POST["confirmarClave"] ?? "";

$roles = $_POST["roles"] ?? [];


/*
 * Los roles deben llegar como un array.
 */
if (!is_array($roles)) {

    $mensaje = "Los roles seleccionados no son válidos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Normalizamos los roles.
 *
 * Eliminamos espacios y convertimos todo a minúsculas.
 */
$roles = array_map(
    function ($rol) {
        return strtolower(trim($rol));
    },
    $roles
);


/*
 * Eliminamos roles repetidos.
 */
$roles = array_unique($roles);


/*
 * Validamos que los campos principales
 * no estén vacíos.
 */
if (
    $cedula === "" ||
    $nombre === "" ||
    $correo === ""
) {

    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos la cédula.
 */
if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {

    $mensaje = "La cédula debe contener exactamente 8 dígitos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos el nombre.
 */
if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/", $nombre)) {

    $mensaje = "El nombre contiene caracteres no válidos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Validamos el correo.
 */
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $mensaje = "El correo electrónico no es válido.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Roles permitidos por el sistema.
 */
$rolesValidos = [
    "administrador",
    "tecnico",
    "docente"
];


/*
 * Comprobamos que todos los roles recibidos
 * sean válidos.
 */
foreach ($roles as $rol) {

    if (!in_array($rol, $rolesValidos, true)) {

        $mensaje = "Uno de los roles seleccionados no es válido.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );

        exit();
    }
}


/*
 * La contraseña es opcional al modificar.
 *
 * Si ambos campos están vacíos, se conserva
 * la contraseña actual.
 */
$claveHash = null;

if ($clave !== "" || $confirmarClave !== "") {

    /*
     * Si se comenzó a introducir una nueva contraseña,
     * ambos campos deben estar completos.
     */
    if ($clave === "" || $confirmarClave === "") {

        $mensaje = "Debe completar ambos campos de contraseña.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );

        exit();
    }


    /*
     * Validamos la longitud de la nueva contraseña.
     */
    if (strlen($clave) < 12) {

        $mensaje = "La contraseña debe contener al menos 12 caracteres.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );

        exit();
    }


    /*
     * Comprobamos que ambas contraseñas coincidan.
     */
    if ($clave !== $confirmarClave) {

        $mensaje = "Las contraseñas no coinciden.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );

        exit();
    }


    /*
     * Generamos el hash de la nueva contraseña.
     */
    $claveHash = password_hash($clave, PASSWORD_DEFAULT);
}


/*
 * Creamos la conexión con la base de datos.
 */
$conectorPDO = new ConectorPDO(
    "127.0.0.1:3306",
    "root",
    "",
    "sgrsi"
);

$conexion = $conectorPDO->establecerConexion();


if ($conexion === null) {

    $mensaje = "No se pudo establecer conexión con la base de datos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Creamos el acceso a los datos.
 */
$accesoDatosUsuario = new AccesoDatosUsuario($conexion);


/*
 * Modificamos los datos del usuario.
 *
 * Si $claveHash es null, se conserva la contraseña actual.
 * Si contiene un hash, se actualiza la contraseña.
 */
$resultado = $accesoDatosUsuario->modificarUsuario(
    $cedula,
    $nombre,
    $correo,
    $roles,
    $claveHash
);


/*
 * Cerramos la conexión.
 */
$conectorPDO->desconectar();


/*
 * Comprobamos el resultado de la modificación.
 */
if (!$resultado) {

    $mensaje = "No se pudo modificar el usuario. La cédula o el correo pueden estar registrados.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Modificación realizada correctamente.
 */
$mensaje = "Usuario modificado correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();