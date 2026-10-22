/*
    TABLA: tecnico_tramita_prestamo
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE tecnico_tramita_prestamo (
    id INT NOT NULL AUTO_INCREMENT,
    ciTecnico CHAR(8) NOT NULL,
    idPrestamo INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_tecnico_tramita_prestamo
        PRIMARY KEY (id),

    CONSTRAINT fk_ttp_prestamo
        FOREIGN KEY (idPrestamo)
        REFERENCES PRESTAMO (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_ttp_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
