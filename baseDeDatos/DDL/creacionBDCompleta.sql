/*

EN ESTA VERSION SE ADAPTA LA BD A LA NORMALIZACION SGRSI EN 3FN.
SE MANTIENE LA ESTRUCTURA Y LOS DATOS DE PRUEBA DE LA VERSION ANTERIOR,
REALIZANDO UNICAMENTE LOS CAMBIOS NECESARIOS PARA QUE LAS TABLAS Y
RELACIONES COINCIDAN CON LA NORMALIZACION.

LA INFORMACION UTILIZADA EN ESTA DEMOSTRACION ES PURAMENTE FICTICIA.

!!! ADVERTENCIA !!!

EL EQUIPO CONTENEDOR DE LA BD DEBE TENER LA FECHA Y HORA
BIEN CONFIGURADAS PARA LA ZONA HORARIA UTC -03:00.
*/

/* CONFIGURACION DE ZONA HORARIA */
SET time_zone = '-03:00';

/* CREACION DE NUEVA BASE DE DATOS */
DROP DATABASE IF EXISTS sgrsi;
CREATE DATABASE IF NOT EXISTS sgrsi;
USE sgrsi;

/* 
   SECCION USUARIOS
    */

CREATE TABLE IF NOT EXISTS USUARIO (
    ci CHAR(8) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_usuario PRIMARY KEY (ci)
);

