/*
    DML OPERATIVO - USUARIO
    Fuente: SGRSI/app/modelo/UsuarioDAO.php

    Incluye INSERT, UPDATE y DELETE ejecutados por el sistema.
    No corresponde a la población inicial.
*/

USE gala;


/* Operación 1: registrarUsuario (1) */
INSERT INTO USUARIO (
                    ci,
                    nombre,
                    clave,
                    activo
                )
                VALUES (
                    :cedula,
                    :nombre,
                    :clave,
                    TRUE
                );

/* Operación 2: registrarUsuario (2) */
INSERT INTO CORREO (
                    ci,
                    correo
                )
                VALUES (
                    :cedula,
                    :correo
                );

/* Operación 3: registrarUsuario (3) */
INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula);

/* Operación 4: registrarUsuario (4) */
INSERT INTO TECNICO (ci)
                            VALUES (:cedula);

/* Operación 5: registrarUsuario (5) */
INSERT INTO DOCENTE (ci)
                            VALUES (:cedula);

/* Operación 6: cambiarEstadoUsuario */
UPDATE USUARIO
            SET activo = :activo
            WHERE ci = :cedula;

/* Operación 7: modificarUsuario (1) */
UPDATE USUARIO
                    SET
                        nombre = :nombre,
                        clave = :clave
                    WHERE ci = :cedula;

/* Operación 8: modificarUsuario (2) */
UPDATE USUARIO
                    SET nombre = :nombre
                    WHERE ci = :cedula;

/* Operación 9: modificarUsuario (3) */
UPDATE CORREO
                SET correo = :correo
                WHERE ci = :cedula;

/* Operación 10: modificarUsuario - eliminación de roles actuales */
DELETE FROM ADMINISTRADOR
                    WHERE ci = :cedula;
DELETE FROM TECNICO
                    WHERE ci = :cedula;
DELETE FROM DOCENTE
                    WHERE ci = :cedula;

/* Operación 11: modificarUsuario (5) */
INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula);

/* Operación 12: modificarUsuario (6) */
INSERT INTO TECNICO (ci)
                            VALUES (:cedula);

/* Operación 13: modificarUsuario (7) */
INSERT INTO DOCENTE (ci)
                            VALUES (:cedula);

/* Operación 14: registrarHistorialUsuario */
INSERT INTO administrador_modifica_usuario (
            ciAdmin,
            ciUsuario,
            tipoInteraccion
        )
        VALUES (
            :ciAdmin,
            :ciUsuario,
            :tipoInteraccion
        );
