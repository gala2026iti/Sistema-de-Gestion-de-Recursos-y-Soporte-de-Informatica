<?php

/**
 * @brief Gestiona la eliminación de ubicaciones.
 *
 * Desvincula los equipos asociados y elimina la ubicación indicada.
 */
class EliminarUbicacion
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
     * @brief Elimina las relaciones de equipos con una ubicación.
     *
     * @param string $id Identificador de la ubicación.
     * @param string $tipo Tipo de ubicación.
     */

    private function eliminarEquiposDeUbicacion(string $id, string $tipo): void
    {
        $sql = "
        DELETE FROM equipo_reside_ubicacion WHERE idUbicacion = :id AND tipoUbicacion = :tipo
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "id" => $id,
            "tipo" => $tipo
        ]);
    }    /**
     * @brief Elimina una ubicación.
     *
     * Antes de eliminarla, desvincula los equipos asociados a la ubicación.
     *
     * @param string $id Identificador de la ubicación.
     * @param string $tipo Tipo de ubicación.
     *
     * @return bool true si la ubicación fue eliminada;
     *              false si ocurrió un error.
     */

    public function eliminarUbicacion(string $id, string $tipo): bool
    {
        $this->eliminarEquiposDeUbicacion($id, $tipo);

        $sql = "
            DELETE FROM UBICACION WHERE id = :id AND tipo = :tipo
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "id" => $id,
                "tipo" => $tipo
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }

        
    }
}
