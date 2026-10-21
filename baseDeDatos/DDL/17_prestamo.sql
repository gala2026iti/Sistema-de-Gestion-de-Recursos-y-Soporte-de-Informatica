/*
    TABLA: PRESTAMO
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE PRESTAMO (
    id INT NOT NULL AUTO_INCREMENT,
    nombrePrestado VARCHAR(100) NOT NULL,
    ciPrestado CHAR(8) NOT NULL,
    fechaFin DATE NOT NULL,
    horaFin TIME NOT NULL,
    devuelto BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_prestamo PRIMARY KEY (id)
);
