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
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function registrarPrestamo(
        string $ciTecnico,
        string $nombrePrestado,
        string $ciPrestado,
        string $fechaFin,
        string $horaFin,
        string $idEquipo
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlPrestamo = "
                INSERT INTO PRESTAMO (nombrePrestado, ciPrestado, fechaFin, horaFin)
                VALUES (:nombrePrestado, :ciPrestado, :fechaFin, :horaFin)
            ";

            $sqlTecnico = "
                INSERT INTO tecnico_tramita_prestamo (ciTecnico, idPrestamo, tipoInteraccion)
                VALUES (:ciTecnico, :idPrestamo, :tipoInteraccion)
            ";

            $sqlEquipo = "
                INSERT INTO prestamo_corresponde_equipo (idPrestamo, idEquipo)
                VALUES (:idPrestamo, :idEquipo)
            ";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "nombrePrestado" => $nombrePrestado,
                "ciPrestado" => $ciPrestado,
                "fechaFin" => $fechaFin,
                "horaFin" => $horaFin
            ]);

            $idPrestamo = $this->conexion->lastInsertId();

            $consultaTecnico = $this->conexion->prepare($sqlTecnico);
            $consultaTecnico->execute([
                "ciTecnico" => $ciTecnico,
                "idPrestamo" => $idPrestamo,
                "tipoInteraccion" => "creacion"
            ]);

            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute([
                "idPrestamo" => $idPrestamo,
                "idEquipo" => $idEquipo
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();

                var_dump($error->getMessage());
                exit();
            }

            return false;
        }
    }
}