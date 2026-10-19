<?php

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Token.php";
require_once RUTA_NUCLEO . "/Sesion.php";

class LoginController
{
    public function autenticar(string $metodo): void
    {
        if ($metodo !== "POST") {
            RespuestaJson::error("Método no permitido", 405);
        }

        $datos = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $cedula = trim($datos["cedula"] ?? "");
        $clave = $datos["clave"] ?? "";

        if ($cedula === "" || $clave === "") {
            RespuestaJson::error("Cédula y contraseña son obligatorias", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new UsuarioDAO($conexion);
        $usuario = $dao->buscarUsuario($cedula);
        unset($dao, $conexion);
        Conexion::desconectar();

        if ($usuario === null || !$usuario->estaActivo() || !password_verify($clave, $usuario->getClaveHash())) {
            RespuestaJson::error("La cédula o la contraseña son incorrectas, o el usuario está inactivo.", 401);
        }

        if (!$usuario->esAdministrador() && !$usuario->esTecnico() && !$usuario->esDocente()) {
            RespuestaJson::error("Acceso denegado: el usuario no tiene un rol asignado.", 403);
        }

        Sesion::iniciar($usuario);
        Token::generarTokenCSRF();
        RespuestaJson::exito(["mensaje" => "Sesión iniciada correctamente", "csrfToken" => $_SESSION["csrfToken"], "rolActual" => $_SESSION["rolActual"]]);
    }

    public function gestionarSesion(string $metodo): void
    {
        match ($metodo) {
            "GET" => $this->obtenerSesion(),
            "PATCH" => $this->cambiarRol(),
            "DELETE" => $this->cerrarSesion(),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function obtenerSesion(): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error(
                "Acceso denegado: sesión no iniciada",
                401
            );
        }

        RespuestaJson::exito([
            "cedula" => $_SESSION["cedula"],
            "nombre" => $_SESSION["nombre"],
            "rolActual" => $_SESSION["rolActual"],
            "administrador" => $_SESSION["administrador"],
            "tecnico" => $_SESSION["tecnico"],
            "docente" => $_SESSION["docente"]
        ]);
    }


    private function cambiarRol(): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        Token::verificarCSRF();
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $rol = strtolower(trim($datos["rol"] ?? ""));

        if ($rol !== "administrador" && $rol !== "tecnico" && $rol !== "docente") {
            RespuestaJson::error("El rol seleccionado no es válido.", 422);
        }

        if (!($_SESSION[$rol] ?? false)) {
            RespuestaJson::error("El usuario no posee el rol seleccionado.", 403);
        }

        if (($_SESSION["rolActual"] ?? "") === $rol) {
            RespuestaJson::error("El rol seleccionado ya se encuentra activo.", 409);
        }

        $_SESSION["rolActual"] = $rol;

        RespuestaJson::exito(["mensaje" => "Rol cambiado correctamente.", "rolActual" => $rol]);
    }


    private function cerrarSesion(): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        Token::verificarCSRF();
        Sesion::cerrar();
        RespuestaJson::exito(["mensaje" => "Sesión cerrada correctamente."]);
    }
}