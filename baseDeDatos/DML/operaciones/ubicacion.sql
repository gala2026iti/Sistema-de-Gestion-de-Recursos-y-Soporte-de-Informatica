/*
    DML OPERATIVO - UBICACION
    Fuente: SGRSI/app/modelo/UbicacionDAO.php

    Incluye INSERT, UPDATE y DELETE ejecutados por el sistema.
    No corresponde a la población inicial.
*/

USE gala;


/* Operación 1: registrarUbicacion (1) */
UPDATE UBICACION
                    SET activo = TRUE
                    WHERE id = :id
                    AND tipo = :tipo;

/* Operación 2: registrarUbicacion (2) */
INSERT INTO UBICACION (
                        id,
                        tipo,
                        activo
                    )
                    VALUES (
                        :id,
                        :tipo,
                        TRUE
                    );

/* Operación 3: eliminarUbicacion (1) */
DELETE FROM equipo_reside_ubicacion
                WHERE idUbicacion = :id
                AND tipoUbicacion = :tipo;

/* Operación 4: eliminarUbicacion (2) */
UPDATE UBICACION
                SET activo = FALSE
                WHERE id = :id
                AND tipo = :tipo
                AND activo = TRUE;

/* Operación 5: registrarHistorialUbicacion */
INSERT INTO administrador_controla_ubicacion (
                ciAdmin,
                idUbicacion,
                tipoUbicacion,
                tipoInteraccion
            )
            VALUES (
                :ciAdmin,
                :idUbicacion,
                :tipoUbicacion,
                :tipoInteraccion
            );
