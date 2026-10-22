/*
    DQL - UBICACION
    Fuente: SGRSI/app/modelo/UbicacionDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: listarUbicaciones */
SELECT
                u.id,
                u.tipo
            FROM UBICACION AS u
            WHERE u.activo = TRUE
            AND u.tipo IN ('laboratorio', 'taller')
            ORDER BY
                u.tipo,
                u.id;

/* Consulta 2: obtenerIdInactivo */
SELECT id
            FROM UBICACION
            WHERE tipo = :tipo
            AND activo = FALSE
            ORDER BY id ASC
            LIMIT 1;

/* Consulta 3: obtenerIdLibre */
SELECT id
                FROM UBICACION
                WHERE id = :id
                AND tipo = :tipo;
