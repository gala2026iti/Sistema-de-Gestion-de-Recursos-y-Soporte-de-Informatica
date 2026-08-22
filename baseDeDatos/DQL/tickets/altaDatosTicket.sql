INSERT INTO USUARIO (ci, nombre, clave, activo)
VALUES (:cedula, :nombre, :clave, TRUE);

INSERT INTO ADMINISTRADOR (ci)
VALUES (:cedula);

INSERT INTO TECNICO (ci)
VALUES (:cedula);

INSERT INTO DOCENTE (ci)
VALUES (:cedula);


INSERT INTO CORREO (ci, correo)
VALUES (:cedula,:correo)

