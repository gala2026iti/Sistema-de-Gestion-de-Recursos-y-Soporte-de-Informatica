/*
    DML OPERATIVO - EQUIPO
    Fuente: SGRSI/app/modelo/EquipoDAO.php

    Incluye INSERT, UPDATE y DELETE ejecutados por el sistema.
    No corresponde a la población inicial.
*/

USE gala;

/* Operación 1: registrarEquipo (1) */
INSERT INTO EQUIPO (id,fechaCreacion,horaCreacion)
VALUES (:idEquipo,CURDATE(),CURTIME());

/* Operación 2: registrarEquipo (2) */
INSERT INTO equipo_reside_ubicacion ( idEquipo, idUbicacion, tipoUbicacion, posicion)
                    VALUES (
                         :idEquipo,
                        :idUbicacion,
                        :tipoUbicacion,
                        :posicion
                    );

/* Operación 3: modificarUbicacionEquipo (1) */
DELETE FROM equipo_reside_ubicacion
                WHERE idEquipo = :idEquipo;

/* Operación 4: modificarUbicacionEquipo (2) */
INSERT INTO equipo_reside_ubicacion (
                        idEquipo,
                        idUbicacion,
                        tipoUbicacion,
                        posicion
                    )
                    VALUES (
                        :idEquipo,
                        :idUbicacion,
                        :tipoUbicacion,
                        :posicion
                    );

/* Operación 5: cambiarEstadoEquipo */
UPDATE EQUIPO
            SET activo = :activo
            WHERE id = :idEquipo;

/* Operación 6: registrarHistorialEquipo */
INSERT INTO administrador_maneja_equipo ( ciAdmin, idEquipo,  tipoInteraccion )
        VALUES ( :ciAdmin, :idEquipo,:tipoInteraccion);
