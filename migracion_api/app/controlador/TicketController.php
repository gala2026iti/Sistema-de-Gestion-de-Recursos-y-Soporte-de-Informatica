<?php

/**
 * @file ControladorTicket.php
 *
 * @brief Gestiona las peticiones relacionadas con los tickets.
 *
 * Recibe las peticiones de la API, valida los datos recibidos
 * y utiliza TicketDAO para acceder a la base de datos.
 */

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class ControladorTicket
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

        if (!($_SESSION["tecnico"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        match ($metodo) {
            "GET" => $this->consultar(),
            "POST" => $this->alta(),
            "PATCH" => $this->modificar(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    /**
     * @brief Gestiona la consulta de tickets.
     *
     * Si se recibe un identificador busca un ticket específico.
     * En caso contrario devuelve el listado de tickets.
     *
     * @return void
     */
    private function consultar(): void
    {
        $idTicket = trim($_GET["idTicket"] ?? "");

        if ($idTicket !== "") {
            $this->buscar($idTicket);
            return;
        }

        $this->listar();
    }

    /**
     * @brief Obtiene los tickets registrados.
     *
     * @return void
     */
    private function listar(): void
    {
        $tiempo = strtolower(trim($_GET["tiempo"] ?? ""));
        $gravedad = strtolower(trim($_GET["gravedad"] ?? ""));
        $clasificacion = strtolower(trim($_GET["clasificacion"] ?? ""));
        $estado = strtolower(trim($_GET["estado"] ?? ""));

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);

        RespuestaJson::exito(
            $dao->listarTickets($tiempo, $gravedad, $clasificacion, $estado)
        );
    }

    /**
     * @brief Busca un ticket por su identificador.
     *
     * @param string $idTicket Identificador del ticket.
     *
     * @return void
     */
    private function buscar(string $idTicket): void
    {
        if (!is_numeric($idTicket)) {
            RespuestaJson::error("El ID del ticket no es válido", 422);
        }

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);
        $ticket = $dao->buscarTicket($idTicket);

        if ($ticket === null) {
            RespuestaJson::error("Ticket no encontrado", 404);
        }

        RespuestaJson::exito($ticket);
    }

    /**
     * @brief Registra un nuevo ticket.
     *
     * @return void
     */
    private function alta(): void
    {
        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $idTicket = trim($datos["idTicket"] ?? "");
        $tipo = strtolower(trim($datos["tipo"] ?? ""));
        $asunto = trim($datos["asunto"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");
        $gravedad = strtolower(trim($datos["gravedad"] ?? ""));
        $estado = strtolower(trim($datos["estado"] ?? ""));
        $fechaCreacion = trim($datos["fechaCreacion"] ?? "");
        $horaCreacion = trim($datos["horaCreacion"] ?? "");

        if (
            $idTicket === "" ||
            $tipo === "" ||
            $asunto === "" ||
            $descripcion === "" ||
            $gravedad === "" ||
            $estado === "" ||
            $fechaCreacion === "" ||
            $horaCreacion === ""
        ) {
            RespuestaJson::error("Existen campos vacíos", 422);
        }

        if (!is_numeric($idTicket)) {
            RespuestaJson::error("El ID del ticket debe ser un número entero", 422);
        }

        if ($tipo !== "hardware" && $tipo !== "software" && $tipo !== "red") {
            RespuestaJson::error("Tipo de ticket no válido", 422);
        }

        if (strlen($asunto) < 10 || strlen($asunto) > 50) {
            RespuestaJson::error("El asunto debe contener entre 10 y 50 caracteres", 422);
        }

        if (strlen($descripcion) < 10 || strlen($descripcion) > 250) {
            RespuestaJson::error("La descripción debe contener entre 10 y 250 caracteres", 422);
        }

        if ($gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
            RespuestaJson::error("Gravedad no válida", 422);
        }

        if ($estado !== "pendiente") {
            RespuestaJson::error("El ticket debe registrarse como pendiente", 422);
        }

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);

        $resultado = $dao->registrarTicket(
            $idTicket,
            $tipo,
            $asunto,
            $descripcion,
            $gravedad,
            $estado,
            $fechaCreacion,
            $horaCreacion
        );

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar el ticket", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ticket registrado correctamente"], 201);
    }

    /**
     * @brief Gestiona las modificaciones de un ticket.
     *
     * @return void
     */
    private function modificar(): void
    {
        $this->verificarCsrf();

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $accion = strtolower(trim($datos["accion"] ?? ""));

        match ($accion) {
            "estado" => $this->cambiarEstado($datos),
            "gravedad" => $this->cambiarGravedad($datos),
            "asignarse" => $this->asignarse($datos),
            "desasignarse" => $this->desasignarse($datos),
            default => RespuestaJson::error("Acción no válida", 422),
        };
    }

    /**
     * @brief Cambia el estado de un ticket.
     *
     * @param array $datos Datos recibidos mediante la API.
     *
     * @return void
     */
    private function cambiarEstado(array $datos): void
    {
        $idTicket = trim($datos["idTicket"] ?? "");
        $estado = strtolower(trim($datos["estado"] ?? ""));

        if ($idTicket === "" || $estado === "") {
            RespuestaJson::error("Faltan datos para modificar el estado", 422);
        }

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);
        $resultado = $dao->cambiarEstadoTicket($idTicket, $estado);

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar el estado del ticket", 400);
        }

        RespuestaJson::exito(["mensaje" => "Estado del ticket modificado correctamente"]);
    }

    /**
     * @brief Cambia la gravedad de un ticket.
     *
     * @param array $datos Datos recibidos mediante la API.
     *
     * @return void
     */
    private function cambiarGravedad(array $datos): void
    {
        $idTicket = trim($datos["idTicket"] ?? "");
        $gravedad = strtolower(trim($datos["gravedad"] ?? ""));

        if ($idTicket === "" || $gravedad === "") {
            RespuestaJson::error("Faltan datos para modificar la gravedad", 422);
        }

        if ($gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
            RespuestaJson::error("Gravedad no válida", 422);
        }

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);
        $resultado = $dao->cambiarGravedadTicket($idTicket, $gravedad);

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar la gravedad del ticket", 400);
        }

        RespuestaJson::exito(["mensaje" => "Gravedad del ticket modificada correctamente"]);
    }

    /**
     * @brief Asigna el técnico actual a un ticket.
     *
     * @param array $datos Datos recibidos mediante la API.
     *
     * @return void
     */
    private function asignarse(array $datos): void
    {
        $idTicket = trim($datos["idTicket"] ?? "");

        if ($idTicket === "") {
            RespuestaJson::error("Falta el ID del ticket", 422);
        }

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);

        $resultado = $dao->asignarme(
            $idTicket,
            $_SESSION["cedula"]
        );

        if (!$resultado) {
            RespuestaJson::error("No se pudo asignar el ticket", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ticket asignado correctamente"]);
    }

    /**
     * @brief Desasigna el técnico actual de un ticket.
     *
     * @param array $datos Datos recibidos mediante la API.
     *
     * @return void
     */
    private function desasignarse(array $datos): void
    {
        $idTicket = trim($datos["idTicket"] ?? "");

        if ($idTicket === "") {
            RespuestaJson::error("Falta el ID del ticket", 422);
        }

        $conexion = $this->conectar();
        $dao = new AccesoDatosTicket($conexion);

        $resultado = $dao->desasignarme(
            $idTicket,
            $_SESSION["cedula"]
        );

        if (!$resultado) {
            RespuestaJson::error("No se pudo desasignar el ticket", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ticket desasignado correctamente"]);
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
     * @brief Establece la conexión con la base de datos.
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