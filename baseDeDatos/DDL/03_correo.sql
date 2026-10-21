/*
    TABLA: CORREO
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE CORREO (
    ci CHAR(8) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    CONSTRAINT pk_correo PRIMARY KEY (ci, correo),
    CONSTRAINT fk_correo_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);
