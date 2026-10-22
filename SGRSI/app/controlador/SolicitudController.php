<?php

require_once RUTA_MODELO . "/SolicitudDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class SolicitudController
{
    public function gestionar(string $metodo, ?string $idRecurso = null, array $segmentosRuta = []): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }
        if ($idRecurso === "propias") {
            if ($metodo !== "GET") {
                RespuestaJson::error("Método no permitido", 405);
            }

            $segundoSegmento = $segmentosRuta[1] ?? null;

            if ($segundoSegmento === null) {
                $this->listar(null, [], true);
                return;
            }

            if ($segundoSegmento === "filtros") {

                $segmentosFiltros = array_slice($segmentosRuta, 2);
                $this->listar(null, $segmentosFiltros, true);
                return;
            }

            if (count($segmentosRuta) === 2) {
                $this->listar($segundoSegmento, [], true);
                return;
            }
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        if ($idRecurso === "filtros") {
            if ($metodo !== "GET") {
                RespuestaJson::error("Método no permitido", 405);
            }

            $segmentosFiltros = array_slice($segmentosRuta, 1);

            $this->listar(null, $segmentosFiltros);
            return;
        }

        if (count($segmentosRuta) > 1) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        if (
            $metodo === "POST" && $idRecurso !== null
        ) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }


        match ($metodo) {
            "GET" => $this->listar($idRecurso),
            "POST" => $this->alta(),
            "PATCH" => $this->finalizar($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(?string $idSolicitud = null, array $segmentosFiltros = [], bool $soloPropias = false): void
    {
        $rolActual = $_SESSION["rolActual"] ?? "";
        $filtros = $this->leerFiltrosRuta($segmentosFiltros);
        $estado = $filtros["estado"];
        $ciDocente = null;

        if ($rolActual === "tecnico") {
            if ($soloPropias) {
                RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
            }
        } elseif ($rolActual === "docente") {

            if (!$soloPropias) {
                RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
            }
            $ciDocente = $_SESSION["cedula"];

        } else {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        if ($idSolicitud !== null && $idSolicitud !== "") {
            $idSolicitud = trim($idSolicitud);
            if (!ctype_digit($idSolicitud)) {
                RespuestaJson::error("El ID de la solicitud no es válido.", 422);
            }
        } else {
            $idSolicitud = "";
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

    private function leerFiltrosRuta(array $segmentos): array
    {
        $filtros = ["estado" => ""];
        if (empty($segmentos)) {
            return $filtros;
        }

        if (count($segmentos) % 2 !== 0) {
            RespuestaJson::error("Los filtros de la ruta no son válidos.", 422);
        }

        for ($i = 0; $i < count($segmentos); $i += 2) {
            $nombre = rawurldecode($segmentos[$i]);
            $valor = strtolower(trim(rawurldecode($segmentos[$i + 1])));
            if (!array_key_exists($nombre, $filtros)) {
                RespuestaJson::error("El filtro solicitado no es válido.", 422);
            }

            $filtros[$nombre] = $valor;
        }
        $estadosValidos = ["", "pendiente", "finalizada"];

        if (!in_array($filtros["estado"], $estadosValidos, true)) {
            RespuestaJson::error("El estado de la solicitud no es válido.", 422);
        }
        return $filtros;
    }
}