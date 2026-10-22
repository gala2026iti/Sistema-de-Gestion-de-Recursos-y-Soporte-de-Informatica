<?php

require_once RUTA_CONTROLADOR . "/LoginController.php";
require_once RUTA_CONTROLADOR . "/UsuarioController.php";
require_once RUTA_CONTROLADOR . "/EquipoController.php";
require_once RUTA_CONTROLADOR . "/UbicacionController.php";
require_once RUTA_CONTROLADOR . "/TicketController.php";
require_once RUTA_CONTROLADOR . "/SolicitudController.php";
require_once RUTA_CONTROLADOR . "/PrestamoController.php";
require_once RUTA_CONTROLADOR . "/HistorialController.php";
require_once RUTA_CONTROLADOR . "/MetricasController.php";

switch ($ruta) {
    case "login":
        $controlador = new LoginController();
        $controlador->autenticar($metodo);
        break;

    case "sesion":
        $controlador = new LoginController();
        $controlador->gestionarSesion($metodo);
        break;

    case "usuarios":
        $controlador = new UsuarioController();
        $controlador->gestionar($metodo, $idRecurso, $segmentosRuta);
        break;

    case "equipos":
        $controlador = new EquipoController();
        $controlador->gestionar($metodo, $idRecurso, $subrecurso, $segmentosRuta);
        break;

    case "ubicaciones":
        $controlador = new UbicacionController();
        $controlador->gestionar($metodo, $idRecurso, $subrecurso);
        break;

    case "tickets":
        $controlador = new TicketController();
        $controlador->gestionar($metodo, $idRecurso, $subrecurso, $segmentosRuta);
        break;

    case "solicitudes":
        $controlador = new SolicitudController();
        $controlador->gestionar($metodo, $idRecurso, $segmentosRuta);
        break;

    case "prestamos":
        $controlador = new PrestamoController();
        $controlador->gestionar($metodo, $idRecurso, $segmentosRuta);
        break;
    case "historiales":
        $controlador = new HistorialController();
        $controlador->gestionar($metodo, $idRecurso);
        break;
    case "metricas":
        $controlador = new MetricasController();
        $controlador->gestionar($metodo, $idRecurso);
        break;
    default:
        RespuestaJson::error("Recurso no encontrado", 404);
}