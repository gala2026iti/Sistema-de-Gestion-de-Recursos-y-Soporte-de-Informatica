<?php
/**
 * @brief Gestiona el registro de nuevos préstamos.
 *
 * Registra el préstamo y la intervención del técnico dentro de una transacción.
 */
class AltaPrestamo
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
     * @brief Registra un nuevo préstamo.
     *
     * Registra el préstamo y la intervención del técnico dentro de una transacción.
     *
     * @param string $idPrestamo Identificador del préstamo.
     * @param string $ciTecnico Cédula del técnico que tramita el préstamo.
     * @param string $nombrePrestado Nombre de la persona a la que se presta.
     * @param string $ciPrestado Cédula de la persona a la que se presta.
     * @param string $fechaFin Fecha prevista de devolución.
     * @param string $horaFin Hora prevista de devolución.
     * @param string $fecha Fecha de la intervención.
     * @param string $hora Hora de la intervención.
     * @param string $tipoInteraccion Tipo de interacción registrada.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function registrarPrestamo(
        string $idPrestamo,
        string $ciTecnico,
        string $nombrePrestado,
        string $ciPrestado,
        string $fechaFin,
        string $horaFin,
        string $fecha,
        string $hora,
        string $tipoInteraccion
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlPrestamo = "
                INSERT INTO PRESTAMO (id, nombrePrestado, ciPrestado, fechaFin, horaFin, devuelto)
                VALUES (:idPrestamo, :nombrePrestado, :ciPrestado, :fechaFin, :horaFin, FALSE)
            ";

            $sqlTecnico = "
                INSERT INTO tecnico_tramita_prestamo (id, ciTecnico, idPrestamo, fecha, hora, tipoInteraccion)
                VALUES (:id, :ciTecnico, :idPrestamo, :fecha, :hora, :tipoInteraccion)
            ";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "idPrestamo" => $idPrestamo,
                "nombrePrestado" => $nombrePrestado,
                "ciPrestado" => $ciPrestado,
                "fechaFin" => $fechaFin,
                "horaFin" => $horaFin
            ]);

            $consultaTecnico = $this->conexion->prepare($sqlTecnico);
            $consultaTecnico->execute([
                "ciTecnico" => $ciTecnico,
                "idPrestamo" => $idPrestamo,
                "fecha" => $fecha,
                "hora" => $hora,
                "tipoInteraccion" => $tipoInteraccion
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