/*
    TABLA: prestamo_corresponde_equipo
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE prestamo_corresponde_equipo (
    idPrestamo INT NOT NULL,
    idEquipo INT NOT NULL,
    CONSTRAINT pk_prestamo_corresponde_equipo PRIMARY KEY (idPrestamo, idEquipo),
    CONSTRAINT fk_pce_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pce_prestamo FOREIGN KEY (idPrestamo)
        REFERENCES PRESTAMO (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
