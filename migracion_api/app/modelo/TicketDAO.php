<?php

class TicketDAO
{
    private PDO $conexion;
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrarTickets( string $ciDocente, string $idUbicacion, string $tipoUbicacion, array $tickets): bool {
        try {
            $this->conexion->beginTransaction();

            foreach ($tickets as $ticket) {

                $sqlTicket = "
                    INSERT INTO TICKET (tipo, asunto, persona, descripcion, gravedad, estado, fechaCreacion, horaCreacion)
                    VALUES ( :tipo, :asunto, :persona, :descripcion,:gravedad, 'pendiente', CURDATE(), CURTIME())";

                $consultaTicket = $this->conexion->prepare($sqlTicket);

                $consultaTicket->execute([
                    "tipo" => $ticket["tipo"],
                    "asunto" => $ticket["asunto"],
                    "persona" => $ticket["persona"],
                    "descripcion" => $ticket["descripcion"],
                    "gravedad" => $ticket["gravedad"]]);

                /*
                 * Obtenemos el ID que MySQL
                 * acaba de generar.
                 */
                $idTicket = $this->conexion->lastInsertId();
                $consultaTicket->closeCursor();

                $sqlDocente = "
                    INSERT INTO docente_reporta_ticket (ciDocente,idTicket)
                    VALUES (:ciDocente,:idTicket) ";

                $consultaDocente = $this->conexion->prepare($sqlDocente);
                $consultaDocente->execute([ "ciDocente" => $ciDocente, "idTicket" => $idTicket ]);
                $consultaDocente->closeCursor();

                $sqlEquipo = "
                    INSERT INTO equipo_ubicacion_genera_ticket (idEquipo, idUbicacion, tipoUbicacion, idTicket)
                    VALUES ( :idEquipo, :idUbicacion, :tipoUbicacion, :idTicket)
                ";

                $consultaEquipo = $this->conexion->prepare($sqlEquipo);
                $consultaEquipo->execute(["idEquipo" => $ticket["idEquipo"], "idUbicacion" => $idUbicacion, "tipoUbicacion" => $tipoUbicacion, "idTicket" => $idTicket ]);
                $consultaEquipo->closeCursor();
            }

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }

    public function listarTickets(
        string $ciTecnico,
        ?string $orden,
        ?string $gravedad,
        ?string $tipo,
        ?string $estado,
        ?string $idTicket,
        ?string $idEquipo
    ): array {
        $sql = "
            SELECT
                t.id,
                t.tipo,
                t.asunto,
                t.descripcion,
                t.persona,
                t.gravedad,
                t.estado,
                t.fechaCreacion,
                t.horaCreacion,
                t.justificacion,

                drt.ciDocente,
                u.nombre AS nombreDocente,

                eugt.idEquipo,
                eugt.idUbicacion,
                eugt.tipoUbicacion,

                (
                    SELECT GROUP_CONCAT(
                        uTecnico.nombre
                        ORDER BY uTecnico.nombre
                        SEPARATOR ', '
                    )

                    FROM COLABORADOR AS c

                    LEFT JOIN USUARIO AS uTecnico
                        ON uTecnico.ci = c.ciTecnico

                    WHERE c.idTicket = t.id
                ) AS colaboradores,

                EXISTS (
                    SELECT 1
                    FROM COLABORADOR AS c
                    WHERE c.idTicket = t.id
                    AND c.ciTecnico = :ciTecnico
                ) AS esColaborador

            FROM TICKET AS t

            LEFT JOIN docente_reporta_ticket AS drt
                ON drt.idTicket = t.id

            LEFT JOIN USUARIO AS u
                ON u.ci = drt.ciDocente

            LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
                ON eugt.idTicket = t.id
        ";

        $condiciones = [];
        $parametros = ["ciTecnico" => $ciTecnico];

        if (!empty($gravedad)) {
            $condiciones[] = "t.gravedad = :gravedad";
            $parametros["gravedad"] = $gravedad;
        }

        if (!empty($tipo)) {
            $condiciones[] = "t.tipo = :tipo";
            $parametros["tipo"] = $tipo;
        }

        if (!empty($estado)) {
            $condiciones[] = "t.estado = :estado";
            $parametros["estado"] = $estado;
        }

        if (!empty($idTicket)) {
            $condiciones[] = "t.id = :idTicket";
            $parametros["idTicket"] = $idTicket;
        }

        if (!empty($idEquipo)) {
            $condiciones[] = "eugt.idEquipo = :idEquipo";
            $parametros["idEquipo"] = $idEquipo;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        if ($orden === "antiguo") {
            $sql .= "
                ORDER BY
                    t.fechaCreacion ASC,
                    t.horaCreacion ASC";
        } else {
            $sql .= "
                ORDER BY
                    t.fechaCreacion DESC,
                    t.horaCreacion DESC";
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $tickets = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta->closeCursor();

        return $tickets;
    }

    public function asignarTecnico(string $idTicket, string $ciTecnico): bool
    {
        try {
            $sql = "
                INSERT INTO COLABORADOR (idTicket,ciTecnico)
                VALUES (:idTicket,:ciTecnico)";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([ "idTicket" => $idTicket, "ciTecnico" => $ciTecnico ]);

            $resultado = $consulta->rowCount() > 0;

            $consulta->closeCursor();

            return $resultado;

        } catch (PDOException $error) {
            return false;
        }
    }

    public function desasignarTecnico(string $idTicket, string $ciTecnico): bool
    {
        try {
            $sql = "
                DELETE FROM COLABORADOR
                WHERE idTicket = :idTicket
                AND ciTecnico = :ciTecnico
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([ "idTicket" => $idTicket, "ciTecnico" => $ciTecnico]);

            $resultado = $consulta->rowCount() > 0;

            $consulta->closeCursor();

            return $resultado;

        } catch (PDOException $error) {
            return false;
        }
    }
}