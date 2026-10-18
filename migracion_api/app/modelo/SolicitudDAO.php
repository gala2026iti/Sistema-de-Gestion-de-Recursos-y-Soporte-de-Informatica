<?php

class SolicitudDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    public function registrarSolicitud(
        string $ciDocente,
        string $asunto,
        string $descripcion,
        string $fechaLimite,
        string $horaLimite
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlSolicitud = "
                INSERT INTO SOLICITUD (
                    asunto,
                    descripcion,
                    fechaLimite,
                    horaLimite,
                    finalizada
                )
                VALUES (
                    :asunto,
                    :descripcion,
                    :fechaLimite,
                    :horaLimite,
                    0
                )
            ";

            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);

            $consultaSolicitud->execute([
                "asunto" => $asunto,
                "descripcion" => $descripcion,
                "fechaLimite" => $fechaLimite,
                "horaLimite" => $horaLimite
            ]);

            $idSolicitud = $this->conexion->lastInsertId();

            $consultaSolicitud->closeCursor();


            $sqlDocente = "
                INSERT INTO docente_ingresa_solicitud (
                    ciDocente,
                    idSolicitud,
                    fecha,
                    hora
                )
                VALUES (
                    :ciDocente,
                    :idSolicitud,
                    CURDATE(),
                    CURTIME()
                )
            ";

            $consultaDocente = $this->conexion->prepare($sqlDocente);

            $consultaDocente->execute([
                "ciDocente" => $ciDocente,
                "idSolicitud" => $idSolicitud
            ]);

            $consultaDocente->closeCursor();

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }


    public function listarSolicitudes(
        ?string $estado,
        ?string $id
    ): array {
        $sql = "
            SELECT
                s.id,
                s.asunto,
                s.descripcion,
                s.fechaLimite,
                s.horaLimite,
                s.finalizada,

                dis.ciDocente,
                uDocente.nombre AS nombreDocente,
                dis.fecha AS fechaIngreso,
                dis.hora AS horaIngreso,

                tfs.ciTecnico,
                uTecnico.nombre AS nombreTecnico,
                tfs.fecha AS fechaFinalizacion,
                tfs.hora AS horaFinalizacion

            FROM SOLICITUD AS s

            INNER JOIN docente_ingresa_solicitud AS dis
                ON dis.idSolicitud = s.id

            INNER JOIN USUARIO AS uDocente
                ON uDocente.ci = dis.ciDocente

            LEFT JOIN tecnico_finaliza_solicitud AS tfs
                ON tfs.idSolicitud = s.id

            LEFT JOIN USUARIO AS uTecnico
                ON uTecnico.ci = tfs.ciTecnico
        ";

        $condiciones = [];
        $parametros = [];

        if ($estado === "pendiente") {
            $condiciones[] = "s.finalizada = 0";
        }

        if ($estado === "finalizada") {
            $condiciones[] = "s.finalizada = 1";
        }

        if (!empty($id)) {
            $condiciones[] = "s.id = :id";
            $parametros["id"] = $id;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY s.id DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta->closeCursor();

        return $solicitudes;
    }


    public function obtenerEstadoSolicitud(string $idSolicitud): ?bool
    {
        $sql = "
            SELECT finalizada
            FROM SOLICITUD
            WHERE id = :idSolicitud
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["idSolicitud" => $idSolicitud]);

        $estado = $consulta->fetchColumn();

        $consulta->closeCursor();

        if ($estado === false) {
            return null;
        }

        return (bool) $estado;
    }


    public function finalizarSolicitud(
        string $idSolicitud,
        string $ciTecnico
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlSolicitud = "
                UPDATE SOLICITUD
                SET finalizada = 1
                WHERE id = :idSolicitud
                AND finalizada = 0
            ";

            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute(["idSolicitud" => $idSolicitud]);

            if ($consultaSolicitud->rowCount() === 0) {
                $consultaSolicitud->closeCursor();
                $this->conexion->rollBack();

                return false;
            }

            $consultaSolicitud->closeCursor();


            $sqlTecnico = "
                INSERT INTO tecnico_finaliza_solicitud (
                    ciTecnico,
                    idSolicitud,
                    fecha,
                    hora
                )
                VALUES (
                    :ciTecnico,
                    :idSolicitud,
                    CURDATE(),
                    CURTIME()
                )
            ";

            $consultaTecnico = $this->conexion->prepare($sqlTecnico);

            $consultaTecnico->execute([
                "ciTecnico" => $ciTecnico,
                "idSolicitud" => $idSolicitud
            ]);

            $consultaTecnico->closeCursor();

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