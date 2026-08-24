<?php

/**
 * @file procesarAltaUsuario.php
 *
 * @brief Procesa el registro de nuevos usuarios.
 *
 * Valida los datos recibidos, comprueba la sesión y el token CSRF
 * y solicita al modelo el registro del usuario.
 */
require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/usuarios/AltaUsuario.php";

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

if ($cedula < 10000000 || $cedula > 99999999) {
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

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

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

$AltaUsuario = new AltaUsuario($conexion);

$resultado = $AltaUsuario->registrarUsuario(
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