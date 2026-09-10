<?php

/**
 * @brief Gestiona el acceso a datos de las solicitudes.
 *
 * Permite registrar, consultar y modificar el estado
 * de las solicitudes almacenadas en la base de datos.
 */
class SolicitudDAO
{
    /** @brief Conexión con la base de datos. */
    private PDO $conexion;

    /**
     * @brief Construye el acceso a datos de solicitudes.
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
     * Registra la solicitud y su relación con el docente
     * que la ingresó dentro de una transacción.
     *
     * @param string $id Identificador de la solicitud.
     * @param string $asunto Asunto de la solicitud.
     * @param string $descripcion Descripción de la solicitud.
     * @param string $fechaLimite Fecha límite.
     * @param string $horaLimite Hora límite.
     * @param string $ciDocente Cédula del docente.
     * @param string $fecha Fecha de registro.
     * @param string $hora Hora de registro.
     *
     * @return bool true si se registró correctamente; false si ocurrió un error.
     */
    public function registrarSolicitud(
        string $id,
        string $asunto,
        string $descripcion,
        string $fechaLimite,
        string $horaLimite,
        string $ciDocente,
        string $fecha,
        string $hora
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlSolicitud = "
                INSERT INTO SOLICITUD (id, asunto, descripcion, fechaLimite, horaLimite, finalizada)
                VALUES (:idSolicitud, :asunto, :descripcion, :fechaLimite, :horaLimite, FALSE)
            ";

            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "idSolicitud" => $id,
                "asunto" => $asunto,
                "descripcion" => $descripcion,
                "fechaLimite" => $fechaLimite,
                "horaLimite" => $horaLimite
            ]);

            $sqlDocente = "
                INSERT INTO docente_ingresa_solicitud (ciDocente, idSolicitud, fecha, hora)
                VALUES (:ciDocente, :idSolicitud, :fecha, :hora)
            ";

            $consultaDocente = $this->conexion->prepare($sqlDocente);
            $consultaDocente->execute([
                "ciDocente" => $ciDocente,
                "idSolicitud" => $id,
                "fecha" => $fecha,
                "hora" => $hora
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

    /**
     * @brief Obtiene las solicitudes registradas.
     *
     * Permite filtrar por solicitudes pendientes o finalizadas.
     *
     * @param string $estado Estado por el cual filtrar.
     *
     * @return array Lista de solicitudes encontradas.
     */
    public function listarSolicitudes(string $estado = ""): array
    {
        $sql = "
            SELECT
                s.id,
                s.asunto,
                s.descripcion,
                s.fechaLimite,
                s.horaLimite,
                s.finalizada,
                dis.ciDocente,
                u.nombre
            FROM SOLICITUD AS s
            INNER JOIN docente_ingresa_solicitud AS dis
                ON dis.idSolicitud = s.id
            INNER JOIN USUARIO AS u
                ON u.ci = dis.ciDocente
        ";

        $condiciones = [];
        $parametros = [];

        if ($estado === "finalizado") {
            $condiciones[] = "s.finalizada = :finalizada";
            $parametros["finalizada"] = 1;
        } elseif ($estado === "pendiente") {
            $condiciones[] = "s.finalizada = :finalizada";
            $parametros["finalizada"] = 0;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY s.id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $solicitudes;
    }

    /**
     * @brief Cambia el estado de una solicitud.
     *
     * @param string $idSolicitud Identificador de la solicitud.
     * @param bool $finalizada Nuevo estado de finalización.
     *
     * @return bool true si fue actualizada; false si ocurrió un error.
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