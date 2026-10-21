/*
    TABLA: tecnico_comenta_ticket
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE tecnico_comenta_ticket (
    id INT NOT NULL AUTO_INCREMENT,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    texto TEXT NOT NULL,

    CONSTRAINT pk_tecnico_comenta_ticket
        PRIMARY KEY (id),

    CONSTRAINT fk_tct_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tct_ticket
        FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
