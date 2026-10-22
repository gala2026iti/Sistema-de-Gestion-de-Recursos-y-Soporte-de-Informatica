/*
    POBLACION: PRESTAMO
*/

SET time_zone = '-03:00';
USE gala;

INSERT INTO PRESTAMO
(id, nombrePrestado, ciPrestado, fechaFin, horaFin, devuelto) VALUES
(1, 'Martin Rodriguez', '40123456', '2026-08-25', '17:00:00', FALSE),
(2, 'Lucia Fernandez', '41234567', '2026-08-26', '16:30:00', FALSE),
(3, 'Sofia Martinez', '42345678', '2026-08-20', '15:00:00', TRUE),
(4, 'Nicolas Pereira', '43456789', '2026-08-21', '18:00:00', TRUE);
