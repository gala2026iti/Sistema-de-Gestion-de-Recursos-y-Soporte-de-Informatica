/*
    DQL - TICKET
    Fuente: SGRSI/app/modelo/TicketDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: listarTickets */
SELECT
                t.id,
                t.tipo,
                t.asunto,
                t.descripcion,
                t.persona,
                t.gravedad,
                t.estado,
                t.fechaCreacion,
                t.horaCreacion,
                t.justificacion,

                drt.ciDocente,
                u.nombre AS nombreDocente,

                eugt.idEquipo,
                eugt.idUbicacion,
                eugt.tipoUbicacion,

                (
                    SELECT GROUP_CONCAT(
                        uTecnico.nombre
                        ORDER BY uTecnico.nombre
                        SEPARATOR ', '
                    )

                    FROM COLABORADOR AS c

                    LEFT JOIN USUARIO AS uTecnico
                        ON uTecnico.ci = c.ciTecnico

                    WHERE c.idTicket = t.id
                ) AS colaboradores,

                EXISTS (
                    SELECT 1
                    FROM COLABORADOR AS c
                    WHERE c.idTicket = t.id
                    AND c.ciTecnico = :ciTecnico
                ) AS esColaborador

            FROM TICKET AS t

            LEFT JOIN docente_reporta_ticket AS drt
                ON drt.idTicket = t.id

            LEFT JOIN USUARIO AS u
                ON u.ci = drt.ciDocente

            LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
                ON eugt.idTicket = t.id;

/* Filtros opcionales utilizados por este método:
   - drt.ciDocente = :ciDocente
   - t.gravedad = :gravedad
   - t.tipo = :tipo
   - t.estado = :estado
   - t.estado <> 'resuelto'
   - t.id = :idTicket
   - eugt.idEquipo = :idEquipo
   - EXISTS ( SELECT 1 FROM COLABORADOR AS ca WHERE ca.idTicket = t.id AND ca.ciTecnico = :ciTecnicoAsignado )
*/
/* Cláusulas de orden/agrupación que el DAO agrega según el caso:
   - ORDER BY t.fechaCreacion DESC, t.horaCreacion DESC
*/

/* Consulta 2: listarComentarios */
SELECT
            tct.id,
            tct.ciTecnico,
            u.nombre,
            tct.fecha,
            tct.hora,
            tct.texto

        FROM tecnico_comenta_ticket AS tct

        LEFT JOIN USUARIO AS u
            ON u.ci = tct.ciTecnico

        WHERE tct.idTicket = :idTicket

        ORDER BY
            tct.fecha ASC,
            tct.hora ASC,
            tct.id ASC;

/* Consulta 3: obtenerEstadoTicket */
SELECT estado
        FROM TICKET
        WHERE id = :idTicket;
