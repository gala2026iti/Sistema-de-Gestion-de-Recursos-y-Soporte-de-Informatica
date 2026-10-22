/*
    DML OPERATIVO - PRESTAMO
    Fuente: SGRSI/app/modelo/PrestamoDAO.php

    Incluye INSERT, UPDATE y DELETE ejecutados por el sistema.
    No corresponde a la población inicial.
*/

USE gala;


/* Operación 1: registrarPrestamo (1) */
INSERT INTO PRESTAMO (
                    nombrePrestado,
                    ciPrestado,
                    fechaFin,
                    horaFin,
                    devuelto
                )
                VALUES (
                    :nombrePrestado,
                    :ciPrestado,
                    :fechaFin,
                    :horaFin,
                    0
                );

/* Operación 2: registrarPrestamo (2) */
INSERT INTO prestamo_corresponde_equipo (
                    idPrestamo,
                    idEquipo
                )
                VALUES (
                    :idPrestamo,
                    :idEquipo
                );

/* Operación 3: registrarPrestamo (3) */
INSERT INTO tecnico_tramita_prestamo (
                    ciTecnico,
                    idPrestamo,
                    fecha,
                    hora,
                    tipoInteraccion
                )
                VALUES (
                    :ciTecnico,
                    :idPrestamo,
                    CURDATE(),
                    CURTIME(),
                    'prestamo'
                );

/* Operación 4: devolverPrestamo (1) */
UPDATE PRESTAMO
                SET devuelto = 1
                WHERE id = :idPrestamo
                AND devuelto = 0;

/* Operación 5: devolverPrestamo (2) */
INSERT INTO tecnico_tramita_prestamo (
                    ciTecnico,
                    idPrestamo,
                    fecha,
                    hora,
                    tipoInteraccion
                )
                VALUES (
                    :ciTecnico,
                    :idPrestamo,
                    CURDATE(),
                    CURTIME(),
                    'devolucion'
                );
