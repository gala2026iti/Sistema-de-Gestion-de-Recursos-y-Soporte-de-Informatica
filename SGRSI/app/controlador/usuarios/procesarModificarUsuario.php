<?php

/**
 * @file procesarModificarUsuario.php
 *
 * @brief Procesa la modificación de usuarios.
 *
 * Valida los datos recibidos mediante POST y solicita al modelo
 * la actualización de los datos, contraseña y roles del usuario.
 */

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/usuarios/ModificarDatosUsuario.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso denegado: debe iniciar sesión.";

    header(
        "Location: ../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!($_SESSION["administrador"] ?? false)) {
    $mensaje = "Acceso denegado: no tiene permisos para realizar esta operación.";

    header(
        "Location: ../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

/*
 * Comprobamos que la solicitud incluya
 * el token CSRF de la sesión.
 */
$csrfToken = $_POST["csrfToken"] ?? "";

if (
    !isset($_SESSION["csrfToken"]) ||
    !is_string($csrfToken) ||
    !hash_equals($_SESSION["csrfToken"], $csrfToken)
) {
    $mensaje = "Solicitud rechazada: token de seguridad inválido.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$cedula = trim($_POST["cedula"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$clave = $_POST["clave"] ?? "";
$confirmarClave = $_POST["confirmarClave"] ?? "";
$roles = $_POST["roles"] ?? [];

if (!is_array($roles)) {
    $mensaje = "Los roles seleccionados no son válidos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$roles = array_map(
    function ($rol) {
        return strtolower(trim($rol));
    },
    $roles
);

$roles = array_unique($roles);

if ($cedula === "" || $nombre === "" || $correo === "") {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
    $mensaje = "La cédula debe contener exactamente 8 dígitos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/", $nombre)) {
    $mensaje = "El nombre contiene caracteres no válidos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $mensaje = "El correo electrónico no es válido.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$rolesValidos = ["administrador", "tecnico", "docente"];

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
 * Si no se ingresa una nueva contraseña,
 * se conserva la contraseña actual.
 */
$claveHash = null;

if ($clave !== "" || $confirmarClave !== "") {
    if ($clave === "" || $confirmarClave === "") {
        $mensaje = "Debe completar ambos campos de contraseña.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );
        exit();
    }

    if (strlen($clave) < 12) {
        $mensaje = "La contraseña debe contener al menos 12 caracteres.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );
        exit();
    }

    if ($clave !== $confirmarClave) {
        $mensaje = "Las contraseñas no coinciden.";

        header(
            "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
            . urlencode($mensaje)
        );
        exit();
    }

    $claveHash = password_hash($clave, PASSWORD_DEFAULT);
}

$conectorPDO = new ConectorPDO(
    $_ENV['DB_HOST'] . ":" . 
    $_ENV['DB_PUERTO'], 
    $_ENV['DB_USUARIO'], 
    $_ENV['DB_CLAVE'], 
    $_ENV['DB_NOMBRE']
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

$modificarDatosUsuario = new ModificarDatosUsuario($conexion);

$resultado = $modificarDatosUsuario->modificarUsuario(
    $cedula,
    $nombre,
    $correo,
    $roles,
    $claveHash
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el usuario. La cédula o el correo pueden estar registrados.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Usuario modificado correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();