<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
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

$idTicket = htmlspecialchars(trim($_GET["id"] ?? ""));

$tiempo = strtolower(htmlspecialchars(trim($_GET["tiempo"] ?? "")));
$gravedad = strtolower(htmlspecialchars(trim($_GET["gravedad"] ?? "")));
$clasificacion = strtolower(htmlspecialchars(trim($_GET["clasificacion"] ?? "")));
$estado = strtolower(htmlspecialchars(trim($_GET["estado"] ?? "")));
$ciTecnico = trim($_SESSION["cedula"] ?? "");
$idTicket = htmlspecialchars(trim($_GET["id"] ?? ""));

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
        "Location: ../../public/paginaWeb/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!is_numeric($ciTecnico) || strlen($ciTecnico) !== 8) {
    $mensaje = "Cédula de técnico inválida, debe ser de 8 dígitos.";

    header(
        "Location: ../../public/paginaWeb/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!empty($idTicket) && !is_numeric($idTicket)) {
    $mensaje = "Error al cargar la información del ticket.";

    header(
        "Location: ../../public/paginaWeb/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$accesoDatosTicket = new CargarTickets($conexion);
$tickets = $accesoDatosTicket->listarTickets($tiempo, $gravedad, $clasificacion, $estado, $ciTecnico, $idTicket);

$conectorPDO->desconectar();

$resultadoURL = $accesoDatosTicket->obtenerURL();
$ticketsRegistrados = $resultadoURL[0];
$ticketsPersonales = $resultadoURL[1];
$detalleTicket = $resultadoURL[2];

if($ticketsPersonales){
    require_once __DIR__ . "/../../vista/tecnico/ticketsPersonales.php";
} else if ($ticketsRegistrados){
    require_once __DIR__ . "/../../vista/homeTecnico.php";
} else if ($detalleTicket){
    require_once __DIR__ . "/../../vista/tecnico/detalleTicket.php";
}




