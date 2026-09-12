USE sgrsi;

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

INSERT INTO administrador_modifica_usuario
(ciAdministrador, ciUsuario, tipoInteraccion) VALUES
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

INSERT INTO EQUIPO
(id) VALUES
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
(1, 1, 'laboratorio', '1'),
(2, 1, 'laboratorio', '2'),
(3, 2, 'laboratorio', '1'),
(4, 101, 'salon', '1'),
(5, 102, 'salon', '1'),
(7, 0, 'prestamo', '1'),
(8, 0, 'prestamo', '2');

/* El equipo 6 queda sin ubicación para disponer de un caso "sin asignar". */

INSERT INTO administrador_maneja_equipo
(ciAdministrador, idEquipo, tipoInteraccion) VALUES
('11111111', 1, 'creacion'),
('11111111', 5, 'desactivacion'),
('44444444', 7, 'modificacion'),
('44444444', 8, 'activacion');

INSERT INTO administrador_controla_ubicacion
(ciAdministrador, idUbicacion, tipoUbicacion, tipoInteraccion) VALUES
('11111111', 1, 'laboratorio', 'creacion'),
('11111111', 2, 'laboratorio', 'activacion'),
('44444444', 101, 'salon', 'creacion'),
('44444444', 102, 'salon', 'creacion'),
('11111111', 0, 'prestamo', 'desactivacion');

INSERT INTO TICKET
(id, tipo, asunto, descripcion, gravedad, estado, justificacion) VALUES
(1, 'hardware', 'Teclado no responde', 'El teclado del equipo deja de responder de forma intermitente.', 'ligera', 'pendiente', NULL),
(2, 'software', 'NetBeans no inicia', 'La aplicacion NetBeans se cierra inmediatamente al abrirla.', 'media', 'en proceso', NULL),
(3, 'red', 'Sin conexion a Internet', 'Los equipos del laboratorio no pueden acceder a la red.', 'grave', 'pendiente', NULL),
(4, 'hardware', 'Monitor sin imagen', 'El monitor enciende pero no recibe señal del equipo.', 'grave', 'resuelto', 'Se reemplazo el cable de video defectuoso.'),
(5, 'software', 'Navegador desactualizado', 'El navegador instalado no permite acceder correctamente a algunas plataformas.', 'ligera', 'resuelto', 'Se actualizo el navegador a la version disponible.'),
(6, 'red', 'Conexion inestable', 'La conexion de red presenta cortes durante las clases.', 'media', 'en proceso', NULL);

INSERT INTO docente_reporta_ticket (ciDocente, idTicket) VALUES
('22222222', 1),
('22222222', 2),
('88888888', 3),
('66666666', 4),
('88888888', 5),
('22222222', 6);

INSERT INTO equipo_ubicacion_genera_ticket
(idEquipo, idUbicacion, tipoUbicacion, idTicket) VALUES
(1, 1, 'laboratorio', 1),
(2, 1, 'laboratorio', 2),
(3, 2, 'laboratorio', 3),
(4, 101, 'salon', 4),
(5, 102, 'salon', 5),
(4, 101, 'salon', 6);

INSERT INTO COLABORADOR (idTicket, ciTecnico) VALUES
(2, '33333333'),
(2, '44444444'),
(3, '66666666'),
(4, '33333333'),
(5, '44444444'),
(6, '33333333'),
(6, '66666666');

INSERT INTO tecnico_gestiona_ticket
(ciTecnico, idTicket, tipoInteraccion) VALUES
('33333333', 2, 'creacion'),
('44444444', 2, 'modificacion'),
('66666666', 3, 'comentario'),
('33333333', 4, 'comentario'),
('44444444', 5, 'desasignacion'),
('33333333', 6, 'asignacion');

INSERT INTO tecnico_comenta_ticket
(ciTecnico, idTicket, texto) VALUES
('33333333', 2, 'Se revisara la instalacion y la configuracion de Java.'),
('66666666', 3, 'Se detecto perdida de conectividad en el laboratorio.'),
('33333333', 4, 'Se probo el monitor con un cable alternativo.'),
('44444444', 5, 'La actualizacion fue instalada correctamente.'),
('33333333', 6, 'Se esta verificando el punto de red del salon.');

INSERT INTO SOLICITUD
(asunto, descripcion, fechaLimite, horaLimite) VALUES
('Instalar NetBeans', 'Instalar NetBeans en los equipos del Laboratorio 1.', "2026-08-18", "18:00"),
('Preparar laboratorio', 'Verificar que todos los equipos del Laboratorio 2 esten operativos.', "2026-08-26", "12:00"),
('Actualizar software', 'Actualizar el navegador web de los equipos utilizados por el grupo.', "2026-09-02", "17:00"),
('Configurar proyector', 'Comprobar y configurar el proyector antes de la actividad.', "2026-08-22", "09:00");

INSERT INTO docente_ingresa_solicitud
(ciDocente, idSolicitud) VALUES
('22222222', 1),
('88888888', 2),
('66666666', 3),
('22222222', 4);

INSERT INTO tecnico_finaliza_solicitud
(ciTecnico, idSolicitud) VALUES
('33333333', 2),
('44444444', 4);

INSERT INTO PRESTAMO
(id, nombrePrestado, ciPrestado, fechaFin, horaFin) VALUES
(1, 'Martin Rodriguez', '40123456', "2026-08-25", "17:00"),
(2, 'Lucia Fernandez', '41234567', "2026-08-26", "16:30"),
(3, 'Sofia Martinez', '42345678', "2026-08-20", "15:00"),
(4, 'Nicolas Pereira', '43456789', "2026-08-21", "18:00");

INSERT INTO tecnico_tramita_prestamo
(ciTecnico, idPrestamo, tipoInteraccion) VALUES
('33333333', 1, 'prestamo'),
('44444444', 2, 'prestamo'),
('66666666', 3, 'prestamo'),
('33333333', 3, 'devolucion'),
('44444444', 4, 'prestamo'),
('44444444', 4, 'devolucion');

INSERT INTO prestamo_corresponde_equipo (idPrestamo, idEquipo) VALUES
(1, 7),
(2, 8),
(3, 7),
(4, 8);