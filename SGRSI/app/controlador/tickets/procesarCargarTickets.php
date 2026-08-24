<?php

/**
 * @file procesarCargarTickets.php
 *
 * @brief Carga los tickets para su gestión.
 *
 * Obtiene los filtros enviados mediante GET, consulta los registros y carga la vista correspondiente.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/tickets/CargarTickets.php";

$tiempo = strtolower(trim($_GET["tiempo"] ?? ""));
$gravedad = strtolower(trim($_GET["gravedad"] ?? ""));
$clasificacion = strtolower(trim($_GET["clasificacion"] ?? ""));
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
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$accesoDatosTicket = new CargarTickets($conexion);
$usuarios = $accesoDatosTicket->listarTickets($tiempo, $gravedad, $clasificacion, $estado);

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/tecnico/homeTecnico.php";