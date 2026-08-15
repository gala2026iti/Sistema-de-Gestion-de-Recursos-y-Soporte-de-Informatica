<?php

/**
 * @brief Gestiona el registro de nuevos usuarios.
 *
 * Se encarga de insertar un usuario en la tabla USUARIO y de asociarlo
 * con uno o varios roles mediante las tablas ADMINISTRADOR, TECNICO
 * y DOCENTE.
 */
class AltaDatosUsuario
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye un objeto AltaDatosUsuario.
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
     * Utiliza una transacción para garantizar que el usuario y todos
     * sus roles sean registrados correctamente.
     *
     * Si ocurre un error durante el proceso, se deshacen todas las
     * inserciones realizadas.
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
                INSERT INTO USUARIO
                    (ci, nombre, correo, clave, activo)
                VALUES
                    (:cedula, :nombre, :correo, :clave, TRUE)
            ";

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute([
                "cedula" => $cedula,
                "nombre" => $nombre,
                "correo" => $correo,
                "clave" => $claveHash
            ]);

            /*
             * Un usuario puede pertenecer a varios roles,
             * por lo que se procesa cada rol seleccionado.
             */
            foreach ($roles as $rol) {

                switch (strtolower($rol)) {

                    case "administrador":

                        $sqlAdministrador = "
                            INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula)
                        ";

                        $consultaRol =
                            $this->conexion->prepare($sqlAdministrador);

                        break;

                    case "tecnico":

                        $sqlTecnico = "
                            INSERT INTO TECNICO (ci)
                            VALUES (:cedula)
                        ";

                        $consultaRol =
                            $this->conexion->prepare($sqlTecnico);

                        break;

                    case "docente":

                        $sqlDocente = "
                            INSERT INTO DOCENTE (ci)
                            VALUES (:cedula)
                        ";

                        $consultaRol =
                            $this->conexion->prepare($sqlDocente);

                        break;

                    default:

                        $this->conexion->rollBack();

                        return false;
                }

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