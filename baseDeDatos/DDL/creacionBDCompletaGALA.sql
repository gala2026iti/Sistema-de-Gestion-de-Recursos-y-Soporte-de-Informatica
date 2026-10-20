/*
    BASE DE DATOS GALA

    La base de datos "gala" ya existe en el servidor.
    Este archivo crea la estructura completa del sistema
    y carga datos de prueba.

    IMPORTANTE:
    No se crea ni se elimina la base de datos.
*/

SET time_zone = '-03:00';

USE gala;


/*
    ELIMINACION DE TABLAS
*/

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS tecnico_comenta_ticket;
DROP TABLE IF EXISTS tecnico_gestiona_ticket;
DROP TABLE IF EXISTS COLABORADOR;
DROP TABLE IF EXISTS equipo_ubicacion_genera_ticket;
DROP TABLE IF EXISTS docente_reporta_ticket;

DROP TABLE IF EXISTS tecnico_tramita_prestamo;
DROP TABLE IF EXISTS prestamo_corresponde_equipo;

DROP TABLE IF EXISTS tecnico_finaliza_solicitud;
DROP TABLE IF EXISTS docente_ingresa_solicitud;

DROP TABLE IF EXISTS administrador_controla_ubicacion;
DROP TABLE IF EXISTS administrador_maneja_equipo;
DROP TABLE IF EXISTS equipo_reside_ubicacion;

DROP TABLE IF EXISTS administrador_modifica_usuario;

DROP TABLE IF EXISTS TICKET;
DROP TABLE IF EXISTS PRESTAMO;
DROP TABLE IF EXISTS SOLICITUD;

DROP TABLE IF EXISTS EQUIPO;
DROP TABLE IF EXISTS UBICACION;

DROP TABLE IF EXISTS ROL;
DROP TABLE IF EXISTS ADMINISTRADOR;
DROP TABLE IF EXISTS TECNICO;
DROP TABLE IF EXISTS DOCENTE;
DROP TABLE IF EXISTS CORREO;
DROP TABLE IF EXISTS USUARIO;

SET FOREIGN_KEY_CHECKS = 1;


/*
    SECCION USUARIOS
*/

CREATE TABLE USUARIO (
    ci CHAR(8) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_usuario PRIMARY KEY (ci)
);


