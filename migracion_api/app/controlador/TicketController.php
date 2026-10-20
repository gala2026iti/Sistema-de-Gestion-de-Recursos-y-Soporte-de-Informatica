<?php

require_once RUTA_MODELO . "/TicketDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class TicketController
{
    public function gestionar(string $metodo, ?string $idRecurso = null, ?string $subrecurso = null): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        if ($subrecurso !== null) {

            if ($subrecurso === "comentarios") {

                if ($metodo !== "POST") {
                    RespuestaJson::error("Método no permitido", 405);
                }
                $this->registrarComentario($idRecurso);
                return;
            }

            RespuestaJson::error("Subrecurso no encontrado", 404);
        }

        match ($metodo) {
            "GET" => $this->listar($idRecurso),
            "POST" => $this->alta(),
            "PUT" => $this->modificarAsignacion($idRecurso),
            "PATCH" => $this->modificarTicket($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }

    private function listar(?string $idRecurso): void
    {
        $rolActual = $_SESSION["rolActual"] ?? "";

        $asignados = trim($_GET["asignados"] ?? "");
        $propios = trim($_GET["propios"] ?? "");

        $orden = strtolower(trim($_GET["orden"] ?? ""));
        $gravedad = strtolower(trim($_GET["gravedad"] ?? ""));
        $tipo = strtolower(trim($_GET["tipo"] ?? ""));
        $estado = strtolower(trim($_GET["estado"] ?? ""));
        $idEquipo = trim($_GET["idEquipo"] ?? "");


        if ($rolActual !== "tecnico" && $rolActual !== "administrador" && $rolActual !== "docente") {
            RespuestaJson::error("Acceso denegado: rol incorrecto",403 );
        }


        if ($propios !== "" && $propios !== "1") {
            RespuestaJson::error("El filtro de tickets propios no es válido.", 422);
        }


        if ($asignados !== "" && $asignados !== "1") {
            RespuestaJson::error("El filtro de tickets asignados no es válido.", 422);
        }

        if ($asignados === "1" && $rolActual !== "tecnico") {
            RespuestaJson::error("El filtro de tickets asignados no está disponible para este rol.", 403);
        }

        $ciDocente = null;

        if ($rolActual === "docente") {

            if ($propios !== "1") {
                RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
            }
            $ciDocente = $_SESSION["cedula"];
        }

        if ($propios === "1" && $rolActual !== "docente") {
            RespuestaJson::error("El filtro de tickets propios no está disponible para este rol.", 422);
        }

        if ($idRecurso !== null && $idRecurso !== "") {
            $idTicket = trim($idRecurso);

        } else {
            $idTicket = trim($_GET["idTicket"] ?? "");
        }

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


        if ($estado !== "" && $estado !== "pendiente" && $estado !== "en proceso" && $estado !== "resuelto") {
            RespuestaJson::error("El estado seleccionado no es válido.", 422);
        }

        $incluirResueltos = $rolActual === "administrador" || $rolActual === "docente";

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);
        $tickets = $dao->listarTickets($_SESSION["cedula"], $orden, $gravedad, $tipo, $estado, $idTicket, $idEquipo, $asignados === "1", $ciDocente, $incluirResueltos);


        if ($idRecurso !== null && $idRecurso !== "" && count($tickets) > 0) {
            $tickets[0]["comentarios"] = $dao->listarComentarios($idTicket);
        }

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

            $ticket["idEquipo"] = $idEquipo;
            $ticket["tipo"] = $tipo;
            $ticket["asunto"] = $asunto;
            $ticket["persona"] = $persona;
            $ticket["descripcion"] = $descripcion;
            $ticket["gravedad"] = $gravedad;
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        $resultado = $dao->registrarTickets($_SESSION["cedula"], $idUbicacion, $tipoUbicacion, $tickets);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudieron registrar las incidencias.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Incidencias registradas correctamente."], 201);
    }

    private function modificarAsignacion(?string $idTicket): void
    {
        Sesion::verificarRol("tecnico");
        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        $idTicket = trim($idTicket ?? "");

        if ($idTicket === "" || !ctype_digit($idTicket)) {
            RespuestaJson::error("El ID del ticket no es válido.", 422);
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $accion = strtolower(trim($datos["accion"] ?? ""));

        if ($accion !== "asignarse" && $accion !== "desasignarse") {
            RespuestaJson::error("La acción no es válida.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        $estadoTicket = $dao->obtenerEstadoTicket($idTicket);

        if ($estadoTicket === null) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El ticket no existe.", 404);
        }

        if ($estadoTicket === "resuelto") {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El ticket está cerrado y no puede modificarse su asignación.", 409);
        }

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

    private function modificarTicket(?string $idTicket): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        $idTicket = trim($idTicket ?? "");

        if ($idTicket === "" || !ctype_digit($idTicket)) {
            RespuestaJson::error("El ID del ticket no es válido.", 422);
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $gravedad = strtolower(trim($datos["gravedad"] ?? ""));
        $estado = strtolower(trim($datos["estado"] ?? ""));
        $justificacion = trim($datos["justificacion"] ?? "");

        if ($gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
            RespuestaJson::error("La gravedad no es válida.", 422);
        }

        if ($estado !== "pendiente" && $estado !== "en proceso" && $estado !== "resuelto") {
            RespuestaJson::error("El estado no es válido.", 422);
        }

        if ($estado === "resuelto" && $justificacion === "") {
            RespuestaJson::error("Debe ingresar una justificación para resolver el ticket.", 422);
        }

        if (strlen($justificacion) > 255) {
            RespuestaJson::error("La justificación no puede superar los 255 caracteres.", 422);
        }

        if ($estado !== "resuelto") {
            $justificacion = null;
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        $estadoActual = $dao->obtenerEstadoTicket($idTicket);

        if ($estadoActual === null) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El ticket no existe.", 404);
        }

        if ($estadoActual === "resuelto") {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El ticket ya se encuentra cerrado.", 409);
        }

        $resultado = $dao->modificarTicket($idTicket, $gravedad, $estado, $justificacion);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar el ticket.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ticket modificado correctamente."]);
    }

    private function registrarComentario(?string $idTicket): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();

        if ($idTicket === null || $idTicket === "" || !ctype_digit($idTicket)) {
            RespuestaJson::error("El ID del ticket no es válido.", 422);
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $texto = trim($datos["texto"] ?? "");

        if ($texto === "") {
            RespuestaJson::error("Debe ingresar un comentario.", 422);
        }

        if (strlen($texto) > 500) {
            RespuestaJson::error("El comentario no puede superar los 500 caracteres.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new TicketDAO($conexion);

        $estadoTicket = $dao->obtenerEstadoTicket($idTicket);

        if ($estadoTicket === null) {
            unset($dao, $conexion);
            Conexion::desconectar();
            RespuestaJson::error("El ticket no existe.", 404);
        }

        if ($estadoTicket === "resuelto") {
            unset($dao, $conexion);
            Conexion::desconectar();
            RespuestaJson::error("El ticket está cerrado y no admite nuevos comentarios.", 409);
        }

        $resultado = $dao->registrarComentario($idTicket, $_SESSION["cedula"], $texto);
        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar el comentario.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Comentario registrado correctamente."], 201);
    }
}