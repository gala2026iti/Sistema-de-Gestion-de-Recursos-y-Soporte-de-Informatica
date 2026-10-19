<?php

require_once RUTA_MODELO . "/PrestamoDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class PrestamoController
{
    public function gestionar( string $metodo, ?string $idRecurso = null): void {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listar($idRecurso),
            "POST" => $this->alta(),
            "PATCH" => $this->devolver($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(?string $idRecurso): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $estado = strtolower(trim($_GET["estado"] ?? ""));
        $idPrestamo = trim($idRecurso ?? "");
        $disponibles = trim($_GET["disponibles"] ?? "");

        if ($estado !== "" && $estado !== "prestado" && $estado !== "devuelto") {
            RespuestaJson::error("El estado del préstamo no es válido.", 422);
        }

        if ($idPrestamo !== "" && !ctype_digit($idPrestamo)) {
            RespuestaJson::error("El ID del préstamo no es válido.", 422);
        }

        if ($disponibles !== "" && $disponibles !== "1") {
            RespuestaJson::error("El filtro de equipos disponibles no es válido.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new PrestamoDAO($conexion);

        if ($disponibles === "1") {
            $equipos = $dao->listarEquiposDisponibles();

            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::exito($equipos);
        }

        $prestamos = $dao->listarPrestamos(
            $estado,
            $idPrestamo
        );

        unset($dao, $conexion);
        Conexion::desconectar();

        RespuestaJson::exito($prestamos);
    }


    private function alta(): void
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

        $nombrePrestado = trim($datos["nombrePrestado"] ?? "");
        $ciPrestado = trim($datos["ciPrestado"] ?? "");
        $fechaFin = trim($datos["fechaFin"] ?? "");
        $horaFin = trim($datos["horaFin"] ?? "");
        $idEquipo = trim($datos["idEquipo"] ?? "");

        if ($nombrePrestado === "") {
            RespuestaJson::error("Debe ingresar el nombre del solicitante.", 422);
        }

        if (strlen($nombrePrestado) > 100) {
            RespuestaJson::error("El nombre no puede superar los 100 caracteres.", 422);
        }

        if (strlen($ciPrestado) !== 8 || !ctype_digit($ciPrestado)) {
            RespuestaJson::error("La cédula debe contener 8 dígitos.", 422);
        }

        if ($idEquipo === "" || !ctype_digit($idEquipo)) {
            RespuestaJson::error("El equipo seleccionado no es válido.", 422);
        }

        $fechaHora = DateTime::createFromFormat(
            "Y-m-d H:i",
            $fechaFin . " " . $horaFin
        );

        if (!$fechaHora) {
            RespuestaJson::error("La fecha u hora ingresadas no son válidas.", 422);
        }

        if (
            $fechaHora->format("Y-m-d") !== $fechaFin ||
            $fechaHora->format("H:i") !== $horaFin
        ) {
            RespuestaJson::error("La fecha u hora ingresadas no son válidas.", 422);
        }

        $ahora = new DateTime();

        if ($fechaHora <= $ahora) {
            RespuestaJson::error("La fecha y hora de devolución deben ser posteriores al momento actual.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new PrestamoDAO($conexion);

        if (!$dao->equipoDisponible($idEquipo)) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El equipo seleccionado no está disponible para préstamo.", 409);
        }

        $resultado = $dao->registrarPrestamo(
            $_SESSION["cedula"],
            $nombrePrestado,
            $ciPrestado,
            $fechaFin,
            $horaFin,
            $idEquipo
        );

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar el préstamo.", 400);
        }

        RespuestaJson::exito(
            ["mensaje" => "Préstamo registrado correctamente."],
            201
        );
    }


    private function devolver(?string $idPrestamo): void
    {
    Sesion::verificarRol("tecnico");

    if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
        RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
    }

    Token::verificarCSRF();
    if ( $idPrestamo === null || $idPrestamo === "" || !ctype_digit($idPrestamo)) {
        RespuestaJson::error("El ID del préstamo no es válido.", 422);
    }

        $conexion = Conexion::conectar();
        $dao = new PrestamoDAO($conexion);

        $estadoPrestamo = $dao->obtenerEstadoPrestamo($idPrestamo);

        if ($estadoPrestamo === null) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El préstamo no existe.", 404);
        }

        if ($estadoPrestamo) {
            unset($dao, $conexion);
            Conexion::desconectar();

            RespuestaJson::error("El préstamo ya fue devuelto.", 409);
        }

        $resultado = $dao->devolverPrestamo(
            $idPrestamo,
            $_SESSION["cedula"]
        );

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar la devolución.", 400);
        }

        RespuestaJson::exito(
            ["mensaje" => "Devolución registrada correctamente."]
        );
    }
}