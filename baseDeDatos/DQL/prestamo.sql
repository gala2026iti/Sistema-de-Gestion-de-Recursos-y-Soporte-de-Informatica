/*
    DQL - PRESTAMO
    Fuente: SGRSI/app/modelo/PrestamoDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: listarPrestamos */
SELECT
                p.id,
                p.nombrePrestado,
                p.ciPrestado,
                p.fechaFin,
                p.horaFin,
                p.devuelto,

                pce.idEquipo,

                ttp.ciTecnico,
                u.nombre AS nombreTecnico,
                ttp.fecha AS fechaPrestamo,
                ttp.hora AS horaPrestamo

            FROM PRESTAMO AS p

            INNER JOIN prestamo_corresponde_equipo AS pce
                ON pce.idPrestamo = p.id

            LEFT JOIN tecnico_tramita_prestamo AS ttp
                ON ttp.id = (
                    SELECT MIN(ttp2.id)
                    FROM tecnico_tramita_prestamo AS ttp2
                    WHERE ttp2.idPrestamo = p.id
                    AND ttp2.tipoInteraccion = 'prestamo'
                )

            LEFT JOIN USUARIO AS u
                ON u.ci = ttp.ciTecnico;

/* Filtros opcionales utilizados por este método:
   - p.devuelto = 0
   - p.devuelto = 1
   - p.id = :idPrestamo
*/

/* Consulta 2: listarEquiposDisponibles */
SELECT
                e.id AS idEquipo

            FROM EQUIPO AS e

            INNER JOIN equipo_reside_ubicacion AS eru
                ON eru.idEquipo = e.id

            WHERE e.activo = 1

            AND eru.idUbicacion = 0
            AND eru.tipoUbicacion = 'prestamo'

            AND NOT EXISTS (
                SELECT 1

                FROM prestamo_corresponde_equipo AS pce

                INNER JOIN PRESTAMO AS p
                    ON p.id = pce.idPrestamo

                WHERE pce.idEquipo = e.id
                AND p.devuelto = 0
            )

            AND NOT EXISTS (
                SELECT 1

                FROM equipo_ubicacion_genera_ticket AS eugt

                INNER JOIN TICKET AS t
                    ON t.id = eugt.idTicket

                WHERE eugt.idEquipo = e.id
                AND t.estado <> 'resuelto'
            )

            ORDER BY e.id;

/* Consulta 3: equipoDisponible */
SELECT COUNT(*)

            FROM EQUIPO AS e

            INNER JOIN equipo_reside_ubicacion AS eru
                ON eru.idEquipo = e.id

            WHERE e.id = :idEquipo
            AND e.activo = 1

            AND eru.idUbicacion = 0
            AND eru.tipoUbicacion = 'prestamo'

            AND NOT EXISTS (
                SELECT 1

                FROM prestamo_corresponde_equipo AS pce

                INNER JOIN PRESTAMO AS p
                    ON p.id = pce.idPrestamo

                WHERE pce.idEquipo = e.id
                AND p.devuelto = 0
            )

            AND NOT EXISTS (
                SELECT 1

                FROM equipo_ubicacion_genera_ticket AS eugt

                INNER JOIN TICKET AS t
                    ON t.id = eugt.idTicket

                WHERE eugt.idEquipo = e.id
                AND t.estado <> 'resuelto'
            );

/* Consulta 4: obtenerEstadoPrestamo */
SELECT devuelto
            FROM PRESTAMO
            WHERE id = :idPrestamo;
