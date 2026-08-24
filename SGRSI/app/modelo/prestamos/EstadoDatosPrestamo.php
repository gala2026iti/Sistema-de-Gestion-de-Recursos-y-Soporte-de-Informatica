<?php

/**
 * @brief Gestiona el estado de los préstamos.
 *
 * Permite actualizar si un préstamo fue devuelto.
 */
class EstadoDatosPrestamo
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
     * @brief Cambia el estado de devolución de un préstamo.
     *
     * @param string $idPrestamo Identificador del préstamo.
     * @param bool $devuelto Nuevo estado de devolución.
     *
     * @return bool true si el préstamo fue actualizado;
     *              false si ocurrió un error.
     */
    public function cambiarEstadoPrestamo(string $idPrestamo, bool $devuelto): bool
    {
        $sql = "
            UPDATE PRESTAMO
            SET devuelto = :devuelto
            WHERE id = :idPrestamo
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "devuelto" => $devuelto,
                "idPrestamo" => $idPrestamo
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}