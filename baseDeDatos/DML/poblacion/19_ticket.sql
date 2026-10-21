/*
    POBLACION: TICKET
*/

SET time_zone = '-03:00';
USE gala;

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

INSERT INTO TICKET
(id, tipo, asunto, persona, descripcion, gravedad, estado,
 fechaCreacion, horaCreacion, justificacion) VALUES
(7, 'hardware',
 'Bateria no carga',
 'Estudiante 5',
 'El equipo destinado a préstamo no carga correctamente.',
 'media', 'pendiente',
 '2026-08-20', '12:00:00', NULL);
