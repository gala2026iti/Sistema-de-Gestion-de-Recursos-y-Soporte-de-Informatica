<?php

session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: index.php");
    exit;
}

if ( !isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../../../app/vista/tecnico/tablaPrestamos.php";

?>