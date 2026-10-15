<?php

require_once RUTA_MODELO . "/TicketDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class TicketController
{
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "PUT" => $this->modificarAsignacion(),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $orden = strtolower(trim($_GET["orden"] ?? ""));
        $gravedad = strtolower(trim($_GET["gravedad"] ?? ""));
        $tipo = strtolower(trim($_GET["tipo"] ?? ""));
        $estado = strtolower(trim($_GET["estado"] ?? ""));
        $idTicket = trim($_GET["id"] ?? "");
        $idEquipo = trim($_GET["idEquipo"] ?? "");

        if ($idTicket !== "" && !ctype_digit($idTicket)) {
            RespuestaJson::error("El ID del ticket no es válido.", 422);
        }

        if ($idEquipo !== "" && !ctype_digit($idEquipo)) {
            RespuestaJson::error("El ID del equipo no es válido.", 422);
        }

        if ($gravedad !== "" && $gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
            RespuestaJson::error("La gravedad no es válida.", 422);
        }

        if ($tipo !== "" && $tipo !== "hardware" && $tipo !== "software" && $tipo !== "red") {
            RespuestaJson::error("El tipo de ticket no es válido.", 422);
        }

        if ($orden !== "" && $orden !== "reciente" && $orden !== "antiguo") {
            RespuestaJson::error("El orden seleccionado no es válido.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        $tickets = $dao->listarTickets(
            $_SESSION["cedula"],
            $orden,
            $gravedad,
            $tipo,
            $estado,
            $idTicket,
            $idEquipo
        );

        unset($dao, $conexion);
        Conexion::desconectar();

        RespuestaJson::exito($tickets);
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

        $idUbicacion = trim($datos["idUbicacion"] ?? "");
        $tipoUbicacion = strtolower(trim($datos["tipoUbicacion"] ?? ""));
        $tickets = $datos["tickets"] ?? [];

        if ($idUbicacion === "" || !ctype_digit($idUbicacion)) {
            RespuestaJson::error("La ubicación seleccionada no es válida.", 422);
        }

        if ($tipoUbicacion !== "laboratorio" && $tipoUbicacion !== "taller") {
            RespuestaJson::error("El tipo de ubicación no es válido.", 422);
        }

        if (!is_array($tickets) || count($tickets) === 0) {
            RespuestaJson::error("No se recibieron incidencias para registrar.", 422);
        }

        foreach ($tickets as $ticket) {

            if (!is_array($ticket)) {
                RespuestaJson::error("Los datos de una incidencia no son válidos.", 422);
            }

            $idEquipo = trim($ticket["idEquipo"] ?? "");
            $tipo = strtolower(trim($ticket["tipo"] ?? ""));
            $asunto = trim($ticket["asunto"] ?? "");
            $persona = trim($ticket["persona"] ?? "");
            $descripcion = trim($ticket["descripcion"] ?? "");
            $gravedad = strtolower(trim($ticket["gravedad"] ?? ""));

            if ($idEquipo === "" || !ctype_digit($idEquipo)) {
                RespuestaJson::error("Uno de los equipos seleccionados no es válido.", 422);
            }

            if ($tipo !== "hardware" && $tipo !== "software" && $tipo !== "red") {
                RespuestaJson::error("El tipo de una incidencia no es válido.", 422);
            }

            if (strlen($asunto) > 50) {
                RespuestaJson::error("El asunto no puede superar los 50 caracteres.", 422);
            }

            if ($asunto === "") {
                RespuestaJson::error("Debe ingresar un asunto.", 422);
            }

            if (strlen($persona) > 100) {
                RespuestaJson::error("El nombre de la persona no puede superar los 100 caracteres.", 422);
            }

            if (strlen($descripcion) > 250) {
                RespuestaJson::error("La descripción no puede superar los 250 caracteres.", 422);
            }

            if ($descripcion === "") {
                RespuestaJson::error("Debe ingresar una descripción.", 422);
            }

            if ($gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
                RespuestaJson::error("La gravedad de una incidencia no es válida.", 422);
            }

            /*
             * Guardamos los valores ya
             * validados y normalizados.
             */
            $ticket["idEquipo"] = $idEquipo;
            $ticket["tipo"] = $tipo;
            $ticket["asunto"] = $asunto;
            $ticket["persona"] = $persona;
            $ticket["descripcion"] = $descripcion;
            $ticket["gravedad"] = $gravedad;
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        $resultado = $dao->registrarTickets(
            $_SESSION["cedula"],
            $idUbicacion,
            $tipoUbicacion,
            $tickets
        );

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudieron registrar las incidencias.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Incidencias registradas correctamente."], 201);
    }


    private function modificarAsignacion(): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $idTicket = trim($datos["idTicket"] ?? "");
        $accion = strtolower(trim($datos["accion"] ?? ""));

        if ($idTicket === "" || !ctype_digit($idTicket)) {
            RespuestaJson::error("El ID del ticket no es válido.", 422);
        }

        if ($accion !== "asignarse" && $accion !== "desasignarse") {
            RespuestaJson::error("La acción no es válida.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        if ($accion === "asignarse") {
            $resultado = $dao->asignarTecnico($idTicket, $_SESSION["cedula"]);

        } else {
            $resultado = $dao->desasignarTecnico($idTicket, $_SESSION["cedula"]);
        }

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {

            if ($accion === "asignarse") {
                RespuestaJson::error("No se pudo asignar el ticket.", 400);
            }

            RespuestaJson::error("No se pudo desasignar el ticket.", 400);
        }

        if ($accion === "asignarse") {
            RespuestaJson::exito(["mensaje" => "Ticket asignado correctamente."]);

        } else {
            RespuestaJson::exito(["mensaje" => "Ticket desasignado correctamente."]);
        }
    }
}