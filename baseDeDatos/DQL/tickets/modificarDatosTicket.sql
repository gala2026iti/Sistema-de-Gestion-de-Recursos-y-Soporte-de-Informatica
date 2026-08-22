UPDATE TICKET
SET 
    gravedad = :gravedad,
    estado = :estado
WHERE id = :idTicket;

UPDATE TICKET
SET 
    estado = 'resuelto',
    justificacion = :justificacion
WHERE id = :idTicket;

DELETE FROM COLABORADOR 
WHERE idTicket = :idTicket AND ciTecnico = :ciTecnico;