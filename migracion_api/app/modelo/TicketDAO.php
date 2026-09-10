<?php

require_once __DIR__ . "/../Ticket.php";

/**
 * @brief Gestiona las consultas relacionadas con los tickets.
 *
 * Permite buscar un ticket por su identificador y obtener listados con filtros.
 */
class AccesoDatosTicket
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
     * @brief Busca un ticket por su identificador.
     *
     * @param string $id Identificador del ticket.
     *
     * @return Ticket|null Ticket encontrado; null si no existe.
     */
    public function buscarTicket(string $id): ?Ticket
    {
        $sql = "
            SELECT
                t.id,
                t.tipo,
                t.asunto,
                t.descripcion,
                t.gravedad,
                t.estado,
                t.fechaCreacion,
                t.horaCreacion,
                t.justificacion,
                drt.ciDocente,
                u_doc.nombre AS nombreDocente,
                eugt.idEquipo,
                eugt.idUbicacion
                eugt.tipoUbicacion
            FROM TICKET AS t
            LEFT JOIN docente_reporta_ticket AS drt ON drt.idTicket = t.id
            LEFT JOIN USUARIO AS u_doc ON u_doc.ci = drt.ciDocente
            LEFT JOIN equipo_ubicacion_genera_ticket AS eugt ON eugt.idTicket = t.id
            WHERE t.id = :id
            ORDER BY t.id DESC

        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "id" => $id
        ]);

        $ticket = $consulta->fetch(PDO::FETCH_ASSOC);
        $consulta = null;

        if ($ticket === false) {
            return null;
        }

        return new Ticket(
            $ticket["id"],
            $ticket["tipo"],
            $ticket["asunto"],
            $ticket["descripcion"],
            $ticket["gravedad"],
            $ticket["estado"],
            $ticket["fechaCreacion"],
            $ticket["horaCreacion"],
            $ticket["justificacion"],
            $ticket["ciDocente"],
            $ticket["nombreDocente"],
            $ticket["idEquipo"],
            $ticket["idUbicacion"],
            $ticket["tipoUbicacion"]
        );
    }

    /**
     * @brief Obtiene un listado aplicando filtros opcionales.
     *
     * @param string $tiempo Criterio de orden temporal.
     * @param string $gravedad Gravedad por la cual filtrar.
     * @param string $clasificacion Clasificación por la cual filtrar.
     * @param string $estado Estado por el cual filtrar.
     *
     * @return array Lista de registros obtenidos.
     */

    // TOFIX: arreglar las consultas para que coincida correctamente
    public function listarTickets(
        string $tiempo = "",
        string $gravedad = "",
        string $clasificacion = "",
        string $estado = ""
    ): array {
        $sql = "
        SELECT
            t.id,
            t.tipo,
            t.asunto,
            t.descripcion,
            t.gravedad,
            t.estado,
            t.fechaCreacion,
            t.horaCreacion
        FROM TICKET AS t
    ";

        $condiciones = [];
        $parametros = [];

        if ($estado !== "") {
            $condiciones[] = "t.estado = :estado";
            $parametros["estado"] = $estado;
        }

        if ($gravedad !== "") {
            $condiciones[] = "t.gravedad = :gravedad";
            $parametros["gravedad"] = $gravedad;
        }

        if ($clasificacion !== "") {
            $condiciones[] = "t.tipo = :clasificacion";
            $parametros["clasificacion"] = $clasificacion;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        if ($tiempo === "antiguo") {
            $sql .= " ORDER BY t.fechaCreacion ASC, t.horaCreacion ASC";
        } else {
            $sql .= " ORDER BY t.fechaCreacion DESC, t.horaCreacion DESC";
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
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

    /**
     * @brief Asocia al técnico de la sesión con un ticket.
     *
     * @param string $idTicket Identificador del ticket.
     *
     * @return bool true si la asignación se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function asignarme(
        string $idTicket,
        string $ciTecnico
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlColaborador = "
                INSERT INTO COLABORADOR (idTicket, ciTecnico)
                VALUES (:idTicket, :ciTecnico)
                ";



            $consultaColaborador = $this->conexion->prepare($sqlColaborador);
            $consultaColaborador->execute([
                "idTicket" => $idTicket,
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
    /**
     * @brief Desasocia al técnico de la sesión de un ticket.
     *
     * @param string $idTicket Identificador del ticket.
     *
     * @return bool true si la desasignación se realizó correctamente;
     *              false si ocurrió un error.
     */
    public function desasignarme(
        string $idTicket,
        string $ciTecnico
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlColaborador = "
                DELETE FROM COLABORADOR
                WHERE idTicket = :idTicket AND ciTecnico = :ciTecnico
                ";

            $consultaColaborador = $this->conexion->prepare($sqlColaborador);
            $consultaColaborador->execute([
                "idTicket" => $idTicket,
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