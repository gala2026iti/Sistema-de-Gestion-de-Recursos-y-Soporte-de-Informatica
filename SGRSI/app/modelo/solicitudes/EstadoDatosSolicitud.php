<?php

/**
 * @brief Gestiona el estado de las solicitudes.
 *
 * Permite marcar una solicitud como finalizada o pendiente.
 */
class EstadoDatosSolicitud
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
     * @brief Cambia el estado de finalización de una solicitud.
     *
     * @param string $idSolicitud Identificador de la solicitud.
     * @param bool $finalizada Nuevo estado de finalización.
     *
     * @return bool true si la solicitud fue actualizada;
     *              false si ocurrió un error.
     */
    public function cambiarEstadoSolicitud(string $idSolicitud, bool $finalizada): bool
    {
        $sql = "
            UPDATE SOLICITUD
            SET finalizada = :finalizada
            WHERE id = :idSolicitud
        ";

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "finalizada" => $finalizada,
                "idSolicitud" => $idSolicitud
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {
            return false;
        }
    }
}