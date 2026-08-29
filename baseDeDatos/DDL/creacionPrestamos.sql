USE sgrsi;
CREATE TABLE IF NOT EXISTS PRESTAMO (
    id INT AUTO_INCREMENT NOT NULL,
    nombrePrestado VARCHAR(100) NOT NULL,
    ciPrestado CHAR(8) NOT NULL,
    fechaFin CHAR(10) NOT NULL,
    horaFin CHAR(5) NOT NULL,
    devuelto BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_prestamo PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS tecnico_tramita_prestamo (
    id INT AUTO_INCREMENT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idPrestamo INT NOT NULL,
    fecha CHAR(10) NOT NULL,
    hora CHAR(5) NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tecnico_tramita_prestamo PRIMARY KEY (id),
    CONSTRAINT fk_ttp_tecnico FOREIGN KEY (ciTecnico) 
        REFERENCES TECNICO (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ttp_prestamo FOREIGN KEY (idPrestamo) 
        REFERENCES PRESTAMO (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS prestamo_corresponde_equipo (
    idPrestamo INT NOT NULL,
    idEquipo INT NOT NULL,
    CONSTRAINT pk_pce PRIMARY KEY (idPrestamo, idEquipo),
    CONSTRAINT fk_pce_prestamo FOREIGN KEY (idPrestamo) 
        REFERENCES PRESTAMO (id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pce_equipo FOREIGN KEY (idEquipo) 
        REFERENCES EQUIPO (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);