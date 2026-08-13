<?php
session_start();

// 1. Impedir acceso sin sesión iniciada
if (!isset($_SESSION["cedula"])) {
    header("Location: index.php?error=sin_sesion");
    exit();
}

// 2. Impedir que un usuario que NO sea admin acceda a administración
if (!isset($_SESSION["cedula"]) || empty($_SESSION["administrador"])) {
    header("Location: index.php?error=" . urlencode("Acceso no autorizado al panel de administración."));
    exit();
}

require_once __DIR__ . "/../../app/vista/homeAdmin.php";
?>