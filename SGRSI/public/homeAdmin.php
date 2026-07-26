<?php
session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

if (!(isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador")) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../app/vista/homeAdmin.php";
?>