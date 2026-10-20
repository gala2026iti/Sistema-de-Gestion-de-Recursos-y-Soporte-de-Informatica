<?php

require_once RUTA_MODELO . "/MetricasDAO.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class MetricasController
{
    public function gestionar(
        string $metodo,
        ?string $recurso = null
    ): void {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error(
                "Acceso denegado: sesión no iniciada",
                401
            );
        }

        if (($_SESSION["rolActual"] ?? "") !== "administrador") {
            RespuestaJson::error(
                "Acceso denegado: rol incorrecto",
                403
            );
        }

        if ($metodo !== "GET") {
            RespuestaJson::error(
                "Método no permitido",
                405
            );
        }

        match ($recurso) {
    "resumen" => $this->obtenerResumen(),

    "equipos" => $this->obtenerMetricasEquipos(),

    default => RespuestaJson::error(
        "La métrica solicitada no es válida.",
        404
    )
};
    }


    private function obtenerResumen(): void
    {
        $conexion = Conexion::conectar();

        $dao = new MetricasDAO($conexion);

        $resumen = $dao->obtenerResumen();
        $ticketsPorTipo = $dao->obtenerTicketsPorTipo();

        unset($dao, $conexion);
        Conexion::desconectar();

        RespuestaJson::exito([
            "resumen" => $resumen,
            "ticketsPorTipo" => $ticketsPorTipo
        ]);
    }
    private function obtenerMetricasEquipos(): void
{
    $conexion = Conexion::conectar();

    $dao = new MetricasDAO($conexion);

    $metricas = $dao->obtenerMetricasEquipos();

    unset($dao, $conexion);

    Conexion::desconectar();

    RespuestaJson::exito($metricas);
}
}