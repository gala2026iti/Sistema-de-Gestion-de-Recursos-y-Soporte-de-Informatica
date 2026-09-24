<?php

/**
 * @file procesarCargarEquipos.php
 *
 * @brief Carga los equipos para su gestión.
 *
 * Obtiene los filtros enviados mediante GET, consulta los equipos y carga la vista correspondiente.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/CargarEquipos.php";

$orden = strtolower(htmlspecialchars(trim($_GET["orden"] ?? "")));
$estado = strtolower(htmlspecialchars(trim($_GET["estado"] ?? "")));
$ubicacion = htmlspecialchars(trim($_GET["ubicacion"] ?? ""));
$tipoUbicacion = strtolower(htmlspecialchars(trim($_GET["tipoUbicacion"] ?? "")));


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

if (!isset($_SESSION["tecnico"]) && !isset($_SESSION["administrador"])) {
    header("Location: index.php?error=Acceso Denegado: Acceso a la zona correspondiente no autorizado");
    exit();

}

if (!empty($ubicacion)){
    if(!(is_numeric($ubicacion) && $ubicacion > 0)) {
            
    $mensaje = "Número de ubicación a buscar no válida.";

    $urlAnterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

    header("Location: " . $urlAnterior . "?error=" . urlencode($mensaje));
    exit();
}
}

if (!empty($tipoUbicacion)) {
    if(!($tipoUbicacion === "prestamo" || $tipoUbicacion === "laboratorio" || $tipoUbicacion === "taller")) {
    $mensaje = "Tipo de ubicación a buscar no válido.";

    $urlAnterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

    header("Location: " . $urlAnterior . "?error=" . urlencode($mensaje));
    exit();
}
}

if (!empty($orden)){
    if(!($orden === "reciente" || $orden === "antiguo" || $orden === "masincidencias" || $orden === "menosincidencias")) {
        $mensaje = "Orden de visualización no válido.";

    $urlAnterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

    header("Location: " . $urlAnterior . "?error=" . urlencode($mensaje));
    exit();
}
}

if (!empty($estado)) {
    if (!($estado === "activo" || $estado === "inactivo")) {
        $mensaje = "Tipo de estado a buscar no válido.";

    $urlAnterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

    header("Location: " . $urlAnterior . "?error=" . urlencode($mensaje));
    exit();
}
}

$accesoDatosEquipo = new CargarEquipos($conexion);
$equipos = $accesoDatosEquipo->listarEquipos($orden, $estado, $ubicacion, $tipoUbicacion);

$conectorPDO->desconectar();
