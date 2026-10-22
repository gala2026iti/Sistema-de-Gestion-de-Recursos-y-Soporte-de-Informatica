/*
    TABLA: equipo_reside_ubicacion
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE equipo_reside_ubicacion (
    idEquipo INT NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    posicion INT NOT NULL,
    CONSTRAINT pk_equipo_reside PRIMARY KEY (idEquipo),
    CONSTRAINT fk_eru_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eru_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion)
        REFERENCES UBICACION (id, tipo)
        ON DELETE CASCADE ON UPDATE CASCADE
);
