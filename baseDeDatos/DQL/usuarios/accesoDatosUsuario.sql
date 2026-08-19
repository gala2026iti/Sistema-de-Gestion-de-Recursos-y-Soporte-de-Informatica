/*
    Consultas utilizadas por AccesoDatosUsuario.php.

    Los valores indicados mediante :parametro son reemplazados
    desde PHP utilizando consultas preparadas con PDO.
*/


/*
    Busca un usuario por su cédula para realizar la autenticación
    y determina los roles que posee.

    :cedula corresponde a la cédula ingresada por el usuario.
*/
SELECT
    u.ci AS cedula,
    u.clave AS claveHash,
    u.activo AS sesionActiva,

    CASE
        WHEN a.ci IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS administrador,

    CASE
        WHEN t.ci IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS tecnico,

    CASE
        WHEN d.ci IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS docente

FROM USUARIO AS u

LEFT JOIN ADMINISTRADOR AS a
    ON a.ci = u.ci

LEFT JOIN TECNICO AS t
    ON t.ci = u.ci

LEFT JOIN DOCENTE AS d
    ON d.ci = u.ci

WHERE u.ci = :cedula;


/*
    Obtiene los usuarios registrados junto con sus roles.

    La cláusula WHERE se construye dinámicamente desde PHP
    según los filtros seleccionados.

    Posibles condiciones:
        u.activo = :activo
        a.ci IS NOT NULL
        t.ci IS NOT NULL
        d.ci IS NOT NULL

    :activo puede valer TRUE o FALSE.
*/
SELECT
    u.ci AS cedula,
    u.nombre,
    u.correo,
    u.activo,

    CASE
        WHEN a.ci IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS administrador,

    CASE
        WHEN t.ci IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS tecnico,

    CASE
        WHEN d.ci IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS docente

FROM USUARIO AS u

LEFT JOIN ADMINISTRADOR AS a
    ON a.ci = u.ci

LEFT JOIN TECNICO AS t
    ON t.ci = u.ci

LEFT JOIN DOCENTE AS d
    ON d.ci = u.ci

-- WHERE se agrega desde PHP cuando existen filtros.

ORDER BY u.ci;