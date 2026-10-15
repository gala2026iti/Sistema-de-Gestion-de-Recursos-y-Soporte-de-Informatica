<?php

require_once RUTA_MODELO . "/UbicacionDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class UbicacionController
{
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "DELETE" => $this->baja(),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(): void
    {
        $conexion = Conexion::conectar();
        $dao = new UbicacionDAO($conexion);

        $ubicaciones = $dao->listarUbicaciones();

        unset($dao, $conexion);
        Conexion::desconectar();

        RespuestaJson::exito($ubicaciones);
    }


    private function alta(): void
    {
        Sesion::verificarRol("administrador");

        if (($_SESSION["rolActual"] ?? "") !== "administrador") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $tipo = strtolower(trim($datos["tipo"] ?? ""));

        $tiposValidos = ["laboratorio", "taller"];

        if (!in_array($tipo, $tiposValidos, true)) {
            RespuestaJson::error("El tipo de ubicación no es válido.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new UbicacionDAO($conexion);

        $resultado = $dao->registrarUbicacion($tipo);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar la ubicación.", 400);
        }

        $mensaje = $tipo === "laboratorio"
            ? "Laboratorio registrado correctamente."
            : "Taller registrado correctamente.";

        RespuestaJson::exito(["mensaje" => $mensaje], 201);
    }


    private function baja(): void
    {
        Sesion::verificarRol("administrador");

        if (($_SESSION["rolActual"] ?? "") !== "administrador") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $id = trim($datos["id"] ?? "");
        $tipo = strtolower(trim($datos["tipo"] ?? ""));

        if (!ctype_digit($id) || (int) $id < 1) {
            RespuestaJson::error("La ubicación no es válida.", 422);
        }

        if ($tipo !== "laboratorio" && $tipo !== "taller") {
            RespuestaJson::error("Solo se pueden eliminar laboratorios o talleres.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new UbicacionDAO($conexion);

        $resultado = $dao->eliminarUbicacion($id, $tipo);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo eliminar la ubicación.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ubicación eliminada correctamente."]);
    }
}