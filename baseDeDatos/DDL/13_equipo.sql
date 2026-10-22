/*
    TABLA: EQUIPO
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE EQUIPO (
    id INT NOT NULL,
    fechaCreacion DATE NOT NULL DEFAULT (CURRENT_DATE),
    horaCreacion TIME NOT NULL DEFAULT (CURRENT_TIME),
    ultimaIntervencion DATE NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_equipo PRIMARY KEY (id)
);
