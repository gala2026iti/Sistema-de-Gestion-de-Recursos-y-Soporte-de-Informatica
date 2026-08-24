<?php

/**
 * @brief Gestiona la ubicación asociada a un equipo.
 *
 * Permite actualizar la ubicación y el tipo de ubicación de un equipo.
 */
class ModificarUbicacionEquipo
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
     * @brief Modifica la ubicación asociada a un equipo.
     *
     * @param string $idEquipo Identificador del equipo.
     * @param string $idUbicacion Nuevo identificador de ubicación.
     * @param string $tipoUbicacion Nuevo tipo de ubicación.
     */

    public function modificarUbicacionEquipo(string $idEquipo, string $idUbicacion, string $tipoUbicacion): void
    {
        $sql = "
        UPDATE equipo_reside_ubicacion SET idUbicacion = :idUbicacion, tipoUbicacion = :tipoUbicacion WHERE idEquipo = :idEquipo
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "idEquipo" => $idEquipo,
            "idUbicacion" => $idUbicacion,
            "tipoUbicacion" => $tipoUbicacion
        ]);
    }

}
