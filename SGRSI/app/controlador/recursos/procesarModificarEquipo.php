<?php

/**
 * @file procesarModificarEquipo.php
 *
 * @brief Procesa la modificación de la ubicación de un equipo.
 *
 * Valida los datos recibidos y solicita al modelo actualizar la ubicación asociada.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/ModificarUbicacionEquipo.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso denegado: debe iniciar sesión.";

    header(
        "Location: ../../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!($_SESSION["administrador"] ?? false)) {
    $mensaje = "Acceso denegado: no tiene permisos para realizar esta operación.";

    header(
        "Location: ../../../public/paginaWeb/index.php?error="
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
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$idEquipo = trim(htmlspecialchars($_POST["idEquipo"] ?? ""));
$ubicacion = explode(" ", trim(htmlspecialchars($_POST["ubicacion"] ?? "")));
$posicion = trim(htmlspecialchars($_POST["posicion"] ?? ""));
$tipoUbicacionOrigen = trim(htmlspecialchars($_POST["tipoUbicacionOrigen"] ?? ""));

$idUbicacion = $ubicacion[1] ?? "";
$tipoUbicacion = $ubicacion[0] ?? "";


if (!is_numeric($idEquipo) || strlen($idEquipo) > 6) {
    $mensaje = "El ID del equipo debe ser un número entero de hasta 6 dígitos.";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($tipoUbicacionOrigen !== "prestamo" && $tipoUbicacionOrigen !== "ninguna" && $tipoUbicacionOrigen !== "laboratorio" && $tipoUbicacionOrigen !== "taller") {
 $mensaje = "No se pudo identificar donde se encuentra alojado el equipo a mover.";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (($tipoUbicacion === "laboratorio" || $tipoUbicacion === "taller") && (!is_numeric($idUbicacion) || (int)$idUbicacion <= 0)
) {
    $mensaje = "El ID de la ubicación debe ser un número entero mayor a 0.";


    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );

    exit();
}

if($tipoUbicacion !== "laboratorio" && $tipoUbicacion !== "taller" && $tipoUbicacion !== "prestamo" && $tipoUbicacion !== "ninguna") {
    $mensaje = "El tipo de ubicación no es válido: solo se permiten 'laboratorio', 'taller', 'prestamo' o 'ninguna'";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
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
        "Location: ../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}


$modificarUbicacionEquipo = new ModificarUbicacionEquipo($conexion);

$resultado = $modificarUbicacionEquipo->modificarUbicacionEquipo(
    $idEquipo,
    $idUbicacion,
    $tipoUbicacion,
    $posicion,
    $tipoUbicacionOrigen
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el equipo. La ubicación elegida se encuentra en uso.";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Equipo movido correctamente.";

header(
    "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?resultado="
    . urlencode($mensaje)
);

exit();