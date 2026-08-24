SELECT
    t.id,
    t.tipo,
    t.asunto,
    t.descripcion,
    t.gravedad,
    t.estado,
    t.fechaCreacion,
    t.horaCreacion,
    t.justificacion,
    drt.ciDocente,
    u_doc.nombre AS nombreDocente,
    eugt.idEquipo,
    eugt.idUbicacion,
    eugt.tipoUbicacion
FROM TICKET AS t
LEFT JOIN docente_reporta_ticket AS drt ON drt.idTicket = t.id
LEFT JOIN USUARIO AS u_doc ON u_doc.ci = drt.ciDocente
LEFT JOIN equipo_ubicacion_genera_ticket AS eugt ON eugt.idTicket = t.id
ORDER BY t.id DESC;

SELECT
    t.id,
    t.tipo,
    t.asunto,
    t.descripcion,
    t.gravedad,
    t.estado,
    t.fechaCreacion,
    t.horaCreacion,
    t.justificacion,
    drt.ciDocente,
    u_doc.nombre AS nombreDocente,
    eugt.idEquipo,
    eugt.idUbicacion
FROM TICKET AS t
LEFT JOIN docente_reporta_ticket AS drt ON drt.idTicket = t.id
LEFT JOIN USUARIO AS u_doc ON u_doc.ci = drt.ciDocente
LEFT JOIN equipo_ubicacion_genera_ticket AS eugt ON eugt.idTicket = t.id
WHERE t.id = :idTicket;

SELECT
    c.idTicket,
    c.ciTecnico,
    u.nombre AS nombreTecnico,
    u.correo AS correoTecnico
FROM COLABORADOR AS c
INNER JOIN USUARIO AS u ON u.ci = c.ciTecnico
WHERE c.idTicket = :idTicket;

SELECT
    tct.id,
    tct.fecha,
    tct.hora,
    tct.texto,
    tct.ciTecnico,
    u.nombre AS nombreTecnico
FROM tecnico_comenta_ticket AS tct
INNER JOIN USUARIO AS u ON u.ci = tct.ciTecnico
WHERE tct.idTicket = :idTicket
ORDER BY tct.fecha ASC, tct.hora ASC;

SELECT
    tgt.id,
    tgt.fecha,
    tgt.hora,
    tgt.tipoInteraccion,
    tgt.ciTecnico,
    u.nombre AS nombreTecnico
FROM tecnico_gestiona_ticket AS tgt
INNER JOIN USUARIO AS u ON u.ci = tgt.ciTecnico
WHERE tgt.idTicket = :idTicket
ORDER BY tgt.fecha DESC, tgt.hora DESC;