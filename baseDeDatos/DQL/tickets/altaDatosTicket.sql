INSERT INTO TICKET (
    tipo, 
    asunto, 
    descripcion, 
    gravedad, 
    estado, 
    fechaCreacion, 
    horaCreacion
) VALUES (
    :tipo, 
    :asunto, 
    :descripcion, 
    :gravedad, 
    'Pendiente', 
    CURRENT_DATE(), 
    CURRENT_TIME()
);

INSERT INTO docente_reporta_ticket (ciDocente, idTicket) 
VALUES (:ciDocente, :idTicket);

INSERT INTO equipo_ubicacion_genera_ticket (idEquipo, idUbicacion, idTicket) 
VALUES (:idEquipo, :idUbicacion, :idTicket);

INSERT INTO COLABORADOR (idTicket, ciTecnico) 
VALUES (:idTicket, :ciTecnico);

INSERT INTO tecnico_comenta_ticket (ciTecnico, idTicket, fecha, hora, texto) 
VALUES (:ciTecnico, :idTicket, CURRENT_DATE(), CURRENT_TIME(), :texto);

INSERT INTO tecnico_gestiona_ticket (ciTecnico, idTicket, fecha, hora, tipoInteraccion) 
VALUES (:ciTecnico, :idTicket, CURRENT_DATE(), CURRENT_TIME(), :tipoInteraccion);