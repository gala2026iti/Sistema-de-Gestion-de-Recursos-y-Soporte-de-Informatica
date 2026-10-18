<?php

require_once RUTA_CONTROLADOR . "/LoginController.php";
require_once RUTA_CONTROLADOR . "/UsuarioController.php";
require_once RUTA_CONTROLADOR . "/EquipoController.php";
require_once RUTA_CONTROLADOR . "/UbicacionController.php";
require_once RUTA_CONTROLADOR . "/TicketController.php";
require_once RUTA_CONTROLADOR . "/SolicitudController.php";
require_once RUTA_CONTROLADOR . "/PrestamoController.php";

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

    case "usuarios":
        $controlador = new UsuarioController();
        $controlador->gestionar($metodo);
        break;

    case "equipos":
        $controlador = new EquipoController();
        $controlador->gestionar($metodo);
        break;

    case "ubicaciones":
        $controlador = new UbicacionController();
        $controlador->gestionar($metodo);
        break;

    case "tickets":
        $controlador = new TicketController();
        $controlador->gestionar($metodo);
        break;

    case "solicitudes":
        $controlador = new SolicitudController();
        $controlador->gestionar($metodo);
        break;

    case "prestamos":
        $controlador = new PrestamoController();
        $controlador->gestionar($metodo);
        break;

    default:
        RespuestaJson::error("Recurso no encontrado", 404);
}