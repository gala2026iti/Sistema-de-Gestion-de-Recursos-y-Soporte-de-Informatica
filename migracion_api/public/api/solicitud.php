<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_CONTROLADOR . "/ControladorSolicitud.php";

session_start();

$controlador = new ControladorSolicitud();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);