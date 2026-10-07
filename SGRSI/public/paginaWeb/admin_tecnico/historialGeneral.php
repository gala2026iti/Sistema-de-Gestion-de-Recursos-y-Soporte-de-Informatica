<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION["cedula"])) {
    header("Location: ../index.php?error=Acceso Denegado: Sesión no iniciada");
    exit();
}

if (!(($_SESSION["tecnico"] && $_SESSION["rolActual"] === "tecnico") || ($_SESSION["administrador"] && $_SESSION["rolActual"] === "administrador"))) {
    $mensaje = "Acceso denegado: No tiene permisos para realizar esta operación.";

    header(
        "Location: ../../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

require_once __DIR__ . "/../../../app/vista/admin_tecnico/historialGeneral.php";
?>