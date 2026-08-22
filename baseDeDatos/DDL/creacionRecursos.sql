USE sgrsi;

CREATE TABLE IF NOT EXISTS UBICACION (
    id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    CONSTRAINT pk_ubicacion PRIMARY KEY (id, tipo)
);

CREATE TABLE IF NOT EXISTS EQUIPO (
    id INT AUTO_INCREMENT NOT NULL,
    fechaCreacion DATE NOT NULL,
    horaCreacion TIME NOT NULL,
    ultimaIntervencion DATE NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_equipo PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS equipo_reside_ubicacion (
    idEquipo INT NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    posicion VARCHAR(20) NOT NULL,
    
    CONSTRAINT pk_equipo_reside PRIMARY KEY (idEquipo),
    
    CONSTRAINT fk_eru_equipo FOREIGN KEY (idEquipo) 
        REFERENCES EQUIPO (id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
        
    CONSTRAINT fk_eru_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion) 
        REFERENCES UBICACION (id, tipo) 
        ON DELETE CASCADE ON UPDATE CASCADE,
        
    CONSTRAINT uk_ubicacion_posicion UNIQUE (idUbicacion, tipoUbicacion, posicion)
);

CREATE TABLE IF NOT EXISTS administrador_maneja_equipo (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdministrador CHAR(8) NOT NULL,
    idEquipo INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_admin_maneja_equipo PRIMARY KEY (id),
    CONSTRAINT fk_ame_admin FOREIGN KEY (ciAdministrador) 
        REFERENCES ADMINISTRADOR (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ame_equipo FOREIGN KEY (idEquipo) 
        REFERENCES EQUIPO (id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS administrador_controla_ubicacion (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdministrador CHAR(8) NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_admin_controla_ubicacion PRIMARY KEY (id),
    CONSTRAINT fk_acu_admin FOREIGN KEY (ciAdministrador) 
        REFERENCES ADMINISTRADOR (ci) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_acu_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion) 
        REFERENCES UBICACION (id, tipo) 
        ON DELETE CASCADE ON UPDATE CASCADE
);