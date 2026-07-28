<?php

/**
 * @brief Gestiona el registro de nuevos tickets.
 *
 * Inserta los datos principales de un ticket en la base de datos.
 */
class AltaTicket
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
     * @brief Registra un nuevo ticket.
     *
     * @param string $id Identificador del ticket.
     * @param string $tipo Tipo del ticket.
     * @param string $asunto Asunto del ticket.
     * @param string $descripcion Descripción del ticket.
     * @param string $gravedad Gravedad del ticket.
     * @param string $estado Estado inicial del ticket.
     * @param string $fechaCreacion Fecha de creación.
     * @param string $horaCreacion Hora de creación.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function registrarTicket(
        string $id,
        string $tipo,
        string $asunto,
        string $descripcion,
        string $gravedad,
        string $estado,
        string $fechaCreacion,
        string $horaCreacion,
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlTicket = "
                INSERT INTO TICKET (id, tipo, asunto, descripcion, gravedad, estado, fechaCreacion, horaCreacion, justificacion)
                VALUES (:id, :tipo, :asunto, :descripcion, :gravedad, :estado, :fechaCreacion, :horaCreacion, NULL)
            ";

            $consultaTicket = $this->conexion->prepare($sqlTicket);
            $consultaTicket->execute([
                "id" => $id,
                "tipo" => $tipo,
                "asunto" => $asunto,
                "descripcion" => $descripcion,
                "gravedad" => $gravedad,
                "estado" => $estado,
                "fechaCreacion" => $fechaCreacion,
                "horaCreacion" => $horaCreacion,
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