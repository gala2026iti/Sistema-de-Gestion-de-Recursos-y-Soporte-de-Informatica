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

private function obtenerPosicionLibrePrestamo(): string
{
    $sql = "
        SELECT posicion
        FROM equipo_reside_ubicacion
        WHERE tipoUbicacion = 'prestamo'
          AND idUbicacion = 0
        ORDER BY posicion
    ";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();

    $posicionesOcupadas = $consulta->fetchAll(PDO::FETCH_COLUMN);

    $posicion = 1;

    while (in_array($posicion, $posicionesOcupadas)) {
        $posicion++;
    }

    $consulta->closeCursor();

    return (string)$posicion;
}

    /* TOFIX : LAS SELECCIONES NO ENCUENTRAN NINGUNA FILA COINCIDENTE, VERIFICAR VARIABLES A FILTRAR, EQUIPOS DE "NINGUNO" ES EL ÚNICO QUE FUNCIONA, DE SALONES Y DE PRESTAMO TIRAN ERROR :() */
    public function modificarUbicacionEquipo(string $idEquipo, string $idUbicacion, string $tipoUbicacion, string $posicion, string $tipoUbicacionOrigen): bool
    {
        try{
        
        $this->conexion->beginTransaction();
        if($tipoUbicacionOrigen === "ninguna"){
            $sql = "
            INSERT INTO equipo_reside_ubicacion (idEquipo, idUbicacion, tipoUbicacion, posicion) VALUES 
                (:idEquipo, :idUbicacion, :tipoUbicacion, :posicion)
            ";

            if($tipoUbicacion === "prestamo"){
                 $posicion = $this->obtenerPosicionLibrePrestamo();
                 $idUbicacion = 0;
            }

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
            "idEquipo" => $idEquipo,
            "idUbicacion" => $idUbicacion,
            "tipoUbicacion" => $tipoUbicacion,
            "posicion" => $posicion
        ]);



        } else {
        if($tipoUbicacion === "ninguna") {
            $sql = "
            DELETE FROM equipo_reside_ubicacion WHERE idEquipo = :idEquipo
            ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
        "idEquipo" => $idEquipo
        ]);

        } else {
        
        if ($tipoUbicacion === "prestamo"){
             $posicion = $this->obtenerPosicionLibrePrestamo();
             $idUbicacion = "0";
             }

            $sql = "
            UPDATE equipo_reside_ubicacion SET idUbicacion = :idUbicacion, tipoUbicacion = :tipoUbicacion, posicion = :posicion WHERE idEquipo = :idEquipo
            ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "idEquipo" => $idEquipo,
            "idUbicacion" => $idUbicacion,
            "tipoUbicacion" => $tipoUbicacion,
            "posicion" => $posicion
        ]);

        }
        }
        
            $consulta->closeCursor();
            $this->conexion->commit();
            return true;
        
        } catch(PDOException $error) {
                if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log("Error al modificar la ubicación del equipo: " . $error->getMessage());
            return false;
        }

    }

}
