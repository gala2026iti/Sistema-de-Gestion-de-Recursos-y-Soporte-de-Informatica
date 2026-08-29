<?php

/**
 * @file procesarCargarUbicaciones.php
 *
 * @brief Carga las ubicaciones registradas.
 *
 * Consulta las ubicaciones mediante el modelo y carga la vista correspondiente.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/CargarUbicaciones.php";

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

$accesoDatosUbicacion = new CargarUbicaciones($conexion);
$ubicaciones = $accesoDatosUbicacion->listarUbicaciones();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/administracion/gestionInventarioTecnologico.php";