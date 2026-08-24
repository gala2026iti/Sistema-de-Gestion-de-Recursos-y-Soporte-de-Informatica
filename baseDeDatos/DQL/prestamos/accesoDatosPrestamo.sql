/* OBTENER INFORMACIÓN BASICA DE PRESTAMOS */

SELECT
    p.id,
    p.nombrePrestado,
    p.ciPrestado,
    p.fechaFin,
    p.horaFin,
    p.devuelto, 
    ttp.ciTecnico,
    pce.idEquipo,
    u.nombre AS nombreTecnico

FROM PRESTAMO AS p

INNER JOIN prestamo_corresponde_equipo AS pce
ON pce.idPrestamo = p.id

INNER JOIN tecnico_tramita_prestamo AS ttp
ON ttp.idPrestamo = p.id

INNER JOIN USUARIO AS u
ON u.ci = ttp.ciTecnico

WHERE p.devuelto = FALSE

ORDER BY p.id