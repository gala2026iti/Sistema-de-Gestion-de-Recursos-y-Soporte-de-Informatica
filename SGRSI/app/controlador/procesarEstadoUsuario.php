<?php

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosUsuario.php";

/**
 * @brief Procesa la activación o desactivación de un usuario.
 *
 * Recibe mediante POST la cédula del usuario y la acción
 * que se desea realizar. Luego solicita al modelo que
 * actualice el campo "activo" de la tabla USUARIO.
 */

/*
 * Solamente se permite acceder mediante POST.
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
$accion = strtolower(trim($_POST["accion"] ?? ""));


/*
 * Verificamos que se haya recibido una cédula
 * y una acción.
 */
if ($cedula === "" || $accion === "") {

    $mensaje = "No se recibieron los datos necesarios.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}


/*
 * Convertimos la acción recibida en el estado
 * que debe tener el usuario.
 */
if ($accion === "activar") {

    $activo = true;

} elseif ($accion === "desactivar") {

    $activo = false;

} else {

    $mensaje = "La acción solicitada no es válida.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}

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

$accesoDatosUsuario = new AccesoDatosUsuario($conexion);


/*
 * Modificamos el estado del usuario.
 */
$resultado = $accesoDatosUsuario->cambiarEstadoUsuario(
    $cedula,
    $activo
);
/*
 * Cerramos la conexión.
 */
$conectorPDO->desconectar();

/*
 * Comprobamos el resultado de la operación.
 */
if (!$resultado) {

    $mensaje = "No se pudo modificar el estado del usuario.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );

    exit();
}
/*
 * Informamos que la operación fue realizada correctamente.
 */
if ($activo) {

    $mensaje = "Usuario activado correctamente.";

} else {

    $mensaje = "Usuario desactivado correctamente.";
}

header(
    "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();