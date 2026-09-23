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
require_once RUTA_MODELO . "/prestamos/CargarPrestamos.php";

$estado = strtolower(htmlspecialchars(trim($_GET["estado"] ?? "")));
$id = htmlspecialchars(trim($_GET["id"] ?? ""));

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
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$accesoDatosPrestamo = new CargarPrestamos($conexion);
$prestamos = $accesoDatosPrestamo->listarPrestamos($estado, $id);

$conectorPDO->desconectar();
