/*
    TABLA: COLABORADOR
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE COLABORADOR (
    idTicket INT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    CONSTRAINT pk_colaborador PRIMARY KEY (idTicket, ciTecnico),
    CONSTRAINT fk_colab_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_colab_ticket FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
