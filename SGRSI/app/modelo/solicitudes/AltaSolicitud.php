<?php

/**
 * @brief Gestiona el registro de nuevas solicitudes.
 *
 * Registra la solicitud y la relación con el docente que la ingresa
 * dentro de una transacción.
 */
class AltaSolicitud
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
     * @brief Registra una nueva solicitud.
     *
     * Registra la solicitud y su relación con el docente dentro de una transacción.
     *
     * @param string $id Identificador de la solicitud.
     * @param string $asunto Asunto de la solicitud.
     * @param string $descripcion Descripción de la solicitud.
     * @param string $fechaLimite Fecha límite.
     * @param string $horaLimite Hora límite.
     * @param string $ciDocente Cédula del docente que ingresa la solicitud.
     * @param string $fecha Fecha de ingreso.
     * @param string $hora Hora de ingreso.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function registrarSolicitud(
        string $asunto,
        string $descripcion,
        string $fechaLimite,
        string $horaLimite,
        string $ciDocente
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlSolicitud = "
                INSERT INTO SOLICITUD (ciDocente, asunto, descripcion, fechaLimite, horaLimite)
                VALUES (:ciDocente, :asunto, :descripcion, :fechaLimite, :horaLimite)
            ";

            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "ciDocente" => $ciDocente,
                "asunto" => $asunto,
                "descripcion" => $descripcion,
                "fechaLimite" => $fechaLimite,
                "horaLimite" => $horaLimite
            ]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            var_dump($error->getMessage());
            exit;
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }
}