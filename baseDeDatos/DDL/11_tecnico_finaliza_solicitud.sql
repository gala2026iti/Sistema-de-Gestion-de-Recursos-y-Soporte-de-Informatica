/*
    TABLA: tecnico_finaliza_solicitud
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE tecnico_finaliza_solicitud (
    ciTecnico CHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,

    CONSTRAINT pk_tecnico_finaliza_solicitud
        PRIMARY KEY (ciTecnico, idSolicitud),

    CONSTRAINT fk_tfs_solicitud
        FOREIGN KEY (idSolicitud)
        REFERENCES SOLICITUD (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tfs_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
