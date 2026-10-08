<?php

require_once __DIR__ . "/../Ubicacion.php";

/**
 * @brief Gestiona las consultas relacionadas con las ubicaciones.
 *
 * Permite obtener las ubicaciones registradas en el sistema.
 */
class CargarUbicaciones
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
 * @brief Obtiene las ubicaciones registradas.
 *
 * @return array Lista de ubicaciones encontradas.
 */
public function listarUbicaciones(): array
    {
        $sql = "
            SELECT
                u.id,
                u.tipo
            FROM UBICACION AS u
        ";



        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $ubicaciones = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $ubicaciones;
    }
}
