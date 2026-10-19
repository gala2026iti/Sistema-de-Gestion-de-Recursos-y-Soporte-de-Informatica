<?php

require_once __DIR__ . "/../config/config.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

try {
    $metodo = $_SERVER["REQUEST_METHOD"];

    $ruta = "";
    $idRecurso = null;
    $subrecurso = null;

    /*
     * Rutas mediante PATH_INFO.
     *
     * Ejemplos:
     * /index.php/tickets
     * /index.php/tickets/5
     * /index.php/tickets/5/comentarios
     */
    $pathInfo = trim($_SERVER["PATH_INFO"] ?? "", "/");

    if ($pathInfo !== "") {
        $segmentos = explode("/", $pathInfo);

        $ruta = $segmentos[0] ?? "";
        $idRecurso = $segmentos[1] ?? null;
        $subrecurso = $segmentos[2] ?? null;

        if (count($segmentos) > 3) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        /*
         * Compatibilidad temporal con los controladores
         * que todavía obtienen el ID mediante $_GET.
         */
        if ($idRecurso !== null && $idRecurso !== "") {
            $_GET["id"] = $idRecurso;
        }

    /*
     * Conservamos temporalmente las rutas antiguas.
     */
    } elseif (isset($_GET["ruta"])) {
        $ruta = trim($_GET["ruta"]);

        if ($ruta === "") {
            RespuestaJson::error("La ruta es inválida.", 400);
        }

    /*
     * Si se abre solamente index.php,
     * mostramos la página principal.
     */
    } else {
        header("Location: ./index.html");
        exit;
    }
    session_start();
    require RUTA_APP . "/rutas/api.php";

} catch (Throwable $error) {
    error_log((string) $error);
    RespuestaJson::error("Error interno del servidor",500);
}