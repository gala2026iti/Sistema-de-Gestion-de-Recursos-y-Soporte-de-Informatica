<?php

require_once RUTA_MODELO . "/HistorialDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class HistorialController
{
    public function gestionar(
        string $metodo,
        ?string $tipoHistorial = null,
        array $segmentosRuta = []
    ): void {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        if ($metodo !== "GET") {
            RespuestaJson::error("Método no permitido", 405);
        }

        $fecha = "";

        if (count($segmentosRuta) === 1) {
            $fecha = "";
        } elseif (count($segmentosRuta) === 4 && $segmentosRuta[1] === "filtros" && $segmentosRuta[2] === "fecha") {
            $fecha = rawurldecode($segmentosRuta[3]);
        } else {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }
        $this->listar($tipoHistorial, $fecha);
    }


    private function listar(?string $tipoHistorial, string $fecha = ""): void
    {
        $tipoHistorial = strtolower(trim($tipoHistorial ?? ""));
        $tiposValidos = ["usuarios", "equipos", "ubicaciones", "tickets", "solicitudes", "prestamos"];

        if (!in_array($tipoHistorial, $tiposValidos, true)) {
            RespuestaJson::error("El tipo de historial no es válido.", 422);
        }

        $this->verificarAcceso($tipoHistorial);

        $fecha = trim($fecha);

        if ($fecha !== "") {
            $this->validarFecha($fecha);
        }

        $conexion = Conexion::conectar();
        $dao = new HistorialDAO($conexion);

        $historial = match ($tipoHistorial) {
            "usuarios" => $dao->listarUsuarios($fecha),
            "equipos" => $dao->listarEquipos($fecha),
            "ubicaciones" => $dao->listarUbicaciones($fecha),
            "tickets" => $dao->listarTickets($fecha),
            "solicitudes" => $dao->listarSolicitudes($fecha),
            "prestamos" => $dao->listarPrestamos($fecha)
        };

        unset($dao, $conexion);
        Conexion::desconectar();
        RespuestaJson::exito($historial);
    }


    private function verificarAcceso(string $tipoHistorial): void
    {
        $rolActual = $_SESSION["rolActual"] ?? "";
        $historialAdministrador = ["usuarios", "equipos", "ubicaciones"];
        $historialTecnico = ["tickets", "solicitudes", "prestamos"];

        if (in_array($tipoHistorial, $historialAdministrador, true) && $rolActual !== "administrador") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        if (in_array($tipoHistorial, $historialTecnico, true) && $rolActual !== "tecnico") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
    }
    private function validarFecha(string $fecha): void
    {
        $fechaValida = DateTime::createFromFormat("Y-m-d", $fecha);

        if (!$fechaValida || $fechaValida->format("Y-m-d") !== $fecha) {
            RespuestaJson::error("La fecha no es válida.", 422);
        }
    }
}