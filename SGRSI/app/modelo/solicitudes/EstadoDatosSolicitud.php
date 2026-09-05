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
    public function cambiarEstadoSolicitud(string $id): bool
    {
        $sql = "
            UPDATE SOLICITUD
            SET finalizada = 1
            WHERE id = CAST(:id AS INT)
        ";

        /*
        Debido a que en la BD "id" es de tipo entero,
        se debe formatear porque, si se manda el número como String,
        no se encuentran coincidencias
        */

        try {
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                "id" => $id
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {

            return false;
        }
    }
}