/*
    TABLA: administrador_maneja_equipo
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE administrador_maneja_equipo (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdmin CHAR(8) NOT NULL,
    idEquipo INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_admin_maneja_equipo
        PRIMARY KEY (id),

    CONSTRAINT fk_ame_admin
        FOREIGN KEY (ciAdmin)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_ame_equipo
        FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
