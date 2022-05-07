<?php

session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: index.php");
    exit;
}

if ( !isset($_SESSION["rol"]) || $_SESSION["rol"] !== "docente") {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../../app/vista/homeDocente.php";

?>