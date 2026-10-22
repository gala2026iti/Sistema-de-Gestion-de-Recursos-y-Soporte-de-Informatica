/*
    POBLACION: tecnico_tramita_prestamo
*/

SET time_zone = '-03:00';
USE gala;

INSERT INTO tecnico_tramita_prestamo
(ciTecnico, idPrestamo, fecha, hora, tipoInteraccion) VALUES
('33333333', 1, '2026-08-20', '09:00:00', 'prestamo'),
('44444444', 2, '2026-08-20', '10:00:00', 'prestamo'),

('66666666', 3, '2026-08-19', '11:00:00', 'prestamo'),
('33333333', 3, '2026-08-20', '15:00:00', 'devolucion'),

('44444444', 4, '2026-08-20', '13:00:00', 'prestamo'),
('44444444', 4, '2026-08-21', '18:00:00', 'devolucion');
