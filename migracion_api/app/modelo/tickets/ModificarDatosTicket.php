<?php


ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * @brief Gestiona la asignación de técnicos a tickets.
 *
 * Permite asociar o desasociar al técnico de la sesión con un ticket.
 */
class ModificarDatosTicket
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
     * @brief Asocia al técnico de la sesión con un ticket.
     *
     * @param string $idTicket Identificador del ticket.
     *
     * @return bool true si la asignación se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function asignarme(
        string $ciTecnico,
        string $idReporte
    ): bool {
        try {
            $this->conexion->beginTransaction();

                $sqlColaborador = "
                INSERT INTO COLABORADOR (ciTecnico, idReporte)
                VALUES (:ciTecnico, :idReporte)
                ";

            $consultaColaborador = $this->conexion->prepare($sqlColaborador);
            $consultaColaborador->execute([
                "ciTecnico" => $ciTecnico,
                "idReporte" => $idReporte
            ]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();

                var_dump($error->getMessage());
                exit;
            }

            return false;
        }
    }
        /**
         * @brief Desasocia al técnico de la sesión de un ticket.
         *
         * @param string $idTicket Identificador del ticket.
         *
         * @return bool true si la desasignación se realizó correctamente;
         *              false si ocurrió un error.
         */
        public function desasignarme(
        string $idReporte
    ): bool {
        try {
            $ciTecnico = $_SESSION['cedula'];
            $this->conexion->beginTransaction();

                $sqlColaborador = "
                DELETE FROM COLABORADOR
                WHERE idReporte = :idReporte AND ciTecnico = :ciTecnico
                ";

            $consultaColaborador = $this->conexion->prepare($sqlColaborador);
            $consultaColaborador->execute([
                "idReporte" => $idReporte,
                "ciTecnico" => $ciTecnico
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