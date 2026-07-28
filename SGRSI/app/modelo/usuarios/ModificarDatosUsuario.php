<?php

/**
 * @brief Gestiona la modificación de usuarios.
 *
 * Actualiza los datos principales, el correo, la contraseña opcional
 * y los roles asociados al usuario.
 */
class ModificarDatosUsuario
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;
    /**
     * @brief Construye el acceso a datos.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }
    /**
     * @brief Modifica los datos y roles de un usuario.
     *
     * Actualiza los datos dentro de una transacción y reemplaza las asociaciones de roles.
     *
     * @param string $cedula Cédula del usuario.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string $correo Nuevo correo del usuario.
     * @param array $roles Roles que tendrá el usuario.
     * @param string|null $claveHash Nuevo hash de contraseña o null para conservar la actual.
     *
     * @return bool true si la modificación se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function modificarUsuario(
        string $cedula,
        string $nombre,
        string $correo,
        array $roles,
        ?string $claveHash = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            if ($claveHash !== null) {
                $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre, clave = :clave WHERE ci = :cedula";
                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "clave"  => $claveHash,
                    "cedula" => $cedula
                ];
            } else {
                $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre WHERE ci = :cedula";
                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "cedula" => $cedula
                ];
            }

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute($parametrosUsuario);
            $consultaUsuario->closeCursor(); // Liberamos el cursor por seguridad

            $sqlCorreo = "UPDATE CORREO SET correo = :correo WHERE ci = :cedula";
            $consultaCorreo = $this->conexion->prepare($sqlCorreo);
            $consultaCorreo->execute([
                "correo" => $correo,
                "cedula" => $cedula
            ]);
            $consultaCorreo->closeCursor();

            $tablasRoles = ["ADMINISTRADOR", "TECNICO", "DOCENTE"];

            foreach ($tablasRoles as $tabla) {
                $sqlEliminarRol = "DELETE FROM $tabla WHERE ci = :cedula";
                $consultaEliminarRol = $this->conexion->prepare($sqlEliminarRol);
                $consultaEliminarRol->execute(["cedula" => $cedula]);
                $consultaEliminarRol->closeCursor();
            }

            foreach ($roles as $rol) {
                switch ($rol) {
                    case "administrador":
                        $sqlRol = "INSERT INTO ADMINISTRADOR (ci) VALUES (:cedula)";
                        break;

                    case "tecnico":
                        $sqlRol = "INSERT INTO TECNICO (ci) VALUES (:cedula)";
                        break;

                    case "docente":
                        $sqlRol = "INSERT INTO DOCENTE (ci) VALUES (:cedula)";
                        break;

                    default:
                        if ($this->conexion->inTransaction()) {
                            $this->conexion->rollBack();
                        }
                        return false;
                }

                $consultaRol = $this->conexion->prepare($sqlRol);
                $consultaRol->execute(["cedula" => $cedula]);
                $consultaRol->closeCursor();
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log("Error en modificarUsuario: " . $error->getMessage());
            return false;
        }
    }
}