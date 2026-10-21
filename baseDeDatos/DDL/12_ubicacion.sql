/*
    TABLA: UBICACION
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE UBICACION (
    id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_ubicacion PRIMARY KEY (id, tipo)
);
