/*
    TABLA: docente_ingresa_solicitud
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE docente_ingresa_solicitud (
    ciDocente CHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,

    CONSTRAINT pk_docente_ingresa_solicitud
        PRIMARY KEY (ciDocente, idSolicitud),

    CONSTRAINT fk_dis_docente
        FOREIGN KEY (ciDocente)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_dis_solicitud
        FOREIGN KEY (idSolicitud)
        REFERENCES SOLICITUD (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
