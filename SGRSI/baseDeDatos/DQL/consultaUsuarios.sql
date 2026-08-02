SELECT U.ci, U.nombre, U.correo, U.clave, U.activo, GROUP_CONCAT(R.rol) AS roles
FROM USUARIO U
LEFT JOIN ROL R ON U.ci = R.ci
WHERE U.ci = '44444444'
GROUP BY U.ci;