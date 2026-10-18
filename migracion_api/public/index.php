<?php

require_once __DIR__ . "/../config/config.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

if (!isset($_GET["recurso"]) && !isset($_GET["ruta"])) {
    header("Location: ./index.html");
    exit;
}

try {
    $metodo = $_SERVER["REQUEST_METHOD"];

    $idRecurso = null;
    $subrecurso = null;

    /*
     * Nueva ruta REST.
     * Ejemplo: /api/tickets/5/comentarios
     */
    if (isset($_GET["recurso"])) {

        $recurso = trim($_GET["recurso"], "/");

        if ($recurso === "") {
            RespuestaJson::error("El recurso es inválido.", 400);
        }

        $segmentos = explode("/", $recurso);

        $ruta = $segmentos[0] ?? "";
        $idRecurso = $segmentos[1] ?? null;
        $subrecurso = $segmentos[2] ?? null;

        if (count($segmentos) > 3) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        /*
         * Compatibilidad temporal con los controladores
         * que todavía leen el ID desde $_GET.
         */
        if ($idRecurso !== null && $idRecurso !== "") {
            $_GET["id"] = $idRecurso;
        }

    /*
     * Ruta antigua.
     * Se mantiene mientras migramos el frontend.
     */
    } else {

        $ruta = $_GET["ruta"];

        if (!is_string($ruta) || trim($ruta) === "") {
            RespuestaJson::error("La ruta es inválida.", 400);
        }

        $ruta = trim($ruta);
    }

    session_start();

    require RUTA_APP . "/rutas/api.php";

} catch (Throwable $error) {
    error_log((string) $error);

    RespuestaJson::error(
        "Error interno del servidor",
        500
    );
}