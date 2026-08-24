
USE sgrsi;

CREATE TABLE IF NOT EXISTS TICKET (
    id INT AUTO_INCREMENT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    gravedad VARCHAR(20) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    fechaCreacion DATE NOT NULL,
    horaCreacion TIME NOT NULL,
    justificacion VARCHAR(255) NULL,
    CONSTRAINT pk_ticket PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS COLABORADOR (
    idTicket INT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    CONSTRAINT pk_colaborador PRIMARY KEY (idTicket, ciTecnico),
    CONSTRAINT fk_colab_ticket FOREIGN KEY (idTicket) 
        REFERENCES TICKET (id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_colab_tecnico FOREIGN KEY (ciTecnico) 
        REFERENCES TECNICO (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS docente_reporta_ticket (
    ciDocente CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    CONSTRAINT pk_docente_reporta_ticket PRIMARY KEY (ciDocente, idTicket),
    CONSTRAINT fk_drt_docente FOREIGN KEY (ciDocente) 
        REFERENCES DOCENTE (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_drt_ticket FOREIGN KEY (idTicket) 
        REFERENCES TICKET (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS equipo_ubicacion_genera_ticket (
    idEquipo INT NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion INT NOT NULL,
    idTicket INT NOT NULL,
    CONSTRAINT pk_eugt PRIMARY KEY (idEquipo, idUbicacion, idTicket),
    CONSTRAINT fk_eugt_equipo FOREIGN KEY (idEquipo) 
        REFERENCES EQUIPO (id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eugt_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion) 
        REFERENCES UBICACION (id, tipo) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eugt_ticket FOREIGN KEY (idTicket) 
        REFERENCES TICKET (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_gestiona_ticket (
    id INT AUTO_INCREMENT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tecnico_gestiona_ticket PRIMARY KEY (id),
    CONSTRAINT fk_tgt_tecnico FOREIGN KEY (ciTecnico) 
        REFERENCES TECNICO (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tgt_ticket FOREIGN KEY (idTicket) 
        REFERENCES TICKET (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_comenta_ticket (
    id INT AUTO_INCREMENT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    texto TEXT NOT NULL,
    CONSTRAINT pk_tecnico_comenta_ticket PRIMARY KEY (id),
    CONSTRAINT fk_tct_tecnico FOREIGN KEY (ciTecnico) 
        REFERENCES TECNICO (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tct_ticket FOREIGN KEY (idTicket) 
        REFERENCES TICKET (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);