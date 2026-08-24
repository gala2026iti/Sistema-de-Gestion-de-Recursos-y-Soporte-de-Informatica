USE sgrsi;

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

INSERT INTO ROL (ci, rol) VALUES
('11111111', 'administrador'),
('22222222', 'docente'),
('33333333', 'tecnico'),
('44444444', 'administrador'),
('44444444', 'tecnico'),
('55555555', 'docente'),
('66666666', 'tecnico'),
('66666666', 'docente'),
('77777777', 'administrador'),
('88888888', 'docente');

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
(ciAdministrador, ciUsuario, fecha, hora, tipoInteraccion) VALUES
('11111111', '55555555', '01/08/2026', '09:15', 'desactivacion'),
('11111111', '66666666', '03/08/2026', '10:30', 'modificacion de roles'),
('44444444', '88888888', '05/08/2026', '14:20', 'modificacion de datos'),
('11111111', '77777777', '08/08/2026', '11:00', 'desactivacion');

INSERT INTO UBICACION (id, tipo) VALUES
(1, 'laboratorio'),
(2, 'laboratorio'),
(101, 'salon'),
(102, 'salon'),
(0, 'prestamo');

INSERT INTO EQUIPO
(id, fechaCreacion, horaCreacion, ultimaIntervencion, activo) VALUES
(1, '10/03/2025', '08:00', '10/08/2026', TRUE),
(2, '10/03/2025', '08:05', '12/08/2026', TRUE),
(3, '15/04/2025', '09:00', '14/08/2026', TRUE),
(4, '15/04/2025', '09:05', '15/08/2026', TRUE),
(5, '20/05/2025', '10:00', '17/08/2026', FALSE),
(6, '20/05/2025', '10:05', NULL, TRUE),
(7, '05/06/2025', '11:00', '20/08/2026', TRUE),
(8, '05/06/2025', '11:05', '21/08/2026', TRUE);

INSERT INTO equipo_reside_ubicacion
(idEquipo, idUbicacion, tipoUbicacion, posicion) VALUES
(1, 1, 'laboratorio', 'PC-01'),
(2, 1, 'laboratorio', 'PC-02'),
(3, 2, 'laboratorio', 'PC-01'),
(4, 101, 'salon', 'PC-01'),
(5, 102, 'salon', 'PC-01'),
(7, 0, 'prestamo', 'P-01'),
(8, 0, 'prestamo', 'P-02');

/* El equipo 6 queda sin ubicación para disponer de un caso "sin asignar". */

INSERT INTO administrador_maneja_equipo
(ciAdministrador, idEquipo, fecha, hora, tipoInteraccion) VALUES
('11111111', 1, '10/08/2026', '09:00', 'actualizacion'),
('11111111', 5, '17/08/2026', '12:10', 'desactivacion'),
('44444444', 7, '20/08/2026', '15:00', 'cambio de ubicacion'),
('44444444', 8, '21/08/2026', '15:20', 'cambio de ubicacion');

INSERT INTO administrador_controla_ubicacion
(ciAdministrador, idUbicacion, tipoUbicacion, fecha, hora, tipoInteraccion) VALUES
('11111111', 1, 'laboratorio', '01/07/2026', '08:30', 'alta'),
('11111111', 2, 'laboratorio', '01/07/2026', '08:35', 'alta'),
('44444444', 101, 'salon', '02/07/2026', '10:00', 'alta'),
('44444444', 102, 'salon', '02/07/2026', '10:05', 'alta'),
('11111111', 0, 'prestamo', '03/07/2026', '11:00', 'alta');

