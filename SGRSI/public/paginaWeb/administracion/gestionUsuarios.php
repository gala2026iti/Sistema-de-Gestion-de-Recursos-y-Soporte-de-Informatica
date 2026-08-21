<?php

require_once __DIR__ . "/../../../config/config.php";

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*
 * Verificar que exista una sesión.
 */
if (!isset($_SESSION["cedula"])) {
    header("Location: ../index.php?error=sin_sesion");
    exit();
}

/*
 * Verificar que el usuario sea administrador.
 */
if (!isset($_SESSION["administrador"]) || $_SESSION["administrador"] !== true) {
    header("Location: ../index.php?error=no_autorizado");
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
    exit("Solicitud Rechazada..." . $_SESSION["csrfToken"]);
}

/*
 * Cargar el controlador.
 */
require_once RUTA_CONTROLADOR . "/cargarGestionUsuarios.php";

?>