CREATE TABLE CORREO (
    ci CHAR(8) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    CONSTRAINT pk_correo PRIMARY KEY (ci, correo),
    CONSTRAINT fk_correo_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE DOCENTE (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_docente PRIMARY KEY (ci),
    CONSTRAINT fk_docente_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE ADMINISTRADOR (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_administrador PRIMARY KEY (ci),
    CONSTRAINT fk_administrador_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE TECNICO (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_tecnico PRIMARY KEY (ci),
    CONSTRAINT fk_tecnico_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE ROL (
    ci CHAR(8) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    CONSTRAINT pk_rol PRIMARY KEY (ci, rol),
    CONSTRAINT fk_rol_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);


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


/*
    SECCION SOLICITUDES
*/

CREATE TABLE SOLICITUD (
    id INT NOT NULL AUTO_INCREMENT,
    asunto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    fechaLimite DATE NOT NULL,
    horaLimite TIME NOT NULL,
    finalizada BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_solicitud PRIMARY KEY (id)
);


CREATE TABLE docente_ingresa_solicitud (
    ciDocente VARCHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,

    CONSTRAINT pk_docente_ingresa_solicitud
        PRIMARY KEY (ciDocente, idSolicitud),

    CONSTRAINT fk_dis_docente
        FOREIGN KEY (ciDocente)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_dis_solicitud
        FOREIGN KEY (idSolicitud)
        REFERENCES SOLICITUD (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE tecnico_finaliza_solicitud (
    ciTecnico VARCHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,

    CONSTRAINT pk_tecnico_finaliza_solicitud
        PRIMARY KEY (ciTecnico, idSolicitud),

    CONSTRAINT fk_tfs_solicitud
        FOREIGN KEY (idSolicitud)
        REFERENCES SOLICITUD (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tfs_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
    SECCION RECURSOS
*/

CREATE TABLE UBICACION (
    id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_ubicacion PRIMARY KEY (id, tipo)
);


CREATE TABLE EQUIPO (
    id INT NOT NULL,
    fechaCreacion DATE NOT NULL DEFAULT (CURRENT_DATE),
    horaCreacion TIME NOT NULL DEFAULT (CURRENT_TIME),
    ultimaIntervencion DATE NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_equipo PRIMARY KEY (id)
);


CREATE TABLE equipo_reside_ubicacion (
    idEquipo INT NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    posicion INT NOT NULL,
    CONSTRAINT pk_equipo_reside PRIMARY KEY (idEquipo),
    CONSTRAINT fk_eru_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eru_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion)
        REFERENCES UBICACION (id, tipo)
        ON DELETE CASCADE ON UPDATE CASCADE
);


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


/*
    SECCION PRESTAMOS
*/

CREATE TABLE PRESTAMO (
    id INT NOT NULL AUTO_INCREMENT,
    nombrePrestado VARCHAR(100) NOT NULL,
    ciPrestado CHAR(8) NOT NULL,
    fechaFin DATE NOT NULL,
    horaFin TIME NOT NULL,
    devuelto BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_prestamo PRIMARY KEY (id)
);


CREATE TABLE prestamo_corresponde_equipo (
    idPrestamo INT NOT NULL,
    idEquipo INT NOT NULL,
    CONSTRAINT pk_prestamo_corresponde_equipo PRIMARY KEY (idPrestamo, idEquipo),
    CONSTRAINT fk_pce_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pce_prestamo FOREIGN KEY (idPrestamo)
        REFERENCES PRESTAMO (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE tecnico_tramita_prestamo (
    id INT NOT NULL AUTO_INCREMENT,
    ciTecnico CHAR(8) NOT NULL,
    idPrestamo INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_tecnico_tramita_prestamo
        PRIMARY KEY (id),

    CONSTRAINT fk_ttp_prestamo
        FOREIGN KEY (idPrestamo)
        REFERENCES PRESTAMO (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_ttp_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*
    SECCION TICKETS
*/

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


CREATE TABLE docente_reporta_ticket (
    ciDocente CHAR(8) NOT NULL,
    idTicket INT NOT NULL,

    CONSTRAINT pk_docente_reporta_ticket
        PRIMARY KEY (ciDocente, idTicket),

    CONSTRAINT fk_drt_docente
        FOREIGN KEY (ciDocente)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_drt_ticket
        FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE equipo_ubicacion_genera_ticket (
    idEquipo INT NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    idTicket INT NOT NULL,
    CONSTRAINT pk_equipo_ubicacion_genera_ticket
        PRIMARY KEY (idEquipo, idUbicacion, idTicket),
    CONSTRAINT fk_eugt_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eugt_ticket FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eugt_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion)
        REFERENCES UBICACION (id, tipo)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE COLABORADOR (
    idTicket INT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    CONSTRAINT pk_colaborador PRIMARY KEY (idTicket, ciTecnico),
    CONSTRAINT fk_colab_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_colab_ticket FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE tecnico_gestiona_ticket (
    id INT NOT NULL AUTO_INCREMENT,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipoInteraccion VARCHAR(50) NOT NULL,

    CONSTRAINT pk_tecnico_gestiona_ticket
        PRIMARY KEY (id),

    CONSTRAINT fk_tgt_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tgt_ticket
        FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE tecnico_comenta_ticket (
    id INT NOT NULL AUTO_INCREMENT,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    texto TEXT NOT NULL,

    CONSTRAINT pk_tecnico_comenta_ticket
        PRIMARY KEY (id),

    CONSTRAINT fk_tct_tecnico
        FOREIGN KEY (ciTecnico)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tct_ticket
        FOREIGN KEY (idTicket)
        REFERENCES TICKET (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*
    POBLACION DE TABLAS
*/


/*
    USUARIOS
*/

INSERT INTO USUARIO (ci, nombre, clave, activo) VALUES
('11111111', 'Administrador Principal', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', TRUE),
('22222222', 'Docente Principal', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', TRUE),
('33333333', 'Tecnico Principal', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', TRUE),
('44444444', 'Leandro', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', TRUE),
('55555555', 'Docente Inactivo', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', FALSE),
('66666666', 'Tecnico Docente', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', TRUE),
('77777777', 'Administrador Inactivo', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', FALSE),
('88888888', 'Docente Secundario', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS', TRUE);


INSERT INTO CORREO (ci, correo) VALUES
('11111111', 'administrador@sgrsi.edu.uy'),
('22222222', 'docente@sgrsi.edu.uy'),
('33333333', 'tecnico@sgrsi.edu.uy'),
('44444444', 'leandro@sgrsi.edu.uy'),
('55555555', 'docente.inactivo@sgrsi.edu.uy'),
('66666666', 'tecnico.docente@sgrsi.edu.uy'),
('77777777', 'administrador.inactivo@sgrsi.edu.uy'),
('88888888', 'docente.secundario@sgrsi.edu.uy');


INSERT INTO ADMINISTRADOR (ci) VALUES
('11111111'),
('44444444'),
('77777777');


INSERT INTO TECNICO (ci) VALUES
('33333333'),
('44444444'),
('66666666');


INSERT INTO DOCENTE (ci) VALUES
('22222222'),
('55555555'),
('66666666'),
('88888888');


INSERT INTO ROL (ci, rol) VALUES
('11111111', 'administrador'),
('44444444', 'administrador'),
('77777777', 'administrador'),

('33333333', 'tecnico'),
('44444444', 'tecnico'),
('66666666', 'tecnico'),

('22222222', 'docente'),
('55555555', 'docente'),
('66666666', 'docente'),
('88888888', 'docente');


INSERT INTO administrador_modifica_usuario
(ciAdmin, ciUsuario, tipoInteraccion) VALUES
('11111111', '55555555', 'desactivacion'),
('11111111', '66666666', 'modificacion'),
('44444444', '88888888', 'modificacion'),
('11111111', '77777777', 'desactivacion');


/*
    UBICACIONES
*/

/*
    IMPORTANTE:

    La ubicación (0, 'prestamo') es una ubicación interna reservada
    utilizada exclusivamente para los equipos destinados a préstamo.

    No representa un laboratorio ni un taller real.
    No debe eliminarse ni mostrarse como una ubicación normal
    al usuario.
*/

INSERT INTO UBICACION (id, tipo) VALUES
(0, 'prestamo');


INSERT INTO UBICACION (id, tipo) VALUES
(1, 'laboratorio'),
(2, 'laboratorio'),
(3, 'taller'),
(4, 'taller');


/*
    EQUIPOS
*/

INSERT INTO EQUIPO (id) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8);


INSERT INTO equipo_reside_ubicacion
(idEquipo, idUbicacion, tipoUbicacion, posicion) VALUES
(1, 1, 'laboratorio', 1),
(2, 1, 'laboratorio', 2),
(3, 2, 'laboratorio', 1),
(4, 3, 'taller', 1),
(5, 4, 'taller', 1),
(7, 0, 'prestamo', 1),
(8, 0, 'prestamo', 2);


INSERT INTO administrador_maneja_equipo
(ciAdmin, idEquipo, tipoInteraccion) VALUES
('11111111', 1, 'creacion'),
('11111111', 5, 'desactivacion'),
('44444444', 7, 'modificacion'),
('44444444', 8, 'activacion');


INSERT INTO administrador_controla_ubicacion
(ciAdmin, idUbicacion, tipoUbicacion, tipoInteraccion) VALUES
('11111111', 1, 'laboratorio', 'creacion'),
('11111111', 2, 'laboratorio', 'activacion'),
('44444444', 3, 'taller', 'creacion'),
('44444444', 4, 'taller', 'creacion');


/*
    SOLICITUDES
*/

INSERT INTO SOLICITUD
(id, asunto, descripcion, fechaLimite, horaLimite, finalizada) VALUES
(1, 'Instalar NetBeans',
 'Instalar NetBeans en los equipos del Laboratorio 1.',
 '2026-08-18', '18:00:00', FALSE),

(2, 'Preparar laboratorio',
 'Verificar que todos los equipos del Laboratorio 2 esten operativos.',
 '2026-08-26', '12:00:00', TRUE),

(3, 'Actualizar software',
 'Actualizar el navegador web de los equipos utilizados por el grupo.',
 '2026-09-02', '17:00:00', FALSE),

(4, 'Configurar proyector',
 'Comprobar y configurar el proyector antes de la actividad.',
 '2026-08-22', '09:00:00', TRUE);


INSERT INTO docente_ingresa_solicitud
(ciDocente, idSolicitud, fecha, hora) VALUES
('22222222', 1, '2026-08-15', '10:00:00'),
('88888888', 2, '2026-08-16', '11:00:00'),
('66666666', 3, '2026-08-17', '09:30:00'),
('22222222', 4, '2026-08-18', '08:00:00');


INSERT INTO tecnico_finaliza_solicitud
(ciTecnico, idSolicitud, fecha, hora) VALUES
('33333333', 2, '2026-08-24', '12:30:00'),
('44444444', 4, '2026-08-21', '16:00:00');


/*
    PRESTAMOS
*/

INSERT INTO PRESTAMO
(id, nombrePrestado, ciPrestado, fechaFin, horaFin, devuelto) VALUES
(1, 'Martin Rodriguez', '40123456', '2026-08-25', '17:00:00', FALSE),
(2, 'Lucia Fernandez', '41234567', '2026-08-26', '16:30:00', FALSE),
(3, 'Sofia Martinez', '42345678', '2026-08-20', '15:00:00', TRUE),
(4, 'Nicolas Pereira', '43456789', '2026-08-21', '18:00:00', TRUE);


INSERT INTO prestamo_corresponde_equipo
(idPrestamo, idEquipo) VALUES
(1, 7),
(2, 8),
(3, 7),
(4, 8);


INSERT INTO tecnico_tramita_prestamo
(ciTecnico, idPrestamo, fecha, hora, tipoInteraccion) VALUES
('33333333', 1, '2026-08-20', '09:00:00', 'prestamo'),
('44444444', 2, '2026-08-20', '10:00:00', 'prestamo'),

('66666666', 3, '2026-08-19', '11:00:00', 'prestamo'),
('33333333', 3, '2026-08-20', '15:00:00', 'devolucion'),

('44444444', 4, '2026-08-20', '13:00:00', 'prestamo'),
('44444444', 4, '2026-08-21', '18:00:00', 'devolucion');


/*
    TICKETS
*/

INSERT INTO TICKET
(id, tipo, asunto, persona, descripcion, gravedad, estado,
 fechaCreacion, horaCreacion, justificacion) VALUES

(1, 'hardware',
 'Teclado no responde',
 'Estudiante 1',
 'El teclado del equipo deja de responder de forma intermitente.',
 'ligera', 'pendiente',
 '2026-08-17', '08:30:00', NULL),

(2, 'software',
 'NetBeans no inicia',
 'Estudiante 2',
 'La aplicacion NetBeans se cierra inmediatamente al abrirla.',
 'media', 'en proceso',
 '2026-08-17', '09:15:00', NULL),

(3, 'red',
 'Sin conexion a Internet',
 NULL,
 'Los equipos del laboratorio no pueden acceder a la red.',
 'grave', 'pendiente',
 '2026-08-18', '10:00:00', NULL),

(4, 'hardware',
 'Monitor sin imagen',
 'Estudiante 3',
 'El monitor enciende pero no recibe señal del equipo.',
 'grave', 'resuelto',
 '2026-08-18', '11:30:00',
 'Se reemplazo el cable de video defectuoso.'),

(5, 'software',
 'Navegador desactualizado',
 NULL,
 'El navegador instalado no permite acceder correctamente a algunas plataformas.',
 'ligera', 'resuelto',
 '2026-08-19', '13:30:00',
 'Se actualizo el navegador a la version disponible.'),

(6, 'red',
 'Conexion inestable',
 'Estudiante 4',
 'La conexion de red presenta cortes durante las clases.',
 'media', 'en proceso',
 '2026-08-19', '15:00:00', NULL);


INSERT INTO docente_reporta_ticket
(ciDocente, idTicket) VALUES
('22222222', 1),
('22222222', 2),
('88888888', 3),
('66666666', 4),
('88888888', 5),
('22222222', 6);


/*
    Esta relación conserva la ubicación en la que se encontraba
    el equipo cuando se generó cada ticket.
*/

INSERT INTO equipo_ubicacion_genera_ticket
(idEquipo, idUbicacion, tipoUbicacion, idTicket) VALUES
(1, 1, 'laboratorio', 1),
(2, 1, 'laboratorio', 2),
(3, 2, 'laboratorio', 3),
(4, 3, 'taller', 4),
(5, 4, 'taller', 5),
(6, 2, 'laboratorio', 6);


INSERT INTO COLABORADOR
(idTicket, ciTecnico) VALUES
(2, '33333333'),
(2, '44444444'),
(3, '66666666'),
(4, '33333333'),
(5, '44444444'),
(6, '33333333'),
(6, '66666666');


INSERT INTO tecnico_gestiona_ticket
(ciTecnico, idTicket, fecha, hora, tipoInteraccion) VALUES
('33333333', 2, '2026-08-17', '10:00:00', 'asignacion'),
('44444444', 2, '2026-08-17', '11:00:00', 'modificacion'),
('66666666', 3, '2026-08-18', '11:00:00', 'asignacion'),
('33333333', 4, '2026-08-18', '13:00:00', 'modificacion'),
('44444444', 5, '2026-08-19', '14:30:00', 'desasignacion'),
('33333333', 6, '2026-08-19', '16:00:00', 'asignacion');


INSERT INTO tecnico_comenta_ticket
(ciTecnico, idTicket, fecha, hora, texto) VALUES
('33333333', 2, '2026-08-17', '10:15:00',
 'Se revisara la instalacion y la configuracion de Java.'),

('66666666', 3, '2026-08-18', '11:15:00',
 'Se detecto perdida de conectividad en el laboratorio.'),

('33333333', 4, '2026-08-18', '13:15:00',
 'Se probo el monitor con un cable alternativo.'),

('44444444', 5, '2026-08-19', '14:45:00',
 'La actualizacion fue instalada correctamente.'),

('33333333', 6, '2026-08-19', '16:15:00',
 'Se esta verificando el punto de red del salon.');