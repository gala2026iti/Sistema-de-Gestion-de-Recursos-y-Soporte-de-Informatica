<?php

require_once __DIR__ . "/../config/config.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

try {
    $metodo = $_SERVER["REQUEST_METHOD"];

    $pathInfo = trim($_SERVER["PATH_INFO"] ?? "", "/");

    /*
     * Si se abre solamente index.php,
     * se muestra la página de inicio.
     */
    if ($pathInfo === "") {
        header("Location: ./index.html");
        exit;
    }

    $segmentos = explode("/", $pathInfo);

    if (count($segmentos) > 3) {
        RespuestaJson::error("La ruta solicitada no es válida.", 404);
    }

    $ruta = $segmentos[0] ?? "";
    $idRecurso = $segmentos[1] ?? null;
    $subrecurso = $segmentos[2] ?? null;

    if ($ruta === "") {
        RespuestaJson::error( "La ruta solicitada no es válida.", 404);
    }

    session_start();

    require RUTA_APP . "/rutas/api.php";

} catch (Throwable $error) {
    error_log((string) $error);
    RespuestaJson::error( "Error interno del servidor", 500);
}