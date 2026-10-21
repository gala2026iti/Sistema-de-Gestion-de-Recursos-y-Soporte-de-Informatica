/*
    TABLA: tecnico_gestiona_ticket
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE tecnico_gestiona_ticket (
    id INT NOT NULL AUTO_INCREMENT,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_tecnico_gestiona_ticket
        PRIMARY KEY (id),

    CONSTRAINT fk_tgt_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tgt_ticket
        FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
