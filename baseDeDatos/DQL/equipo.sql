/*
    DQL - EQUIPO
    Fuente: SGRSI/app/modelo/EquipoDAO.php

    Estas son las consultas SELECT utilizadas por el DAO actual.
    Los filtros marcados como opcionales son agregados dinámicamente
    por PHP según los parámetros recibidos.
*/

USE gala;


/* Consulta 1: listarEquipos (1) */
SELECT
                    e.id AS idEquipo,
                    e.fechaCreacion,
                    e.horaCreacion,
                    e.ultimaIntervencion,
                    e.activo,
                    eru.idUbicacion,
                    eru.tipoUbicacion,
                    eru.posicion,
                    COUNT(DISTINCT eugt.idTicket) AS totalIncidencias

                FROM EQUIPO AS e

                LEFT JOIN equipo_reside_ubicacion AS eru
                    ON e.id = eru.idEquipo

                LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
                    ON eugt.idEquipo = e.id;

/* Consulta 2: listarEquipos (2) */
SELECT
                    e.id AS idEquipo,
                    e.ultimaIntervencion,
                    e.activo,
                    eru.idUbicacion,
                    eru.tipoUbicacion,
                    eru.posicion

                FROM EQUIPO AS e

                LEFT JOIN equipo_reside_ubicacion AS eru
                    ON e.id = eru.idEquipo;

/* Filtros opcionales utilizados por este método:
   - e.activo = :activo
   - e.activo = TRUE
   - eru.tipoUbicacion = :tipoUbicacion
   - eru.idUbicacion = :ubicacion
   - e.id = :id
*/
/* Cláusulas de orden/agrupación que el DAO agrega según el caso:
   - ORDER BY e.ultimaIntervencion DESC
   - ORDER BY e.ultimaIntervencion ASC
   - ORDER BY totalIncidencias DESC
   - ORDER BY e.id ASC
   - ORDER BY totalIncidencias ASC
*/

/* Consulta 3: obtenerPosicionLibrePrestamo */
SELECT posicion
            FROM equipo_reside_ubicacion
            WHERE tipoUbicacion = 'prestamo'
            AND idUbicacion = 0
            ORDER BY posicion;
