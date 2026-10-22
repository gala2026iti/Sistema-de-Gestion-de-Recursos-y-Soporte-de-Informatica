/*
    TABLA: equipo_ubicacion_genera_ticket
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE equipo_ubicacion_genera_ticket (
    idEquipo INT NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    idTicket INT NOT NULL,
    CONSTRAINT pk_equipo_ubicacion_genera_ticket
        PRIMARY KEY (idEquipo, idUbicacion, idTicket),
    CONSTRAINT fk_eugt_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eugt_ticket FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eugt_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion)
        REFERENCES UBICACION (id, tipo)
        ON DELETE CASCADE ON UPDATE CASCADE
);
