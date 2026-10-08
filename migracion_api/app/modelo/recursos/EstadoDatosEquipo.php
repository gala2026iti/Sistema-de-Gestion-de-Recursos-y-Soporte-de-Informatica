<?php


/**
 * @brief Gestiona el estado de los equipos.
 *
 * Permite activar o desactivar un equipo en la base de datos.
 */
class EstadoDatosEquipo
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
     * @brief Cambia el estado de un equipo.
     *
     * @param string $idEquipo Identificador del equipo.
     * @param bool $activo Nuevo estado del equipo.
     *
     * @return bool true si el equipo fue actualizado;
     *              false si ocurrió un error.
     */
    public function cambiarEstadoEquipo(string $idEquipo, bool $activo): bool
    {
        $sql = "
            UPDATE EQUIPO
            SET activo = :activo
            WHERE id = :idEquipo
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