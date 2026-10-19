<?php

require_once RUTA_MODELO . "/UbicacionDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class UbicacionController
{
    public function gestionar( string $metodo, ?string $idRecurso = null, ?string $subrecurso = null): void
{
    if (!isset($_SESSION["cedula"])) {
        RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
    }
    match ($metodo) {
        "GET" => $this->listar(),
        "POST" => $this->alta(),
        "DELETE" => $this->baja($idRecurso, $subrecurso),
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

        $mensaje = $tipo === "laboratorio" ? "Laboratorio registrado correctamente." : "Taller registrado correctamente.";
        RespuestaJson::exito(["mensaje" => $mensaje], 201);
    }

    private function baja( ?string $tipo, ?string $id): void {
    Sesion::verificarRol("administrador");

    if (($_SESSION["rolActual"] ?? "") !== "administrador") {
        RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
    }

    Token::verificarCSRF();

    $tipo = strtolower(trim($tipo ?? ""));
    $id = trim($id ?? "");

    if ($tipo !== "laboratorio" && $tipo !== "taller") {
        RespuestaJson::error("Solo se pueden eliminar laboratorios o talleres.", 422);
    }

    if ($id === "" || !ctype_digit($id) || (int) $id < 1) {
        RespuestaJson::error("La ubicación no es válida.",422);
    }

    $conexion = Conexion::conectar();
    $dao = new UbicacionDAO($conexion);
    $resultado = $dao->eliminarUbicacion($id,$tipo);

    unset($dao, $conexion);
    Conexion::desconectar();

    if (!$resultado) {
        RespuestaJson::error("No se pudo eliminar la ubicación.",400);
    }

    RespuestaJson::exito(["mensaje" => "Ubicación eliminada correctamente."]);
}
}