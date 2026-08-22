<?php

/**
 * @brief Gestiona la modificación de los datos de los usuarios.
 *
 * Permite actualizar los datos principales, la contraseña y los
 * roles asociados a un usuario.
 */
class ModificarDatosUsuario
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

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