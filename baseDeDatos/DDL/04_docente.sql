/*
    TABLA: DOCENTE
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE DOCENTE (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_docente PRIMARY KEY (ci),
    CONSTRAINT fk_docente_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);
