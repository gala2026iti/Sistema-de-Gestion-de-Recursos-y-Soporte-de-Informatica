/*
    DQL - USUARIO
    Fuente: SGRSI/app/modelo/UsuarioDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: buscarUsuario */
SELECT
                u.ci AS cedula,
                u.nombre,
                u.clave AS claveHash,
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

            WHERE u.ci = :cedula;

/* Consulta 2: listarUsuarios */
SELECT
                u.ci AS cedula,
                u.nombre,
                c.correo,
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

            LEFT JOIN CORREO AS c
                ON c.ci = u.ci

            LEFT JOIN ADMINISTRADOR AS a
                ON a.ci = u.ci

            LEFT JOIN TECNICO AS t
                ON t.ci = u.ci

            LEFT JOIN DOCENTE AS d
                ON d.ci = u.ci;

/* Filtros opcionales utilizados por este método:
   - u.activo = :activo
   - a.ci IS NOT NULL
   - t.ci IS NOT NULL
   - d.ci IS NOT NULL
*/
