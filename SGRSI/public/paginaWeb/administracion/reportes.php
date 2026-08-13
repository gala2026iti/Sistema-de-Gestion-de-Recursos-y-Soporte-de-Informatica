<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION["cedula"])) {
    header("Location: ../index.php?error=sin_sesion");
    exit();
}

if (!isset($_SESSION["administrador"]) || $_SESSION["administrador"] !== true) {
    header("Location: ../index.php?error=no_autorizado");
    exit();
}

require_once __DIR__ . "/../../../app/vista/administracion/reportes.php";
?>