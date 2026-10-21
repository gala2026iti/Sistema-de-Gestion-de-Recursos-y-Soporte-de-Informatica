/*
    TABLA: TECNICO
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE TECNICO (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_tecnico PRIMARY KEY (ci),
    CONSTRAINT fk_tecnico_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);