CREATE TABLE IF NOT EXISTS CORREO (
    ci CHAR(8) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    CONSTRAINT pk_correo PRIMARY KEY (ci, correo),
    CONSTRAINT fk_correo_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS DOCENTE (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_docente PRIMARY KEY (ci),
    CONSTRAINT fk_docente_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ADMINISTRADOR (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_administrador PRIMARY KEY (ci),
    CONSTRAINT fk_administrador_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS TECNICO (
    ci CHAR(8) NOT NULL,
    CONSTRAINT pk_tecnico PRIMARY KEY (ci),
    CONSTRAINT fk_tecnico_usuario FOREIGN KEY (ci)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS administrador_modifica_usuario (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdmin CHAR(8) NOT NULL,
    ciUsuario CHAR(8) NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_admin_modifica_usuario PRIMARY KEY (id),
    CONSTRAINT fk_amu_admin FOREIGN KEY (ciAdmin)
        REFERENCES ADMINISTRADOR (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_amu_usuario FOREIGN KEY (ciUsuario)
        REFERENCES USUARIO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* 
   SECCION SOLICITUDES
    */

CREATE TABLE IF NOT EXISTS SOLICITUD (
    id INT AUTO_INCREMENT NOT NULL,
    ciDocente CHAR(8) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    fechaLimite DATE NOT NULL,
    horaLimite TIME NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    finalizada BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_solicitud PRIMARY KEY (id, ciDocente),
    CONSTRAINT fk_solicitud_docente FOREIGN KEY (ciDocente)
        REFERENCES DOCENTE (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_finaliza_solicitud (
    ciTecnico CHAR(8) NOT NULL,
    idSolicitud INT NOT NULL,
    ciDocente CHAR(8) NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    CONSTRAINT pk_tecnico_finaliza_solicitud PRIMARY KEY (ciTecnico, idSolicitud, ciDocente),
    CONSTRAINT fk_tfs_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tfs_solicitud FOREIGN KEY (idSolicitud, ciDocente)
        REFERENCES SOLICITUD (id, ciDocente)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tfs_docente FOREIGN KEY (ciDocente)
        REFERENCES DOCENTE (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* 
   SECCION RECURSOS
    */

CREATE TABLE IF NOT EXISTS UBICACION (
    id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    CONSTRAINT pk_ubicacion PRIMARY KEY (id, tipo)
);

CREATE TABLE IF NOT EXISTS EQUIPO (
    id INT NOT NULL,
    fechaCreacion DATE NOT NULL DEFAULT (CURRENT_DATE),
    horaCreacion TIME NOT NULL DEFAULT (CURRENT_TIME),
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
    ciAdmin CHAR(8) NOT NULL,
    idEquipo INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_admin_maneja_equipo PRIMARY KEY (id),
    CONSTRAINT fk_ame_admin FOREIGN KEY (ciAdmin)
        REFERENCES ADMINISTRADOR (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ame_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS administrador_controla_ubicacion (
    id INT AUTO_INCREMENT NOT NULL,
    ciAdmin CHAR(8) NOT NULL,
    idUbicacion INT NOT NULL,
    tipoUbicacion VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_admin_controla_ubicacion PRIMARY KEY (id),
    CONSTRAINT fk_acu_admin FOREIGN KEY (ciAdmin)
        REFERENCES ADMINISTRADOR (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_acu_ubicacion FOREIGN KEY (idUbicacion, tipoUbicacion)
        REFERENCES UBICACION (id, tipo)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* 
   SECCION PRESTAMOS
    */

CREATE TABLE IF NOT EXISTS PRESTAMO (
    id INT AUTO_INCREMENT NOT NULL,
    idEquipo INT NOT NULL,
    nombrePrestado VARCHAR(100) NOT NULL,
    ciPrestado CHAR(8) NOT NULL,
    fechaFin DATE NOT NULL,
    horaFin TIME NOT NULL,
    devuelto BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_prestamo PRIMARY KEY (id, idEquipo),
    CONSTRAINT fk_prestamo_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_tramita_prestamo (
    id INT AUTO_INCREMENT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idPrestamo INT NOT NULL,
    idEquipo INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tecnico_tramita_prestamo PRIMARY KEY (id),
    CONSTRAINT fk_ttp_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ttp_prestamo FOREIGN KEY (idPrestamo, idEquipo)
        REFERENCES PRESTAMO (id, idEquipo)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ttp_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* 
   SECCION REPORTES Y TICKETS
    */

CREATE TABLE IF NOT EXISTS REPORTE (
    id INT AUTO_INCREMENT NOT NULL,
    CONSTRAINT pk_reporte PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS TICKET (
    id INT AUTO_INCREMENT NOT NULL,
    idEquipo INT NOT NULL,
    idReporte INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    gravedad VARCHAR(20) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    fechaCreacion DATE NOT NULL DEFAULT (CURRENT_DATE),
    horaCreacion TIME NOT NULL DEFAULT (CURRENT_TIME),
    justificacion VARCHAR(255) NULL,

    CONSTRAINT pk_ticket
        PRIMARY KEY (id, idEquipo, idReporte),

    CONSTRAINT fk_ticket_equipo
        FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id),

    CONSTRAINT fk_ticket_reporte
        FOREIGN KEY (idReporte)
        REFERENCES REPORTE (id)

);

CREATE TABLE IF NOT EXISTS COLABORADOR (
    idTicket INT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idEquipo INT NOT NULL,
    idReporte INT NOT NULL,
    CONSTRAINT pk_colaborador PRIMARY KEY (idTicket, ciTecnico, idEquipo),
    CONSTRAINT fk_colab_ticket FOREIGN KEY (idTicket, idEquipo, idReporte)
        REFERENCES TICKET (id, idEquipo, idReporte)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_colab_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS docente_reporta_reporte (
    ciDocente CHAR(8) NOT NULL,
    idReporte INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    CONSTRAINT pk_docente_reporta_reporte PRIMARY KEY (ciDocente, idReporte),
    CONSTRAINT fk_drr_docente FOREIGN KEY (ciDocente)
        REFERENCES DOCENTE (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_drr_reporte FOREIGN KEY (idReporte)
        REFERENCES REPORTE (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_comunica_reporte (
    idTecnico CHAR(8) NOT NULL,
    idReporte INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    CONSTRAINT pk_tecnico_comunica_reporte PRIMARY KEY (idTecnico, idReporte),
    CONSTRAINT fk_tcr_tecnico FOREIGN KEY (idTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tcr_reporte FOREIGN KEY (idReporte)
        REFERENCES REPORTE (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS equipo_genera_ticket (
    idEquipo INT NOT NULL,
    idTicket INT NOT NULL,
    idReporte INT NOT NULL,
    CONSTRAINT pk_egt PRIMARY KEY (idEquipo, idTicket, idReporte),
    CONSTRAINT fk_egt_equipo FOREIGN KEY (idEquipo)
        REFERENCES EQUIPO (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_egt_ticket FOREIGN KEY (idTicket, idEquipo, idReporte)
        REFERENCES TICKET (id, idEquipo, idReporte)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_gestiona_ticket (
    id INT AUTO_INCREMENT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    idReporte INT NOT NULL,
    idEquipo INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    tipoInteraccion VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tecnico_gestiona_ticket PRIMARY KEY (id),
    CONSTRAINT fk_tgt_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tgt_ticket FOREIGN KEY (idTicket, idEquipo, idReporte)
        REFERENCES TICKET (id, idEquipo, idReporte)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tgt_reporte FOREIGN KEY (idReporte)
        REFERENCES REPORTE (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS tecnico_comenta_ticket (
    id INT AUTO_INCREMENT NOT NULL,
    ciTecnico CHAR(8) NOT NULL,
    idTicket INT NOT NULL,
    idReporte INT NOT NULL,
    idEquipo INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    hora TIME NOT NULL DEFAULT (CURRENT_TIME),
    texto TEXT NOT NULL,
    CONSTRAINT pk_tecnico_comenta_ticket PRIMARY KEY (id),
    CONSTRAINT fk_tct_tecnico FOREIGN KEY (ciTecnico)
        REFERENCES TECNICO (ci)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tct_ticket FOREIGN KEY (idTicket, idEquipo, idReporte)
        REFERENCES TICKET (id, idEquipo, idReporte)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tct_reporte FOREIGN KEY (idReporte)
        REFERENCES REPORTE (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* 
   POBLACION TABLAS
    */

INSERT INTO USUARIO (ci, nombre, clave) VALUES
('11111111', 'Administrador Principal', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('22222222', 'Docente Principal', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('33333333', 'Tecnico Principal', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('44444444', 'Leandro', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('55555555', 'Docente Inactivo', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('66666666', 'Tecnico Docente', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('77777777', 'Administrador Inactivo', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('88888888', 'Docente Secundario', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS');

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
('11111111'), ('44444444'), ('77777777');

INSERT INTO TECNICO (ci) VALUES
('33333333'), ('44444444'), ('66666666');

INSERT INTO DOCENTE (ci) VALUES
('22222222'), ('55555555'), ('66666666'), ('88888888');

INSERT INTO administrador_modifica_usuario
(ciAdmin, ciUsuario, tipoInteraccion) VALUES
('11111111', '55555555', 'desactivacion'),
('11111111', '66666666', 'modificacion'),
('44444444', '88888888', 'modificacion'),
('11111111', '77777777', 'desactivacion');

INSERT INTO UBICACION (id, tipo) VALUES
(1, 'laboratorio'),
(2, 'laboratorio'),
(101, 'salon'),
(102, 'salon'),
(0, 'prestamo');

INSERT INTO EQUIPO (id) VALUES
(1), (2), (3), (4), (5), (6), (7), (8);

INSERT INTO equipo_reside_ubicacion
(idEquipo, idUbicacion, tipoUbicacion, posicion) VALUES
(1, 1, 'laboratorio', '1'),
(2, 1, 'laboratorio', '2'),
(3, 2, 'laboratorio', '1'),
(4, 101, 'salon', '1'),
(5, 102, 'salon', '1'),
(7, 0, 'prestamo', '1'),
(8, 0, 'prestamo', '2');

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
('44444444', 101, 'salon', 'creacion'),
('44444444', 102, 'salon', 'creacion'),
('11111111', 0, 'prestamo', 'desactivacion');

/* Los reportes se separan de los tickets según la normalización. */
INSERT INTO REPORTE (id) VALUES
(1),
(2),
(3),
(4),
(5),
(6);

INSERT INTO TICKET
(id, idEquipo, idReporte, tipo, asunto, descripcion, gravedad, estado, justificacion) VALUES
(1, 1, 1, 'hardware', 'Teclado no responde', 'El teclado del equipo deja de responder de forma intermitente.', 'ligera', 'pendiente', NULL),
(2, 2, 2, 'software', 'NetBeans no inicia', 'La aplicacion NetBeans se cierra inmediatamente al abrirla.', 'media', 'en proceso', NULL),
(3, 3, 3, 'red', 'Sin conexion a Internet', 'Los equipos del laboratorio no pueden acceder a la red.', 'grave', 'pendiente', NULL),
(4, 4, 4, 'hardware', 'Monitor sin imagen', 'El monitor enciende pero no recibe señal del equipo.', 'grave', 'resuelto', 'Se reemplazo el cable de video defectuoso.'),
(5, 5, 5, 'software', 'Navegador desactualizado', 'El navegador instalado no permite acceder correctamente a algunas plataformas.', 'ligera', 'resuelto', 'Se actualizo el navegador a la version disponible.'),
(6, 6, 6, 'red', 'Conexion inestable', 'La conexion de red presenta cortes durante las clases.', 'media', 'en proceso', NULL);

INSERT INTO docente_reporta_reporte
(ciDocente, idReporte) VALUES
('22222222', 1),
('22222222', 2),
('88888888', 3),
('66666666', 4),
('88888888', 5),
('22222222', 6);

INSERT INTO equipo_genera_ticket
(idEquipo, idTicket, idReporte) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6);

INSERT INTO COLABORADOR (idTicket, ciTecnico, idEquipo, idReporte) VALUES
(2, '33333333', 2, 2),
(2, '44444444', 2, 2),
(3, '66666666', 3, 3),
(4, '33333333', 4, 4),
(5, '44444444', 5, 5),
(6, '33333333', 6, 6),
(6, '66666666', 6, 6);

INSERT INTO tecnico_gestiona_ticket
(ciTecnico, idTicket, idReporte, idEquipo, tipoInteraccion) VALUES
('33333333', 2, 2, 2, 'creacion'),
('44444444', 2, 2, 2, 'modificacion'),
('66666666', 3, 3, 3, 'comentario'),
('33333333', 4, 4, 4, 'comentario'),
('44444444', 5, 5, 5, 'desasignacion'),
('33333333', 6, 6, 6, 'asignacion');

INSERT INTO tecnico_comenta_ticket
(ciTecnico, idTicket, idReporte, idEquipo, texto) VALUES
('33333333', 2, 2, 2, 'Se revisara la instalacion y la configuracion de Java.'),
('66666666', 3, 3, 3, 'Se detecto perdida de conectividad en el laboratorio.'),
('33333333', 4, 4, 4, 'Se probo el monitor con un cable alternativo.'),
('44444444', 5, 5, 5, 'La actualizacion fue instalada correctamente.'),
('33333333', 6, 6, 6, 'Se esta verificando el punto de red del salon.');

INSERT INTO SOLICITUD
(id, ciDocente, asunto, descripcion, fechaLimite, horaLimite) VALUES
(1, '22222222', 'Instalar NetBeans', 'Instalar NetBeans en los equipos del Laboratorio 1.', '2026-08-18', '18:00'),
(2, '88888888', 'Preparar laboratorio', 'Verificar que todos los equipos del Laboratorio 2 esten operativos.', '2026-08-26', '12:00'),
(3, '66666666', 'Actualizar software', 'Actualizar el navegador web de los equipos utilizados por el grupo.', '2026-09-02', '17:00'),
(4, '22222222', 'Configurar proyector', 'Comprobar y configurar el proyector antes de la actividad.', '2026-08-22', '09:00');

INSERT INTO tecnico_finaliza_solicitud
(ciTecnico, idSolicitud, ciDocente) VALUES
('33333333', 2, '88888888'),
('44444444', 4, '22222222');

INSERT INTO PRESTAMO
(id, idEquipo, nombrePrestado, ciPrestado, fechaFin, horaFin) VALUES
(1, 7, 'Martin Rodriguez', '40123456', '2026-08-25', '17:00'),
(2, 8, 'Lucia Fernandez', '41234567', '2026-08-26', '16:30'),
(3, 7, 'Sofia Martinez', '42345678', '2026-08-20', '15:00'),
(4, 8, 'Nicolas Pereira', '43456789', '2026-08-21', '18:00');

INSERT INTO tecnico_tramita_prestamo
(ciTecnico, idPrestamo, idEquipo, tipoInteraccion) VALUES
('33333333', 1, 7, 'prestamo'),
('44444444', 2, 8, 'prestamo'),
('66666666', 3, 7, 'prestamo'),
('33333333', 3, 7, 'devolucion'),
('44444444', 4, 8, 'prestamo'),
('44444444', 4, 8, 'devolucion');
