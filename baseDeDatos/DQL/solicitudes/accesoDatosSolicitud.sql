/* SELECCION DE TODAS LAS SOLICITUDES */

SELECT
s.id,
s.asunto,
s.descripcion,
s.fechaLimite,
s.horaLimite,
s.finalizada,
dis.ciDocente,
u.ci

FROM SOLICITUD AS s

INNER JOIN docente_ingresa_solicitud AS dis
ON dis.idSolicitud = s.id

INNER JOIN USUARIO AS u
ON u.ci = dis.ciDocente

ORDER BY s.id

/* SELECCION DE TODAS LAS SOLICITUDES FINALIZADAS */

SELECT
    s.id,
    s.asunto,
    s.descripcion,
    s.fechaLimite,
    s.horaLimite,
    s.finalizada,
    dis.ciDocente,
    u_docente.nombre AS nombreDocente,
    tfs.ciTecnico,
    u_tecnico.nombre AS nombreTecnico,
    tfs.fecha,
    tfs.hora

FROM SOLICITUD AS s

INNER JOIN docente_ingresa_solicitud AS dis
    ON dis.idSolicitud = s.id

INNER JOIN USUARIO AS u_docente
    ON u_docente.ci = dis.ciDocente

INNER JOIN tecnico_finaliza_solicitud AS tfs
    ON tfs.idSolicitud = s.id

INNER JOIN USUARIO AS u_tecnico
    ON u_tecnico.ci = tfs.ciTecnico

WHERE s.finalizada = TRUE
ORDER BY s.id;
