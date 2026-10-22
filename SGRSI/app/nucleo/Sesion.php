<?php

require_once RUTA_MODELO . "/Usuario.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class Sesion
{
    public static function iniciar(Usuario $usuario): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION["cedula"] = $usuario->getCedula();
        $_SESSION["nombre"] = $usuario->getNombre();

        $_SESSION["administrador"] = $usuario->esAdministrador();
        $_SESSION["tecnico"] = $usuario->esTecnico();
        $_SESSION["docente"] = $usuario->esDocente();

        $_SESSION["rolActual"] = $_SESSION["administrador"] ? "administrador" : ($_SESSION["tecnico"] ? "tecnico" : "docente");
    }

    public static function cerrar(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                time() - 42000,
                $parametros["path"],
                $parametros["domain"],
                $parametros["secure"],
                $parametros["httponly"]
            );
        }

        session_destroy();
    }

    public static function verificarRol(string $rol): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        if (!($_SESSION[$rol] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
    }
}