INSERT INTO PRESTAMO (id, nombrePrestado, ciPrestado, fechaInicio, horaInicio, fechaFin, horaFin, devuelto, entregaAtrasada)
VALUES (:idPrestamo, :nombrePrestado, :ciPrestado, :fechaInicio, :horaInicio, :fechaFin, :horaFin, FALSE, FALSE);

INSERT INTO tecnico_tramita_prestamo (id, ciTecnico, idPrestamo, fecha, hora, tipoInteraccion)
VALUES (:id, :ciTecnico, :idPrestamo, :fecha, :hora, :tipoInteraccion)

INSERT INTO prestamo_corresponde_equipo (idPrestamo, idEquipo)
VALUES (:idPrestamo, :idEquipo)


