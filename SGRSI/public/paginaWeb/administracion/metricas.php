<?php

session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: index.php");
    exit;
}

if ( !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../../../app/vista/administracion/metricas.php";

?>