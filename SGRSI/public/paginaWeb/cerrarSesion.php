<?php
// 1. Iniciar o reanudar la sesión existente
session_start();

// 2. Cabeceras HTTP Anti-Caché (Obligatorio para que el botón "Atrás" no muestre la página tras cerrar sesión)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 3. Control de acceso: Verificar si hay sesión activa y el rol correspondiente
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["administrador"]) || !$_SESSION["administrador"]) {
    // Si no está autenticado o no tiene el rol de administrador, se destruye lo que quede y se redirige al login
    session_unset();
    session_destroy();
    header("Location: index.php?error=" . urlencode("Acceso denegado: Debe iniciar sesión con el rol correspondiente."));
    exit();
}
?>