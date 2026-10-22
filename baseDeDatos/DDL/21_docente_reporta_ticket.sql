/*
    TABLA: docente_reporta_ticket
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE docente_reporta_ticket (
    ciDocente CHAR(8) NOT NULL,
    idTicket INT NOT NULL,

    CONSTRAINT pk_docente_reporta_ticket
        PRIMARY KEY (ciDocente, idTicket),

    CONSTRAINT fk_drt_docente
        FOREIGN KEY (ciDocente)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_drt_ticket
        FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
