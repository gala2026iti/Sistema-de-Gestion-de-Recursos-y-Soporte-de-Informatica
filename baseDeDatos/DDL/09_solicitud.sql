/*
    TABLA: SOLICITUD
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE SOLICITUD (
    id INT NOT NULL AUTO_INCREMENT,
    asunto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    fechaLimite DATE NOT NULL,
    horaLimite TIME NOT NULL,
    finalizada BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_solicitud PRIMARY KEY (id)
);
