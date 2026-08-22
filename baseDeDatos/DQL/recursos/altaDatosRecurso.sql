INSERT INTO UBICACION (id, tipo)
VALUES (:idUbicacion, :tipoUbicacion);

INSERT INTO EQUIPO (fechaCreacion, horaCreacion, ultimaIntervencion, activo)
VALUES (:fechaCreacion, :horaCreacion, NULL, TRUE);

INSERT INTO equipo_reside_ubicacion (idEquipo, idUbicacion, tipoUbicacion, posicion)
VALUES (:idEquipo, :idUbicacion, :tipoUbicacion, :posicion);

INSERT INTO administrador_maneja_equipo (ciAdministrador, idEquipo, fecha, hora, tipoInteraccion)
VALUES (:ciAdministrador, :idEquipo, :fecha, :hora, :tipoInteraccion);

INSERT INTO administrador_controla_ubicacion (ciAdministrador, idUbicacion, tipoUbicacion, fecha, hora, tipoInteraccion)
VALUES (:ciAdministrador, :idUbicacion, :tipoUbicacion, :fecha, :hora, :tipoInteraccion);