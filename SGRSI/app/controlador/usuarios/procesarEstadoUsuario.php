<?php

/**
 * @file procesarEstadoUsuario.php
 *
 * @brief Procesa la activación o desactivación de usuarios.
 *
 * Recibe mediante POST la cédula y la acción solicitada, valida
 * la petición y solicita al modelo actualizar el estado del usuario.
 */

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/usuarios/EstadoDatosUsuario.php";

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
    $mensaje = "Solicitud rechazada: token inválido.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$cedula = trim($_POST["cedula"] ?? "");
$accion = strtolower(trim($_POST["accion"] ?? ""));

if ($cedula === "" || $accion === "") {
    $mensaje = "No se recibieron los datos necesarios.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

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

$estadoDatosUsuario = new EstadoDatosUsuario($conexion);

$resultado = $estadoDatosUsuario->cambiarEstadoUsuario(
    $cedula,
    $activo
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el estado del usuario.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = $activo
    ? "Usuario activado correctamente."
    : "Usuario desactivado correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();