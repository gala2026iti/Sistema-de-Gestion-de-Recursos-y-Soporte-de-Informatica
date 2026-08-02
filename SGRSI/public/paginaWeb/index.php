<?php
$mensajeError = "";
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'credenciales':
            $mensajeError = "Cédula o contraseña incorrectas.";
            break;
        case 'inactivo':
            $mensajeError = "Su usuario se encuentra inactivo. Contacte al administrador.";
            break;
        case 'sin_roles':
            $mensajeError = "Su usuario no cuenta con roles asignados en el sistema.";
            break;
        case 'sin_sesion':
            $mensajeError = "Debe iniciar sesión para acceder al sistema.";
            break;
        case 'no_autorizado':
            $mensajeError = "No tiene permisos suficientes para acceder a este panel.";
            break;
    }
}

// Cargar la vista HTML desde app/vista/index.php
require_once __DIR__ . "/../../app/vista/index.php";
?>