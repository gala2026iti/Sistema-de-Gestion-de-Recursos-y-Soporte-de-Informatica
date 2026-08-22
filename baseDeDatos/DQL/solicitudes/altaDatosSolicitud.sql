INSERT INTO SOLICITUD (id, asunto, descripcion, fechaLimite, horaLimite, finalizada)
VALUES (:idSolicitud, :asunto, :descripcion, :fechaLimite, :horaLimite, FALSE);

INSERT INTO docente_ingresa_solicitud (ciDocente, idSolicitud, fecha, hora)
VALUES (:ciDocente, :idSolicitud, :fecha, :hora)

INSERT INTO tecnico_finaliza_solicitud (ciTecnico, idSolicitud, fecha, hora)
VALUES (:ciTecnico, :idSolicitud, :fecha, :hora)

