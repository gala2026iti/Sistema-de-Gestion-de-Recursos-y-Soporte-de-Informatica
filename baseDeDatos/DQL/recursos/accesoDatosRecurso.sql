SELECT
    e.id AS idEquipo,
    e.fechaCreacion,
    e.horaCreacion,
    e.ultimaIntervencion,
    e.activo,
    eru.idUbicacion,
    eru.tipoUbicacion,
    eru.posicion
FROM EQUIPO AS e
LEFT JOIN equipo_reside_ubicacion AS eru
    ON e.id = eru.idEquipo
ORDER BY e.id;

SELECT
    e.id AS idEquipo,
    e.fechaCreacion,
    e.horaCreacion,
    e.ultimaIntervencion,
    e.activo,
    eru.posicion
FROM EQUIPO AS e
INNER JOIN equipo_reside_ubicacion AS eru
    ON e.id = eru.idEquipo
WHERE eru.idUbicacion = :idUbicacion 
  AND eru.tipoUbicacion = :tipoUbicacion
ORDER BY eru.posicion;

SELECT
    u.id AS idUbicacion,
    u.tipo
FROM UBICACION AS u
ORDER BY u.id;

SELECT
    ame.id,
    ame.ciAdministrador,
    u_admin.nombre AS nombreAdministrador,
    ame.idEquipo,
    ame.fecha,
    ame.hora,
    ame.tipoInteraccion
FROM administrador_maneja_equipo AS ame
INNER JOIN ADMINISTRADOR AS a
    ON a.ci = ame.ciAdministrador
INNER JOIN USUARIO AS u_admin
    ON u_admin.ci = a.ci
WHERE ame.idEquipo = :idEquipo
ORDER BY ame.fecha DESC, ame.hora DESC;

SELECT
    acu.id,
    acu.ciAdministrador,
    u_admin.nombre AS nombreAdministrador,
    acu.idUbicacion,
    acu.tipoUbicacion,
    acu.fecha,
    acu.hora,
    acu.tipoInteraccion
FROM administrador_controla_ubicacion AS acu
INNER JOIN ADMINISTRADOR AS a
    ON a.ci = acu.ciAdministrador
INNER JOIN USUARIO AS u_admin
    ON u_admin.ci = a.ci
WHERE acu.idUbicacion = :idUbicacion 
  AND acu.tipoUbicacion = :tipoUbicacion
ORDER BY acu.fecha DESC, acu.hora DESC;