<?php

require_once RUTA_MODELO . "/PrestamoDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class PrestamoController
{
    public function gestionar(string $metodo, ?string $idRecurso = null, array $segmentosRuta = []): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        if ($idRecurso === "disponibles") {
            if ($metodo !== "GET") {
                RespuestaJson::error("Método no permitido", 405);
            }

            if (count($segmentosRuta) !== 1) {
                RespuestaJson::error("La ruta solicitada no es válida.", 404);
            }
            $this->listarDisponibles();
            return;
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

        if ($metodo === "POST" && $idRecurso !== null) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        match ($metodo) {
            "GET" => $this->listar($idRecurso),
            "POST" => $this->alta(),
            "PATCH" => $this->devolver($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(?string $idPrestamo = null, array $segmentosFiltros = []): void
    {

        Sesion::verificarRol("tecnico");
        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
        $filtros = $this->leerFiltrosRuta($segmentosFiltros);
        $estado = $filtros["estado"];

        if ($idPrestamo !== null && $idPrestamo !== "") {

            $idPrestamo = trim($idPrestamo);
            if (!ctype_digit($idPrestamo)) {
                RespuestaJson::error("El ID del préstamo no es válido.", 422);
            }
        } else {
            $idPrestamo = "";
        }

        $conexion = Conexion::conectar();
        $dao = new PrestamoDAO($conexion);

        $prestamos = $dao->listarPrestamos($estado, $idPrestamo);

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

        $fechaHora = DateTime::createFromFormat("Y-m-d H:i", $fechaFin . " " . $horaFin);

        if (!$fechaHora) {
            RespuestaJson::error("La fecha u hora ingresadas no son válidas.", 422);
        }

        if ($fechaHora->format("Y-m-d") !== $fechaFin || $fechaHora->format("H:i") !== $horaFin) {
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

        $resultado = $dao->registrarPrestamo($_SESSION["cedula"], $nombrePrestado, $ciPrestado, $fechaFin, $horaFin, $idEquipo);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar el préstamo.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Préstamo registrado correctamente."], 201);
    }


    private function devolver(?string $idPrestamo): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        Token::verificarCSRF();
        if ($idPrestamo === null || $idPrestamo === "" || !ctype_digit($idPrestamo)) {
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

        $resultado = $dao->devolverPrestamo($idPrestamo, $_SESSION["cedula"]);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar la devolución.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Devolución registrada correctamente."]);
    }

    private function listarDisponibles(): void
    {
        Sesion::verificarRol("tecnico");

        if (($_SESSION["rolActual"] ?? "") !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $conexion = Conexion::conectar();

        $dao = new PrestamoDAO($conexion);
        $equipos = $dao->listarEquiposDisponibles();
        unset($dao, $conexion);

        Conexion::desconectar();
        RespuestaJson::exito($equipos);
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
        $estadosValidos = ["", "prestado", "devuelto"];

        if (!in_array($filtros["estado"], $estadosValidos, true)) {
            RespuestaJson::error("El estado del préstamo no es válido.", 422);
        }

        return $filtros;
    }

}