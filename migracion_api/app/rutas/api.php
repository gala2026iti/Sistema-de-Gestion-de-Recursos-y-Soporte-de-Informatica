<?php

require_once RUTA_CONTROLADOR . "/LoginController.php";

// Espacio donde se encuentran los endpoints
// que establecen la comunicación con el controlador.s
switch ($ruta) {

    case "login":
        $controlador = new LoginController();
        $controlador->autenticar($metodo);
        break;
    case "sesion":
    $controlador = new LoginController();
    $controlador->obtenerSesion($metodo);
    break;

    case "logout":
        $controlador = new LoginController();
        $controlador->cerrarSesion($metodo);
        break;

    default:
        RespuestaJson::error(
            "Ruta no encontrada",
            404
        );
}