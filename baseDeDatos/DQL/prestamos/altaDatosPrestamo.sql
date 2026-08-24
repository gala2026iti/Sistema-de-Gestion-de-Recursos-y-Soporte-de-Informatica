INSERT INTO PRESTAMO (id, nombrePrestado, ciPrestado, fechaFin, horaFin, devuelto)
VALUES (:idPrestamo, :nombrePrestado, :ciPrestado, :fechaFin, :horaFin, FALSE);

INSERT INTO tecnico_tramita_prestamo (id, ciTecnico, idPrestamo, fecha, hora, tipoInteraccion)
VALUES (:id, :ciTecnico, :idPrestamo, :fecha, :hora, :tipoInteraccion)

INSERT INTO prestamo_corresponde_equipo (idPrestamo, idEquipo)
VALUES (:idPrestamo, :idEquipo)


