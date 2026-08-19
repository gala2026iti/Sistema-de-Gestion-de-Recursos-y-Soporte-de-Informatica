<?php

/**
 * @brief Gestiona la modificación de los datos de los usuarios.
 *
 * Permite actualizar los datos principales, la contraseña y los
 * roles asociados a un usuario.
 */
class ModificarDatosUsuario
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye el acceso para modificar usuarios.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * @brief Modifica los datos principales y los roles de un usuario.
     *
     * Actualiza el nombre, correo y roles del usuario. La contraseña
     * solamente se modifica si se proporciona un nuevo hash.
     *
     * @param string $cedula Cédula del usuario que se modificará.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string $correo Nuevo correo del usuario.
     * @param array $roles Roles que tendrá el usuario.
     * @param string|null $claveHash Nuevo hash de contraseña o null
     *        para conservar la contraseña actual.
     *
     * @return bool true si la modificación se realizó correctamente;
     *              false si ocurrió algún error.
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

            if ($claveHash === null) {
                $sqlUsuario = "
                    UPDATE USUARIO
                    SET nombre = :nombre, correo = :correo
                    WHERE ci = :cedula
                ";

                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "correo" => $correo,
                    "cedula" => $cedula
                ];
            } else {
                $sqlUsuario = "
                    UPDATE USUARIO
                    SET nombre = :nombre, correo = :correo, clave = :clave
                    WHERE ci = :cedula
                ";

                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "correo" => $correo,
                    "clave" => $claveHash,
                    "cedula" => $cedula
                ];
            }

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute($parametrosUsuario);

            /*
             * Eliminamos los roles actuales para luego
             * registrar los seleccionados.
             */
            $tablasRoles = ["ADMINISTRADOR", "TECNICO", "DOCENTE"];

            foreach ($tablasRoles as $tabla) {
                $sqlEliminarRol = "
                    DELETE FROM $tabla
                    WHERE ci = :cedula
                ";

                $consultaEliminarRol = $this->conexion->prepare($sqlEliminarRol);
                $consultaEliminarRol->execute([
                    "cedula" => $cedula
                ]);
            }

            foreach ($roles as $rol) {
                switch ($rol) {
                    case "administrador":
                        $sqlRol = "
                            INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    case "tecnico":
                        $sqlRol = "
                            INSERT INTO TECNICO (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    case "docente":
                        $sqlRol = "
                            INSERT INTO DOCENTE (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    default:
                        $this->conexion->rollBack();
                        return false;
                }

                $consultaRol = $this->conexion->prepare($sqlRol);
                $consultaRol->execute([
                    "cedula" => $cedula
                ]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }
}