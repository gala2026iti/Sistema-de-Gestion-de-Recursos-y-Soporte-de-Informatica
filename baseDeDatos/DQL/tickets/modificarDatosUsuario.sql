
UPDATE USUARIO
SET nombre = :nombre
WHERE ci = :cedula;

UPDATE USUARIO
SET nombre = :nombre,
    clave = :clave
WHERE ci = :cedula;

UPDATE CORREO
SET correo = :correo
WHERE ci = :cedula;

DELETE FROM ADMINISTRADOR
WHERE ci = :cedula;

DELETE FROM TECNICO
WHERE ci = :cedula;

DELETE FROM DOCENTE
WHERE ci = :cedula;

INSERT INTO ADMINISTRADOR (ci)
VALUES (:cedula);

INSERT INTO TECNICO (ci)
VALUES (:cedula);

INSERT INTO DOCENTE (ci)
VALUES (:cedula);