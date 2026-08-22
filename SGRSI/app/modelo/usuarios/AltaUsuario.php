<?php

/**
 * @brief Gestiona el registro de nuevos usuarios.
 *
 * Inserta usuarios en la tabla USUARIO y los asocia con uno o varios
 * roles mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */
class AltaDatosUsuario
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye el acceso para registrar usuarios.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * @brief Registra un nuevo usuario y sus roles.
     *
     * Utiliza una transacción para garantizar que el usuario y sus
     * roles se registren como una única operación.
     *
     * @param string $cedula Cédula de identidad del usuario.
     * @param string $nombre Nombre del usuario.
     * @param string $correo Correo electrónico del usuario.
     * @param string $claveHash Contraseña almacenada mediante hash.
     * @param array $roles Roles que tendrá el usuario.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió algún error.
     */
    public function registrarUsuario(
        string $cedula,
        string $nombre,
        string $correo,
        string $claveHash,
        array $roles
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlUsuario = "
                INSERT INTO USUARIO (ci, nombre, clave, activo)
                VALUES (:cedula, :nombre, :clave, TRUE)
            ";

            $sqlCorreo = "
                INSERT INTO CORREO (ci, correo)
                VALUES (:cedula, :correo)
            ";

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute([
                "cedula" => $cedula,
                "nombre" => $nombre,
                "clave" => $claveHash
            ]);

            $consultaCorreo = $this->conexion->prepare($sqlCorreo);
            $consultaCorreo->execute([
                "cedula" => $cedula,
                "correo" => $correo,
            ]);

            /*
             * Se registra al usuario en cada uno
             * de los roles seleccionados.
             */
            foreach ($roles as $rol) {
                switch (strtolower($rol)) {
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