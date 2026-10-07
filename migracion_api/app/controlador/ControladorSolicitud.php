<?php

/**
 * @file ControladorSolicitud.php
 *
 * @brief Gestiona las peticiones relacionadas con las solicitudes.
 *
 * Recibe las peticiones de la API, valida los datos recibidos
 * y utiliza SolicitudDAO para acceder a la base de datos.
 */

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/SolicitudDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class ControladorSolicitud
{
    /**
     * @brief Gestiona las peticiones recibidas por la API.
     *
     * @param string $metodo Método HTTP recibido.
     *
     * @return void
     */
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "PATCH" => $this->finalizar(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    /**
     * @brief Obtiene las solicitudes registradas.
     *
     * @return void
     */
    private function listar(): void
    {
        if (!($_SESSION["tecnico"] ?? false) && !($_SESSION["docente"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $estado = strtolower(trim($_GET["estado"] ?? ""));

        $conexion = $this->conectar();
        $dao = new SolicitudDAO($conexion);

        RespuestaJson::exito($dao->listarSolicitudes($estado));
    }

    /**
     * @brief Registra una nueva solicitud.
     *
     * @return void
     */
    private function alta(): void
    {
        if (!($_SESSION["docente"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $id = trim($datos["id"] ?? "");
        $asunto = trim($datos["asunto"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");
        $fechaLimite = trim($datos["fechaLimite"] ?? "");
        $horaLimite = trim($datos["horaLimite"] ?? "");

        if ($id === "" || $asunto === "" || $descripcion === "" || $fechaLimite === "" || $horaLimite === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }

        $ciDocente = $_SESSION["cedula"];
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");

        $conexion = $this->conectar();
        $dao = new SolicitudDAO($conexion);

        $resultado = $dao->registrarSolicitud(
            $id,
            $asunto,
            $descripcion,
            $fechaLimite,
            $horaLimite,
            $ciDocente,
            $fecha,
            $hora
        );

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar la solicitud", 400);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud registrada exitosamente"], 201);
    }

    /**
     * @brief Finaliza una solicitud.
     *
     * @return void
     */
    private function finalizar(): void
    {
        if (!($_SESSION["tecnico"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $idSolicitud = trim($datos["idSolicitud"] ?? "");

        if ($idSolicitud === "") {
            RespuestaJson::error("Falta el identificador de la solicitud", 422);
        }

        $conexion = $this->conectar();
        $dao = new SolicitudDAO($conexion);

        $resultado = $dao->cambiarEstadoSolicitud($idSolicitud, true);

        if (!$resultado) {
            RespuestaJson::error("No se pudo finalizar la solicitud", 400);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud finalizada exitosamente"]);
    }

    /**
     * @brief Verifica el token CSRF recibido.
     *
     * @return void
     */
    private function verificarCsrf(): void
    {
        $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";

        if (!isset($_SESSION["csrfToken"]) || !hash_equals($_SESSION["csrfToken"], $token)) {
            RespuestaJson::error("Solicitud rechazada", 403);
        }
    }

    /**
     * @brief Establece una conexión con la base de datos.
     *
     * @return PDO Conexión establecida.
     */
    private function conectar(): PDO
    {
        $conector = new ConectorPDO(
            $_ENV["DB_HOST"] . ":" . $_ENV["DB_PUERTO"],
            $_ENV["DB_USUARIO"],
            $_ENV["DB_CLAVE"],
            $_ENV["DB_NOMBRE"]
        );

        $conexion = $conector->establecerConexion();

        if ($conexion === null) {
            RespuestaJson::error("Error de conexión con la base de datos", 500);
        }

        return $conexion;
    }
}