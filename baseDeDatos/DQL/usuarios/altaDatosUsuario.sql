/*
    Consultas utilizadas por AltaDatosUsuario.php.

    El registro del usuario y sus roles se realiza dentro
    de una transacción.
*/


/*
    Registra un nuevo usuario.

    :cedula corresponde a la cédula.
    :nombre corresponde al nombre.
    :correo corresponde al correo electrónico.
    :clave contiene el hash generado con password_hash().
*/
INSERT INTO USUARIO (ci, nombre, correo, clave, activo)
VALUES (:cedula, :nombre, :correo, :clave, TRUE);


/*
    Asocia al usuario con el rol administrador.
    Se ejecuta solamente si dicho rol fue seleccionado.
*/
INSERT INTO ADMINISTRADOR (ci)
VALUES (:cedula);


/*
    Asocia al usuario con el rol técnico.
    Se ejecuta solamente si dicho rol fue seleccionado.
*/
INSERT INTO TECNICO (ci)
VALUES (:cedula);


/*
    Asocia al usuario con el rol docente.
    Se ejecuta solamente si dicho rol fue seleccionado.
*/
INSERT INTO DOCENTE (ci)
VALUES (:cedula);