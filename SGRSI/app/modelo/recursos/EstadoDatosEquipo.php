<?php

/**
 * @brief Gestiona el estado de los usuarios.
 *
 * Permite activar o desactivar usuarios mediante el campo
 * activo de la tabla USUARIO.
 */
class EstadoDatosEquipo
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
    public function cambiarEstadoEquipo(string $idEquipo, bool $activo): bool
    {
        $sql = "
            UPDATE EQUIPO
            SET activo = :activo
            WHERE ci = :idEquipo
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "activo" => $activo ? 1 : 0, /* Esta cosita de 1:0 es porque activo se formatea a '""', por lo que se hace una comparación para que si se guarde como bool, ya que es lo que la base de datos quiere */
                "idEquipo" => $idEquipo
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}