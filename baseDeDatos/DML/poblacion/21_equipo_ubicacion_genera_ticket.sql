/*
    POBLACION: equipo_ubicacion_genera_ticket
*/

SET time_zone = '-03:00';
USE gala;

INSERT INTO equipo_ubicacion_genera_ticket
(idEquipo, idUbicacion, tipoUbicacion, idTicket) VALUES
(1, 1, 'laboratorio', 1),
(2, 1, 'laboratorio', 2),
(3, 2, 'laboratorio', 3),
(4, 3, 'taller', 4),
(5, 4, 'taller', 5),
(6, 2, 'laboratorio', 6);

INSERT INTO equipo_ubicacion_genera_ticket
(idEquipo, idUbicacion, tipoUbicacion, idTicket) VALUES
(7, 0, 'prestamo', 7);
