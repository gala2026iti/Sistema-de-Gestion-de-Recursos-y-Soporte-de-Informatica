
UPDATE TICKET
SET estado = 'en proceso'
WHERE id = :idTicket;

UPDATE TICKET
SET estado = 'pendiente'
WHERE id = :idTicket;

UPDATE TICKET
SET estado = 'resuelto'
WHERE id = :idTicket;