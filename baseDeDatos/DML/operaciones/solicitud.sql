/*
    DML OPERATIVO - SOLICITUD
    Fuente: SGRSI/app/modelo/SolicitudDAO.php

    Incluye INSERT, UPDATE y DELETE ejecutados por el sistema.
    No corresponde a la población inicial.
*/

USE gala;


/* Operación 1: registrarSolicitud (1) */
INSERT INTO SOLICITUD (
                    asunto,
                    descripcion,
                    fechaLimite,
                    horaLimite,
                    finalizada
                )
                VALUES (
                    :asunto,
                    :descripcion,
                    :fechaLimite,
                    :horaLimite,
                    0
                );

/* Operación 2: registrarSolicitud (2) */
INSERT INTO docente_ingresa_solicitud (
                    ciDocente,
                    idSolicitud,
                    fecha,
                    hora
                )
                VALUES (
                    :ciDocente,
                    :idSolicitud,
                    CURDATE(),
                    CURTIME()
                );

/* Operación 3: finalizarSolicitud (1) */
UPDATE SOLICITUD
                SET finalizada = 1
                WHERE id = :idSolicitud
                AND finalizada = 0;

/* Operación 4: finalizarSolicitud (2) */
INSERT INTO tecnico_finaliza_solicitud (
                    ciTecnico,
                    idSolicitud,
                    fecha,
                    hora
                )
                VALUES (
                    :ciTecnico,
                    :idSolicitud,
                    CURDATE(),
                    CURTIME()
                );
