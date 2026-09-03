<?php

/**
 * @brief Gestiona el registro de nuevos equipos.
 *
 * Registra el equipo y su ubicación asociada dentro de una transacción.
 */
class AltaEquipo
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
     * @brief Registra un nuevo equipo y su ubicación.
     *
     * Utiliza una transacción para registrar el equipo y su relación con la ubicación.
     *
     * @param string $idEquipo Identificador del equipo.
     * @param string $fechaCreacion Fecha de creación.
     * @param string $horaCreacion Hora de creación.
     * @param string $ultimaIntervencion Última intervención registrada.
     * @param bool $activo Estado inicial del equipo.
     * @param string $idUbicacion Identificador de la ubicación.
     * @param string $tipoUbicacion Tipo de ubicación.
     * @param string $posicion Posición del equipo en la ubicación.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function registrarEquipo(
        string $idEquipo,
        string $fechaCreacion,
        string $horaCreacion,
        string $idUbicacion,
        string $tipoUbicacion,
        string $posicion
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlEquipo = "
                INSERT INTO EQUIPO (id, fechaCreacion, horaCreacion, ultimaIntervencion, activo)
                VALUES (:idEquipo, :fechaCreacion, :horaCreacion, NULL, TRUE)
            ";

            $sqlUbicacion = "
                INSERT INTO equipo_reside_ubicacion (idEquipo, idUbicacion, tipoUbicacion, posicion)
                VALUES (:idEquipo, :idUbicacion, :tipoUbicacion, :posicion)
            ";

            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute([
                "idEquipo" => $idEquipo,
                "fechaCreacion" => $fechaCreacion,
                "horaCreacion" => $horaCreacion,
            ]);

            $consultaUbicacion = $this->conexion->prepare($sqlUbicacion);
            $consultaUbicacion->execute([
                "idEquipo" => $idEquipo,
                "idUbicacion" => $idUbicacion,
                "tipoUbicacion" => $tipoUbicacion,
                "posicion" => $posicion
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