INSERT INTO TICKET
(id, tipo, asunto, descripcion, gravedad, estado, fechaCreacion, horaCreacion, justificacion) VALUES
(1, 'hardware', 'Teclado no responde', 'El teclado del equipo deja de responder de forma intermitente.', 'ligera', 'pendiente', '18/08/2026', '08:15', NULL),
(2, 'software', 'NetBeans no inicia', 'La aplicacion NetBeans se cierra inmediatamente al abrirla.', 'media', 'en proceso', '18/08/2026', '10:25', NULL),
(3, 'red', 'Sin conexion a Internet', 'Los equipos del laboratorio no pueden acceder a la red.', 'grave', 'pendiente', '19/08/2026', '09:40', NULL),
(4, 'hardware', 'Monitor sin imagen', 'El monitor enciende pero no recibe señal del equipo.', 'grave', 'resuelto', '19/08/2026', '13:10', 'Se reemplazo el cable de video defectuoso.'),
(5, 'software', 'Navegador desactualizado', 'El navegador instalado no permite acceder correctamente a algunas plataformas.', 'ligera', 'resuelto', '20/08/2026', '08:50', 'Se actualizo el navegador a la version disponible.'),
(6, 'red', 'Conexion inestable', 'La conexion de red presenta cortes durante las clases.', 'media', 'en proceso', '21/08/2026', '11:30', NULL);

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
(ciTecnico, idTicket, fecha, hora, tipoInteraccion) VALUES
('33333333', 2, '18/08/2026', '11:00', 'asignacion'),
('44444444', 2, '18/08/2026', '11:15', 'cambio de estado'),
('66666666', 3, '19/08/2026', '10:00', 'asignacion'),
('33333333', 4, '19/08/2026', '14:00', 'resolucion'),
('44444444', 5, '20/08/2026', '09:30', 'resolucion'),
('33333333', 6, '21/08/2026', '12:00', 'asignacion');

INSERT INTO tecnico_comenta_ticket
(ciTecnico, idTicket, fecha, hora, texto) VALUES
('33333333', 2, '18/08/2026', '11:05', 'Se revisara la instalacion y la configuracion de Java.'),
('66666666', 3, '19/08/2026', '10:10', 'Se detecto perdida de conectividad en el laboratorio.'),
('33333333', 4, '19/08/2026', '13:50', 'Se probo el monitor con un cable alternativo.'),
('44444444', 5, '20/08/2026', '09:20', 'La actualizacion fue instalada correctamente.'),
('33333333', 6, '21/08/2026', '12:10', 'Se esta verificando el punto de red del salon.');

INSERT INTO SOLICITUD
(id, asunto, descripcion, fechaLimite, horaLimite, finalizada) VALUES
(1, 'Instalar NetBeans', 'Instalar NetBeans en los equipos del Laboratorio 1.', '28/08/2026', '18:00', FALSE),
(2, 'Preparar laboratorio', 'Verificar que todos los equipos del Laboratorio 2 esten operativos.', '26/08/2026', '12:00', TRUE),
(3, 'Actualizar software', 'Actualizar el navegador web de los equipos utilizados por el grupo.', '02/09/2026', '17:00', FALSE),
(4, 'Configurar proyector', 'Comprobar y configurar el proyector antes de la actividad.', '22/08/2026', '09:00', TRUE);

INSERT INTO docente_ingresa_solicitud
(ciDocente, idSolicitud, fecha, hora) VALUES
('22222222', 1, '20/08/2026', '09:00'),
('88888888', 2, '20/08/2026', '10:30'),
('66666666', 3, '21/08/2026', '08:45'),
('22222222', 4, '21/08/2026', '12:15');

INSERT INTO tecnico_finaliza_solicitud
(ciTecnico, idSolicitud, fecha, hora) VALUES
('33333333', 2, '23/08/2026', '11:20'),
('44444444', 4, '22/08/2026', '08:30');

INSERT INTO PRESTAMO
(id, nombrePrestado, ciPrestado, fechaFin, horaFin, devuelto) VALUES
(1, 'Martin Rodriguez', '40123456', '25/08/2026', '17:00', FALSE),
(2, 'Lucia Fernandez', '41234567', '26/08/2026', '16:30', FALSE),
(3, 'Sofia Martinez', '42345678', '20/08/2026', '15:00', TRUE),
(4, 'Nicolas Pereira', '43456789', '21/08/2026', '18:00', TRUE);

INSERT INTO tecnico_tramita_prestamo
(ciTecnico, idPrestamo, fecha, hora, tipoInteraccion) VALUES
('33333333', 1, '24/08/2026', '08:30', 'prestamo'),
('44444444', 2, '24/08/2026', '09:15', 'prestamo'),
('66666666', 3, '19/08/2026', '10:00', 'prestamo'),
('33333333', 3, '20/08/2026', '14:45', 'devolucion'),
('44444444', 4, '20/08/2026', '11:30', 'prestamo'),
('44444444', 4, '21/08/2026', '17:40', 'devolucion');

INSERT INTO prestamo_corresponde_equipo (idPrestamo, idEquipo) VALUES
(1, 7),
(2, 8),
(3, 7),
(4, 8);