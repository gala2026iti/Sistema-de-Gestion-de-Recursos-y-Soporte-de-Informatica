/*
    DQL - METRICAS
    Fuente: SGRSI/app/modelo/MetricasDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: obtenerResumen */
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
                ) AS prestamosActivos;

/* Consulta 2: obtenerTicketsPorTipo */
SELECT
                tipo,
                COUNT(*) AS cantidad
            FROM TICKET
            GROUP BY tipo
            ORDER BY cantidad DESC, tipo ASC;

/* Consulta 3: obtenerMetricasEquipos (1) */
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
            ) AS incidenciasGraves;

/* Consulta 4: obtenerMetricasEquipos (2) */
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

        LIMIT 10;
