<?php

/**
 * @file procesarAltaUsuario.php
 *
 * @brief Procesa el registro de nuevos usuarios.
 *
 * Valida los datos recibidos mediante POST, genera el hash de la
 * contraseña y solicita al modelo el registro del usuario y sus roles.
 */

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaUsuario.php";

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

if (
    $cedula === "" ||
    $nombre === "" ||
    $correo === "" ||
    $clave === "" ||
    $confirmarClave === "" ||
    empty($roles)
) {
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
 * La contraseña se almacena mediante un hash
 * y nunca como texto plano.
 */
$claveHash = password_hash($clave, PASSWORD_DEFAULT);

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

$altaDatosUsuario = new AltaDatosUsuario($conexion);

$resultado = $altaDatosUsuario->registrarUsuario(
    $cedula,
    $nombre,
    $correo,
    $claveHash,
    $roles
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el usuario. La cédula o el correo pueden estar ya registrados.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Usuario registrado correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();