<?php

/**
 * @brief Gestiona el registro de nuevas ubicaciones.
 *
 * Inserta una ubicación con su identificador y tipo.
 */
class AltaUbicacion
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
     * @brief Registra una nueva ubicación.
     *
     * @param string $id Identificador de la ubicación.
     * @param string $tipo Tipo de ubicación.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function registrarUbicacion(
        string $id,
        string $tipo
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlUbicacion = "
                INSERT INTO UBICACION (id, tipo)
                VALUES (:id, :tipo)
            ";

            $consultaEquipo = $this->conexion->prepare($sqlUbicacion);
            $consultaEquipo->execute([
                "id" => $id,
                "tipo" => $tipo
                ]);

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