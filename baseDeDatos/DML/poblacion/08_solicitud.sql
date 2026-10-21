/*
    POBLACION: SOLICITUD
*/

SET time_zone = '-03:00';
USE gala;

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
