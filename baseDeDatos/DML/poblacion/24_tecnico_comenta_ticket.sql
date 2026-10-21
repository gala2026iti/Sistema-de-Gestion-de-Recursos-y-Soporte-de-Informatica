/*
    POBLACION: tecnico_comenta_ticket
*/

SET time_zone = '-03:00';
USE gala;

INSERT INTO tecnico_comenta_ticket
(ciTecnico, idTicket, fecha, hora, texto) VALUES
('33333333', 2, '2026-08-17', '10:15:00',
 'Se revisara la instalacion y la configuracion de Java.'),

('66666666', 3, '2026-08-18', '11:15:00',
 'Se detecto perdida de conectividad en el laboratorio.'),

('33333333', 4, '2026-08-18', '13:15:00',
 'Se probo el monitor con un cable alternativo.'),

('44444444', 5, '2026-08-19', '14:45:00',
 'La actualizacion fue instalada correctamente.'),

('33333333', 6, '2026-08-19', '16:15:00',
 'Se esta verificando el punto de red del salon.');
