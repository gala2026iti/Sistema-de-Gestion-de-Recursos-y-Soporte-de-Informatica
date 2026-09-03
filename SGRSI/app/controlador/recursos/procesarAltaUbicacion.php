<?php

/**
 * @file procesarAltaUbicacion.php
 *
 * @brief Procesa el registro de nuevas ubicaciones.
 *
 * Valida los datos, la sesión y el token CSRF antes de solicitar al modelo
 * el registro de la ubicación.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/AltaUbicacion.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
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

$agregar = trim($_POST["agregar"] ?? "");

if($agregar !== "laboratorio" && $agregar !== "taller")  {
    $mensaje = "El tipo de ubicación no es válido.";
    header("Location: ../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error=" . urlencode($mensaje));
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
        "Location: ../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$AltaUbicacion = new AltaUbicacion($conexion);

$resultado = $AltaUbicacion->registrarUbicacion(
    $agregar
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar la ubicación.";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Ubicación registrada correctamente.";

header(
    "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?resultado="
    . urlencode($mensaje)
);

exit();