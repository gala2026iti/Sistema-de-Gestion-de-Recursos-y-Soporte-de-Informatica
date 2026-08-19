<?php

/**
 * @brief Gestiona el estado de los usuarios.
 *
 * Permite activar o desactivar usuarios mediante el campo
 * activo de la tabla USUARIO.
 */
class EstadoDatosUsuario
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye el acceso para modificar el estado de usuarios.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * @brief Cambia el estado de un usuario.
     *
     * Actualiza el campo activo de la tabla USUARIO para activar
     * o desactivar al usuario sin modificar sus roles.
     *
     * @param string $cedula Cédula del usuario cuyo estado se modificará.
     * @param bool $activo Nuevo estado del usuario.
     *
     * @return bool true si el usuario fue actualizado correctamente;
     *              false si ocurrió un error.
     */
    public function cambiarEstadoUsuario(string $cedula, bool $activo): bool
    {
        $sql = "
            UPDATE USUARIO
            SET activo = :activo
            WHERE ci = :cedula
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "activo" => $activo,
                "cedula" => $cedula
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}