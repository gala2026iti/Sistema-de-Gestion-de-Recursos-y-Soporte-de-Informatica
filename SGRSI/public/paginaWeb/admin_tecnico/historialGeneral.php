<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION["cedula"])) {
    header("Location: index.php?error=Acceso Denegado: Sesión no iniciada");
    exit();
}

if (/*realizar verificacion de si es admin o tecnico en base a lo que se pdida en la url*/ false){
    header("Location: index.php?error=Acceso Denegado: Acceso a la zona correspondiente no autorizado");
    exit();
}

require_once __DIR__ . "/../../../app/vista/admin_tecnico/historialGeneral.php";
?>