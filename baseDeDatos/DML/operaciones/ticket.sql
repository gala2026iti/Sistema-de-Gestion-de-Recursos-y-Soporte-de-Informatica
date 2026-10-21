/*
    DML OPERATIVO - TICKET
    Fuente: SGRSI/app/modelo/TicketDAO.php

    Incluye INSERT, UPDATE y DELETE ejecutados por el sistema.
    No corresponde a la población inicial.
*/

USE gala;


/* Operación 1: registrarTickets (1) */
INSERT INTO TICKET (tipo, asunto, persona, descripcion, gravedad, estado, fechaCreacion, horaCreacion)
                    VALUES ( :tipo, :asunto, :persona, :descripcion,:gravedad, 'pendiente', CURDATE(), CURTIME());

/* Operación 2: registrarTickets (2) */
INSERT INTO docente_reporta_ticket (ciDocente, idTicket)
        VALUES (:ciUsuario, :idTicket);

/* Operación 3: registrarTickets (3) */
INSERT INTO tecnico_gestiona_ticket (ciTecnico, idTicket, fecha, hora, tipoInteraccion)
        VALUES ( :ciUsuario, :idTicket, CURDATE(), CURTIME(), 'creacion');

/* Operación 4: registrarTickets (4) */
INSERT INTO equipo_ubicacion_genera_ticket (idEquipo, idUbicacion, tipoUbicacion, idTicket)
                    VALUES ( :idEquipo, :idUbicacion, :tipoUbicacion, :idTicket);

/* Operación 5: modificarTicket */
UPDATE TICKET
            SET
                gravedad = :gravedad,
                estado = :estado,
                justificacion = :justificacion
            WHERE id = :idTicket;

/* Operación 6: registrarComentario */
INSERT INTO tecnico_comenta_ticket (ciTecnico,idTicket,fecha,hora,texto)
            VALUES (:ciTecnico,:idTicket,CURDATE(),CURTIME(),:texto);

/* Operación 7: asignarTecnico */
INSERT INTO COLABORADOR (idTicket,ciTecnico)
                VALUES (:idTicket,:ciTecnico);

/* Operación 8: desasignarTecnico */
DELETE FROM COLABORADOR
                WHERE idTicket = :idTicket
                AND ciTecnico = :ciTecnico;
