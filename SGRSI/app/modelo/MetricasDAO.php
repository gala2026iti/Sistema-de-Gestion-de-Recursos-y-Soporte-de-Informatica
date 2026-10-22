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
            t.ticketsAbiertos,
            t.ticketsCerrados,
            e.equiposActivos,
            e.equiposInactivos,
            s.solicitudesPendientes,
            p.prestamosActivos

        FROM (
            SELECT
                COUNT(
                    CASE
                        WHEN estado <> 'resuelto' THEN 1
                    END
                ) AS ticketsAbiertos,

                COUNT(
                    CASE
                        WHEN estado = 'resuelto' THEN 1
                    END
                ) AS ticketsCerrados

            FROM TICKET
        ) AS t

        CROSS JOIN (
            SELECT
                COUNT(
                    CASE
                        WHEN activo = TRUE THEN 1
                    END
                ) AS equiposActivos,

                COUNT(
                    CASE
                        WHEN activo = FALSE THEN 1
                    END
                ) AS equiposInactivos

            FROM EQUIPO
        ) AS e

        CROSS JOIN (
            SELECT
                COUNT(*) AS solicitudesPendientes
            FROM SOLICITUD
            WHERE finalizada = FALSE
        ) AS s

        CROSS JOIN (
            SELECT
                COUNT(*) AS prestamosActivos
            FROM PRESTAMO
            WHERE devuelto = FALSE
        ) AS p
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
        equipos.totalEquipos,
        equipos.equiposFallados,
        tickets.ultimoIncidente,
        tickets.incidenciasGraves

    FROM (
        SELECT
            COUNT(DISTINCT equipo.id)
                AS totalEquipos,

            COUNT(
                DISTINCT CASE
                    WHEN ticket.estado <> 'resuelto'
                    THEN equipo.id
                END
            ) AS equiposFallados

        FROM EQUIPO AS equipo

        LEFT JOIN equipo_ubicacion_genera_ticket AS relacion
            ON relacion.idEquipo = equipo.id

        LEFT JOIN TICKET AS ticket
            ON ticket.id = relacion.idTicket

    ) AS equipos

    CROSS JOIN (
        SELECT
            MAX(
                TIMESTAMP(
                    fechaCreacion,
                    horaCreacion
                )
            ) AS ultimoIncidente,

            COUNT(
                CASE
                    WHEN gravedad = 'grave' THEN 1
                END ) AS incidenciasGraves
        FROM TICKET ) AS tickets";

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