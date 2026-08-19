/*
    Consultas utilizadas por ModificarDatosUsuario.php.

    Todas las modificaciones se realizan dentro de una transacción.
*/


/*
    Modifica el nombre y correo conservando la contraseña actual.

    Se utiliza cuando no se proporciona una nueva contraseña.
*/
UPDATE USUARIO
SET nombre = :nombre,
    correo = :correo
WHERE ci = :cedula;


/*
    Modifica nombre, correo y contraseña.

    :clave contiene el nuevo hash de contraseña.
    Se utiliza únicamente cuando se proporciona una nueva contraseña.
*/
UPDATE USUARIO
SET nombre = :nombre,
    correo = :correo,
    clave = :clave
WHERE ci = :cedula;


/*
    Elimina las asociaciones actuales con los roles antes
    de registrar nuevamente los roles seleccionados.
*/
DELETE FROM ADMINISTRADOR
WHERE ci = :cedula;

DELETE FROM TECNICO
WHERE ci = :cedula;

DELETE FROM DOCENTE
WHERE ci = :cedula;


/*
    Vuelve a asociar al usuario con los roles seleccionados.
    Solamente se ejecutan las consultas correspondientes
    a los roles recibidos desde PHP.
*/
INSERT INTO ADMINISTRADOR (ci)
VALUES (:cedula);

INSERT INTO TECNICO (ci)
VALUES (:cedula);

INSERT INTO DOCENTE (ci)
VALUES (:cedula);