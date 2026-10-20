<?php

class MetricasDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    public function obtenerResumen(): array
    {
        $sql = "
            SELECT

                (
                    SELECT COUNT(*)
                    FROM TICKET
                    WHERE estado <> 'resuelto'
                ) AS ticketsAbiertos,

                (
                    SELECT COUNT(*)
                    FROM TICKET
                    WHERE estado = 'resuelto'
                ) AS ticketsCerrados,

                (
                    SELECT COUNT(*)
                    FROM EQUIPO
                    WHERE activo = TRUE
                ) AS equiposActivos,

                (
                    SELECT COUNT(*)
                    FROM EQUIPO
                    WHERE activo = FALSE
                ) AS equiposInactivos,

                (
                    SELECT COUNT(*)
                    FROM SOLICITUD
                    WHERE finalizada = FALSE
                ) AS solicitudesPendientes,

                (
                    SELECT COUNT(*)
                    FROM PRESTAMO
                    WHERE devuelto = FALSE
                ) AS prestamosActivos
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        $consulta->closeCursor();

        return [
            "ticketsAbiertos" => (int) $resultado["ticketsAbiertos"],
            "ticketsCerrados" => (int) $resultado["ticketsCerrados"],
            "equiposActivos" => (int) $resultado["equiposActivos"],
            "equiposInactivos" => (int) $resultado["equiposInactivos"],
            "solicitudesPendientes" => (int) $resultado["solicitudesPendientes"],
            "prestamosActivos" => (int) $resultado["prestamosActivos"]
        ];
    }


    public function obtenerTicketsPorTipo(): array
    {
        $sql = "
            SELECT
                tipo,
                COUNT(*) AS cantidad
            FROM TICKET
            GROUP BY tipo
            ORDER BY cantidad DESC, tipo ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta->closeCursor();

        foreach ($resultado as &$fila) {
            $fila["cantidad"] = (int) $fila["cantidad"];
        }

        unset($fila);

        return $resultado;
    }

    public function obtenerMetricasEquipos(): array
{
    /*
     * Resumen general de las métricas.
     */
    $sql = "
        SELECT

            (
                SELECT COUNT(*)
                FROM EQUIPO
            ) AS totalEquipos,

            (
                SELECT COUNT(DISTINCT relacion.idEquipo)

                FROM equipo_ubicacion_genera_ticket AS relacion

                INNER JOIN TICKET AS ticket
                    ON ticket.id = relacion.idTicket

                WHERE ticket.estado <> 'resuelto'
            ) AS equiposFallados,

            (
                SELECT MAX(
                    TIMESTAMP(
                        fechaCreacion,
                        horaCreacion
                    )
                )
                FROM TICKET
            ) AS ultimoIncidente,

            (
                SELECT COUNT(*)
                FROM TICKET
                WHERE gravedad = 'grave'
            ) AS incidenciasGraves
    ";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();

    $resumen = $consulta->fetch(PDO::FETCH_ASSOC);

    $consulta->closeCursor();


    /*
     * Ranking de equipos según la cantidad
     * total de tickets registrados.
     */
    $sql = "
        SELECT
            equipo.id AS idEquipo,

            ubicacion.idUbicacion,
            ubicacion.tipoUbicacion,

            COUNT(
                DISTINCT relacion.idTicket
            ) AS fallasRegistradas

        FROM EQUIPO AS equipo

        INNER JOIN equipo_ubicacion_genera_ticket AS relacion
            ON relacion.idEquipo = equipo.id

        LEFT JOIN equipo_reside_ubicacion AS ubicacion
            ON ubicacion.idEquipo = equipo.id

        GROUP BY
            equipo.id,
            ubicacion.idUbicacion,
            ubicacion.tipoUbicacion

        ORDER BY
            fallasRegistradas DESC,
            equipo.id ASC

        LIMIT 10
    ";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();

    $ranking = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $consulta->closeCursor();

    foreach ($ranking as &$equipo) {
        $equipo["idEquipo"] =
            (int) $equipo["idEquipo"];

        $equipo["fallasRegistradas"] =
            (int) $equipo["fallasRegistradas"];

        if ($equipo["idUbicacion"] !== null) {
            $equipo["idUbicacion"] =
                (int) $equipo["idUbicacion"];
        }
    }

    unset($equipo);

    return [
        "totalEquipos" =>
            (int) $resumen["totalEquipos"],

        "equiposFallados" =>
            (int) $resumen["equiposFallados"],

        "ultimoIncidente" =>
            $resumen["ultimoIncidente"],

        "incidenciasGraves" =>
            (int) $resumen["incidenciasGraves"],

        "rankingEquipos" =>
            $ranking
    ];
}
}