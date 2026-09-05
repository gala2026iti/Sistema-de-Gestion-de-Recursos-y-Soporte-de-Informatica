<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * @file procesarEstadoSolicitud.php
 *
 * @brief Procesa cambios de estado de solicitudes.
 *
 * Valida la solicitud recibida y solicita al modelo actualizar su estado de finalización.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/solicitudes/CargarSolicitudes.php";

$estado = strtolower(trim($_GET["estado"] ?? ""));

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
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$accesoDatosSolicitud = new CargarSolicitudes($conexion);
$solicitudes = $accesoDatosSolicitud->listarSolicitudes($estado);

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/tecnico/gestionSolicitudes.php";