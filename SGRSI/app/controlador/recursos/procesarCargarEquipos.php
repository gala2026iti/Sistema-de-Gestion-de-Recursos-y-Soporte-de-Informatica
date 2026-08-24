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

$rol = strtolower(trim($_GET["rol"] ?? ""));
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
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$accesoDatosEquipo = new CargarEquipos($conexion);
$equipos = $accesoDatosEquipo->listarEquipos($rol, $estado);

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/administracion/gestionEquipos.php";