<?php

/**
 * @brief Gestiona el estado de los usuarios.
 *
 * Permite activar o desactivar usuarios mediante el campo activo.
 */
class EstadoDatosUsuario
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
     * @brief Cambia el estado de un usuario.
     *
     * @param string $cedula Cédula del usuario.
     * @param bool $activo Nuevo estado del usuario.
     *
     * @return bool true si el usuario fue actualizado;
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
                "activo" => $activo ? 1 : 0, /* Esta cosita de 1:0 es porque activo se formatea a '""', por lo que se hace una comparación para que si se guarde como bool, ya que es lo que la base de datos quiere */
                "cedula" => $cedula
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}