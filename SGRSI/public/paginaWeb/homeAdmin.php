<?php
session_start();

// 1. Impedir acceso sin sesión iniciada
if (!isset($_SESSION["cedula"])) {
    header("Location: index.php?error=sin_sesion");
    exit();
}

// 2. Impedir que un usuario que NO sea admin acceda a administración
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: index.php?error=no_autorizado");
    exit();
}

require_once __DIR__ . "/../../app/vista/homeAdmin.php";
?>