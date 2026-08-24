<?php

/**
 * @brief Gestiona el estado y la gravedad de los tickets.
 *
 * Permite actualizar estos valores sin modificar el resto de sus datos.
 */
class EstadoDatosTicket
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
     * @brief Cambia el estado de un ticket.
     *
     * @param string $id Identificador del ticket.
     * @param string $estado Nuevo estado del ticket.
     *
     * @return bool true si el ticket fue actualizado;
     *              false si ocurrió un error.
     */
    public function cambiarEstadoTicket(string $id, string $estado): bool
    {
        $sql = "
            UPDATE TICKET
            SET estado = :estado
            WHERE id = :id
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "estado" => $estado,
                "id" => $id
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
        /**
         * @brief Cambia la gravedad de un ticket.
         *
         * @param string $id Identificador del ticket.
         * @param string $gravedad Nueva gravedad del ticket.
         *
         * @return bool true si el ticket fue actualizado;
         *              false si ocurrió un error.
         */
        public function cambiarGravedadTicket(string $id, string $gravedad): bool
    {
        $sql = "
            UPDATE TICKET
            SET gravedad = :gravedad 
            WHERE id = :id
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "gravedad" => $gravedad,
                "id" => $id
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}