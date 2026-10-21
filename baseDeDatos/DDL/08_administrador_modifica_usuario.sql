/*
    TABLA: administrador_modifica_usuario
*/

SET time_zone = '-03:00';
USE gala;

CREATE TABLE administrador_modifica_usuario (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdmin CHAR(8) NOT NULL,
    ciUsuario CHAR(8) NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_admin_modifica_usuario
        PRIMARY KEY (id),

    CONSTRAINT fk_amu_admin
        FOREIGN KEY (ciAdmin)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_amu_usuario
        FOREIGN KEY (ciUsuario)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
