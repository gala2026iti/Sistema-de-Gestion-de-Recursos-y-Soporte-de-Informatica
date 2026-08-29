USE sgrsi;

CREATE TABLE IF NOT EXISTS SOLICITUD (
    id INT AUTO_INCREMENT NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    fechaLimite CHAR(10) NOT NULL,
    horaLimite CHAR(5) NOT NULL,
    finalizada BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_solicitud PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS docente_ingresa_solicitud (
    ciDocente VARCHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    fecha CHAR(10) NOT NULL,
    hora CHAR(5) NOT NULL,
    CONSTRAINT pk_docente_ingresa_solicitud PRIMARY KEY (ciDocente, idSolicitud),
    CONSTRAINT fk_dis_docente FOREIGN KEY (ciDocente) 
        REFERENCES DOCENTE (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_dis_solicitud FOREIGN KEY (idSolicitud) 
        REFERENCES SOLICITUD (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE IF NOT EXISTS tecnico_finaliza_solicitud (
    ciTecnico VARCHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    fecha CHAR(10) NOT NULL,
    hora CHAR(5) NOT NULL,
    CONSTRAINT pk_tecnico_finaliza_solicitud PRIMARY KEY (ciTecnico, idSolicitud),
    CONSTRAINT fk_tfs_tecnico FOREIGN KEY (ciTecnico) 
        REFERENCES TECNICO (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tfs_solicitud FOREIGN KEY (idSolicitud) 
        REFERENCES SOLICITUD (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

