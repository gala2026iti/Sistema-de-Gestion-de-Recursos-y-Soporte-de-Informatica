/*
    TABLA: ADMINISTRADOR
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE ADMINISTRADOR (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_administrador PRIMARY KEY (ci),
    CONSTRAINT fk_administrador_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);
