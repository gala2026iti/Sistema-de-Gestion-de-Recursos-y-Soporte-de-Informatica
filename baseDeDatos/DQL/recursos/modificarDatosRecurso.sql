UPDATE EQUIPO
SET ultimaIntervencion = :ultimaIntervencion
WHERE id = :idEquipo;

UPDATE equipo_reside_ubicacion
SET posicion = :posicion
WHERE idEquipo = :idEquipo;

UPDATE equipo_reside_ubicacion
SET idUbicacion = :idUbicacion,
    tipoUbicacion = :tipoUbicacion,
    posicion = :posicion
WHERE idEquipo = :idEquipo;