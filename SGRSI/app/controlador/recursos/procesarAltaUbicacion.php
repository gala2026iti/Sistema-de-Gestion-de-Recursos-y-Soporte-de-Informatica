<?php

/**
 * @file procesarAltaUsuario.php
 *
 * @brief Procesa el registro de nuevos usuarios.
 *
 * Valida los datos recibidos mediante POST, genera el hash de la
 * contraseña y solicita al modelo el registro del usuario y sus roles.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/AltaUbicacion.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
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

$id = trim($_POST["id"] ?? "");
$tipo = trim($_POST["tipo"] ?? "");



if (
    $id === "" ||
    $tipo === "" 
    ) {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(!is_numeric($id))  {
    $mensaje = "El ID de la ubicación debe ser un número entero.";
    header("Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($tipo) !== "laboratorio" && strlen($tipo) !== "salon") {
    $mensaje = 'El tipo de ubicación no es válido, debe ser "laboratorio" o "salon".';
    header("Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error=" . urlencode($mensaje));
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
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$AltaUbicacion = new AltaUbicacion($conexion);

$resultado = $AltaUbicacion->registrarUbicacion(
    $id,
    $tipo
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar la ubicación.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Ubicación registrada correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?resultado="
    . urlencode($mensaje)
);

exit();