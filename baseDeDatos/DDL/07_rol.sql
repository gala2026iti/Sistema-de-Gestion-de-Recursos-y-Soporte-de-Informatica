/*
    TABLA: ROL
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE ROL (
    ci CHAR(8) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    CONSTRAINT pk_rol PRIMARY KEY (ci, rol),
    CONSTRAINT fk_rol_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);
