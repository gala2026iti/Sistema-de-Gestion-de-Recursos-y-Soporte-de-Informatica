<?php

/**
 * @file procesarCargarUsuarios.php
 *
 * @brief Carga los usuarios para la página de gestión.
 *
 * Obtiene los filtros enviados mediante GET, consulta los usuarios
 * correspondientes y carga la vista de gestión de usuarios.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/usuarios/CargarUsuarios.php";

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
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$accesoDatosUsuario = new CargarUsuarios($conexion);
$usuarios = $accesoDatosUsuario->listarUsuarios($rol, $estado);

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/administracion/gestionUsuarios.php";