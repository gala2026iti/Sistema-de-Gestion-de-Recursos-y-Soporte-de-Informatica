USE sgrsi;

/*
    ============================================================================
    DOCUMENTACIÓN DE CONSULTAS DQL (Data Query Language) - SGRSI
    ============================================================================
    Ubicación en el proyecto: app/modelo/AccesoDatosUsuario.php
    
    Nota: Las consultas se presentan con valores de prueba (ej: '11111111').
    En PHP (PDO), el valor literal se reemplaza dinámicamente utilizando 
    consultas preparadas con marcadores de parámetros (:cedula).
*/

-- ============================================================================
-- 1. CONSULTA DE AUTENTICACIÓN / BÚSQUEDA DE USUARIO POR CÉDULA
-- ============================================================================

/* Consulta 1.1: Búsqueda individual de usuario y determinación de roles.
   Se ejecuta en AccesoDatosUsuario::buscarUsuario(string $cedula).
   
   Técnica del Docente:
   - Realiza un LEFT JOIN con las subclases de roles (ADMINISTRADOR, TECNICO, DOCENTE).
   - Evalúa si el usuario pertenece a cada rol mediante la expresión CASE WHEN.
   - Retorna en una sola tupla el estado de la cuenta, clave hash y los tres roles.
*/
SELECT
    u.cedula,
    u.claveHash,
    u.sesionActiva,

    CASE
        WHEN a.cedula IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS administrador,

    CASE
        WHEN t.cedula IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS tecnico,

    CASE
        WHEN d.cedula IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS docente

FROM USUARIO AS u

LEFT JOIN ADMINISTRADOR AS a
    ON a.cedula = u.cedula

LEFT JOIN TECNICO AS t
    ON t.cedula = u.cedula

LEFT JOIN DOCENTE AS d
    ON d.cedula = u.cedula

WHERE u.cedula = '11111111';


-- ============================================================================
-- 2. CONSULTA DE LISTADO GENERAL DE USUARIOS
-- ============================================================================

/* Consulta 2.1: Obtiene todos los usuarios del sistema proyectando sus roles.
   Se ejecuta en AccesoDatosUsuario::listarUsuarios().
   
   Utiliza el mismo principio de LEFT JOIN + CASE WHEN para armar la tabla 
   de administración en el frontend.
*/
SELECT
    u.cedula,
    u.nombre,
    u.correo,
    u.sesionActiva,

    CASE
        WHEN a.cedula IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS administrador,

    CASE
        WHEN t.cedula IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS tecnico,

    CASE
        WHEN d.cedula IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS docente

FROM USUARIO AS u

LEFT JOIN ADMINISTRADOR AS a
    ON a.cedula = u.cedula

LEFT JOIN TECNICO AS t
    ON t.cedula = u.cedula

LEFT JOIN DOCENTE AS d
    ON d.cedula = u.cedula;