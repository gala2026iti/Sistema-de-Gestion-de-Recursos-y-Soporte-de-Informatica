/*
    DQL - SOLICITUD
    Fuente: SGRSI/app/modelo/SolicitudDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: listarSolicitudes */
SELECT
                s.id,
                s.asunto,
                s.descripcion,
                s.fechaLimite,
                s.horaLimite,
                s.finalizada,

                dis.ciDocente,
                uDocente.nombre AS nombreDocente,
                dis.fecha AS fechaIngreso,
                dis.hora AS horaIngreso,

                tfs.ciTecnico,
                uTecnico.nombre AS nombreTecnico,
                tfs.fecha AS fechaFinalizacion,
                tfs.hora AS horaFinalizacion

            FROM SOLICITUD AS s

            INNER JOIN docente_ingresa_solicitud AS dis
                ON dis.idSolicitud = s.id

            INNER JOIN USUARIO AS uDocente
                ON uDocente.ci = dis.ciDocente

            LEFT JOIN tecnico_finaliza_solicitud AS tfs
                ON tfs.idSolicitud = s.id

            LEFT JOIN USUARIO AS uTecnico
                ON uTecnico.ci = tfs.ciTecnico;

/* Filtros opcionales utilizados por este método:
   - s.finalizada = 0
   - s.finalizada = 1
   - s.id = :id
*/

/* Consulta 2: obtenerEstadoSolicitud */
SELECT finalizada
            FROM SOLICITUD
            WHERE id = :idSolicitud;
