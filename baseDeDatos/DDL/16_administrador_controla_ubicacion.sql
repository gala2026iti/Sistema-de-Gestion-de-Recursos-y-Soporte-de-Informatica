/*
    TABLA: administrador_controla_ubicacion
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE administrador_controla_ubicacion (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdmin CHAR(8) NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_admin_controla_ubicacion
        PRIMARY KEY (id),

    CONSTRAINT fk_acu_admin
        FOREIGN KEY (ciAdmin)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_acu_ubicacion
        FOREIGN KEY (idUbicacion, tipoUbicacion)
        REFERENCES UBICACION (id, tipo)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
