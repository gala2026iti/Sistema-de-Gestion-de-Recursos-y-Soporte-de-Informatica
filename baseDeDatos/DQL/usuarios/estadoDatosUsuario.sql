/*
    Consulta utilizada por EstadoDatosUsuario.php.
*/


/*
    Activa o desactiva un usuario sin eliminarlo.

    :cedula identifica al usuario.
    :activo vale TRUE para activar y FALSE para desactivar.
*/
UPDATE USUARIO
SET activo = :activo
WHERE ci = :cedula;