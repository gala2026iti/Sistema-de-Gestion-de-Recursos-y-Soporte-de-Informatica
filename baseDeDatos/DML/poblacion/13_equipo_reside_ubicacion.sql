/*
    POBLACION: equipo_reside_ubicacion
*/

SET time_zone = '-03:00';
USE gala;

INSERT INTO equipo_reside_ubicacion
(idEquipo, idUbicacion, tipoUbicacion, posicion) VALUES
(1, 1, 'laboratorio', 1),
(2, 1, 'laboratorio', 2),
(3, 2, 'laboratorio', 1),
(4, 3, 'taller', 1),
(5, 4, 'taller', 1),
(7, 0, 'prestamo', 1),
(8, 0, 'prestamo', 2);
