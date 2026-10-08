<?php
session_start();
session_regenerate_id();

if($_SESSION['cedula']) {
    switch($_SESSION['rolActual']) {
        case 'tecnico':
            header("Location: homeTecnico.php?resultado=Redirigido+automaticamente+a+rol+de+tecnico");
            exit();
        case 'administrador':
            header("Location: homeAdmin.php?resultado=Redirigido+automaticamente+a+rol+de+administrador");
            exit();
        case 'docente':
            header("Location: homeDocente.php?resultado=Redirigido+automaticamente+a+rol+de+docente");
            exit();
    }
}
/**
 * @brief Controla la página de inicio de sesión.
 **/
require_once __DIR__ . "/../../app/vista/index.php";