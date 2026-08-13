
USE sgrsi;

/*
    Espacio donde se documentan las consultas SQL utilizadas en la aplicación.
    
    Nota: Las consultas contienen valores fijos de prueba (ej: '44444444'). 
    En PHP, estos valores son reemplazados dinámicamente por marcadores 
    de parámetros (:cedula) utilizando PDO prepared statements.
*/

-- ============================================================================
-- 1. CONSULTAS UTILIZADAS EN AccesoDatosUsuario.php
-- ============================================================================

/* Consulta 1.1: Obtiene los datos principales del usuario para el inicio de sesión.
    Se ejecuta al buscar un usuario por su cédula en AccesoDatosUsuario::buscarUsuarioPorCedula().
    En PHP, '44444444' se reemplaza por el marcador :cedula.
*/
SELECT ci, nombre, correo, clave, activo 
FROM USUARIO 
WHERE ci = '44444444';


/* Consulta 1.2: Obtiene el listado de roles asociados a la cédula consultada.
    Se ejecuta inmediatamente después de la consulta 1.1 para verificar los permisos del usuario.
    En PHP, '44444444' se reemplaza por el marcador :cedula.
*/
SELECT rol 
FROM ROL 
WHERE ci = '44444444';


-- ============================================================================
-- 2. CONSULTA UNIFICADA ALTERNATIVA (OPTIMIZADA CON JOIN Y GROUP_CONCAT)
-- ============================================================================

/* Consulta 2.1: Consulta optimizada en una sola transacción.
    Combina los datos del usuario y concatena sus roles asociados ('administrador,tecnico')
    utilizando LEFT JOIN y GROUP_CONCAT.
    En PHP, '44444444' se reemplaza por el marcador :cedula.
*/
SELECT 
    U.ci, 
    U.nombre, 
    U.correo, 
    U.clave, 
    U.activo, 
    GROUP_CONCAT(R.rol) AS roles
FROM USUARIO U
LEFT JOIN ROL R ON U.ci = R.ci
WHERE U.ci = '44444444'
GROUP BY U.ci;