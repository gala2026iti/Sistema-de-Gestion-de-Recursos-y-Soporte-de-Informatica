/*
    POBLACION: tecnico_gestiona_ticket
*/

SET time_zone = '-03:00';
USE gala;

INSERT INTO tecnico_gestiona_ticket
(ciTecnico, idTicket, fecha, hora, tipoInteraccion) VALUES
('33333333', 2, '2026-08-17', '10:00:00', 'asignacion'),
('44444444', 2, '2026-08-17', '11:00:00', 'modificacion'),
('66666666', 3, '2026-08-18', '11:00:00', 'asignacion'),
('33333333', 4, '2026-08-18', '13:00:00', 'modificacion'),
('44444444', 5, '2026-08-19', '14:30:00', 'desasignacion'),
('33333333', 6, '2026-08-19', '16:00:00', 'asignacion');

INSERT INTO tecnico_gestiona_ticket
(ciTecnico, idTicket, fecha, hora, tipoInteraccion) VALUES
('33333333', 7, '2026-08-20', '12:00:00', 'creacion');
