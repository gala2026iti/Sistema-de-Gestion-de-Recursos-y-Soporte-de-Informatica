/*
    TABLA: TICKET
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE TICKET (
    id INT NOT NULL AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    gravedad VARCHAR(20) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    fechaCreacion DATE NOT NULL,
    horaCreacion TIME NOT NULL,
    justificacion VARCHAR(255) DEFAULT NULL,
    persona VARCHAR(100) DEFAULT NULL,
    CONSTRAINT pk_ticket PRIMARY KEY (id)
);
