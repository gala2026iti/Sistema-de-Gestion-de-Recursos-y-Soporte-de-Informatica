/*
    TABLA: USUARIO
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE USUARIO (
    ci CHAR(8) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_usuario PRIMARY KEY (ci)
);
