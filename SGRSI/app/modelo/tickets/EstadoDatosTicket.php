<?php

/**
 * @brief Gestiona el estado de los usuarios.
 *
 * Permite activar o desactivar usuarios mediante el campo
 * activo de la tabla USUARIO.
 */
class EstadoDatosTicket
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
    public function cambiarEstadoTicket(string $id, string $estado): bool
    {
        $sql = "
            UPDATE TICKET
            SET estado = :estado
            WHERE id = :id
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "estado" => $estado,
                "id" => $id
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }

        public function cambiarGravedadTicket(string $id, string $gravedad): bool
    {
        $sql = "
            UPDATE TICKET
            SET gravedad = :gravedad 
            WHERE id = :id
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "gravedad" => $gravedad,
                "id" => $id
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}