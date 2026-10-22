<?php

require_once RUTA_MODELO . "/SolicitudDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class SolicitudController
{
    public function gestionar(string $metodo, ?string $idRecurso = null): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listar($idRecurso),
            "POST" => $this->alta(),
            "PATCH" => $this->finalizar($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(?string $idRecurso): void
    {
        $rolActual = $_SESSION["rolActual"] ?? "";
        $estado = strtolower(trim($_GET["estado"] ?? ""));
        $propias = trim($_GET["propias"] ?? "");


        if ($propias !== "" && $propias !== "1") {
            RespuestaJson::error("El filtro de solicitudes propias no es válido.",422);
        }

        $ciDocente = null;

        if ($rolActual === "tecnico") {
            $ciDocente = null;

        } elseif ($rolActual === "docente") {

            if ($propias !== "1") {
                RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
            }
            $ciDocente = $_SESSION["cedula"];

        } else {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        if ($idRecurso !== null && $idRecurso !== "") {
            $idSolicitud = trim($idRecurso);
        } else {
            $idSolicitud = trim($_GET["idSolicitud"] ?? "");
        }

        if ($estado !== "" && $estado !== "pendiente" && $estado !== "finalizada") {
            RespuestaJson::error("El estado de la solicitud no es válido.", 422);
        }
        if ($idSolicitud !== "" && !ctype_digit($idSolicitud)) {
            RespuestaJson::error("El ID de la solicitud no es válido.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new SolicitudDAO($conexion);
        $solicitudes = $dao->listarSolicitudes($estado, $idSolicitud, $ciDocente);

        unset($dao, $conexion);
        Conexion::desconectar();
        RespuestaJson::exito($solicitudes);
    }


    private function alta(): void
    {
        Sesion::verificarRol("docente");

        if (($_SESSION["rolActual"] ?? "") !== "docente") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $asunto = trim($datos["asunto"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");
        $fechaLimite = trim($datos["fechaLimite"] ?? "");
        $horaLimite = trim($datos["horaLimite"] ?? "");

        if ($asunto === "") {
            RespuestaJson::error("Debe ingresar un asunto.", 422);
        }

        if (strlen($asunto) < 10 || strlen($asunto) > 30) {
            RespuestaJson::error("El asunto debe tener entre 10 y 30 caracteres.", 422);
        }

        if ($descripcion === "") {
            RespuestaJson::error("Debe ingresar una descripción.", 422);
        }

        if (strlen($descripcion) < 10 || strlen($descripcion) > 200) {
            RespuestaJson::error("La descripción debe tener entre 10 y 200 caracteres.", 422);
        }

        $fechaHora = DateTime::createFromFormat("Y-m-d H:i", $fechaLimite . " " . $horaLimite);

        if (!$fechaHora) {
            RespuestaJson::error("La fecha u hora ingresadas no son válidas.", 422);
        }

        if ($fechaHora->format("Y-m-d") !== $fechaLimite || $fechaHora->format("H:i") !== $horaLimite) {
            RespuestaJson::error("La fecha u hora ingresadas no son válidas.", 422);
        }

        $ahora = new DateTime();

        if ($fechaHora <= $ahora) {
            RespuestaJson::error("La fecha y hora deben ser posteriores al momento actual.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new SolicitudDAO($conexion);

        $resultado = $dao->registrarSolicitud($_SESSION["cedula"], $asunto, $descripcion, $fechaLimite, $horaLimite);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar la solicitud.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud registrada correctamente."], 201);
    }

    private function finalizar(?string $idSolicitud): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        if ($idSolicitud === null || $idSolicitud === "" || !ctype_digit($idSolicitud)) {
            RespuestaJson::error("El ID de la solicitud no es válido.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new SolicitudDAO($conexion);

        $estadoSolicitud = $dao->obtenerEstadoSolicitud($idSolicitud);

        if ($estadoSolicitud === null) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("La solicitud no existe.", 404);
        }

        if ($estadoSolicitud) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("La solicitud ya se encuentra finalizada.", 409);
        }

        $resultado = $dao->finalizarSolicitud($idSolicitud, $_SESSION["cedula"]);
        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo finalizar la solicitud.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud finalizada correctamente."]);
    }
}