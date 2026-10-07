<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION["cedula"])) {
    header("Location: ../../index.php?error=Acceso Denegado: Sesión no iniciada");
    exit();
}

if (!(($_SESSION["administrador"] && $_SESSION["rolActual"] === "administrador") || ($_SESSION["tecnico"] && $_SESSION["rolActual"] === "tecnico"))) {
    header("Location: ../asda.php?error=Acceso Denegado: Acceso a la zona correspondiente no autorizado");
    exit();
}

if (!isset($_SESSION["csrfToken"])) {
    /**
     * Si el usuario no posee los permisos de acceso correctos por falta
     * de un token, devuelve el estado 403 que es forbidden.
     * 
     * Estados HTTP: https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status#client_error_responses
     */
    http_response_code(403);
    exit("Solicitud rechazada.");
}

require_once __DIR__ . "/../../../app/controlador/recursos/procesarGestionInventarioTecnologico.php";
?>