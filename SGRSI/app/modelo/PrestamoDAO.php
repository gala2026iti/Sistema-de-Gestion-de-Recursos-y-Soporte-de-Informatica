<?php

class PrestamoDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    public function listarPrestamos(?string $estado, ?string $idPrestamo): array
    {
        $sql = "
            SELECT
                p.id,
                p.nombrePrestado,
                p.ciPrestado,
                p.fechaFin,
                p.horaFin,
                p.devuelto,

                pce.idEquipo,

                ttp.ciTecnico,
                u.nombre AS nombreTecnico,
                ttp.fecha AS fechaPrestamo,
                ttp.hora AS horaPrestamo

            FROM PRESTAMO AS p

            INNER JOIN prestamo_corresponde_equipo AS pce
                ON pce.idPrestamo = p.id

            LEFT JOIN tecnico_tramita_prestamo AS ttp
                ON ttp.id = (
                    SELECT MIN(ttp2.id)
                    FROM tecnico_tramita_prestamo AS ttp2
                    WHERE ttp2.idPrestamo = p.id
                    AND ttp2.tipoInteraccion = 'prestamo'
                )

            LEFT JOIN USUARIO AS u
                ON u.ci = ttp.ciTecnico
        ";

        $condiciones = [];
        $parametros = [];

        if ($estado === "prestado") {
            $condiciones[] = "p.devuelto = 0";
        }

        if ($estado === "devuelto") {
            $condiciones[] = "p.devuelto = 1";
        }

        if (!empty($idPrestamo)) {
            $condiciones[] = "p.id = :idPrestamo";
            $parametros["idPrestamo"] = $idPrestamo;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY p.id DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $prestamos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta->closeCursor();

        return $prestamos;
    }


    public function listarEquiposDisponibles(): array
    {
        return $this->consultarEquiposDisponibles();
    }


    public function equipoDisponible(string $idEquipo): bool
    {
        $equipos = $this->consultarEquiposDisponibles($idEquipo);
        return count($equipos) > 0;
    }

    private function consultarEquiposDisponibles(?string $idEquipo = null): array
    {
        $sql = "
        SELECT
            e.id AS idEquipo

        FROM EQUIPO AS e

        INNER JOIN equipo_reside_ubicacion AS eru
            ON eru.idEquipo = e.id

        LEFT JOIN prestamo_corresponde_equipo AS pce
            ON pce.idEquipo = e.id

        LEFT JOIN PRESTAMO AS p
            ON p.id = pce.idPrestamo
            AND p.devuelto = 0

        LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
            ON eugt.idEquipo = e.id

        LEFT JOIN TICKET AS t
            ON t.id = eugt.idTicket
            AND t.estado <> 'resuelto'

        WHERE e.activo = 1
        AND eru.idUbicacion = 0
        AND eru.tipoUbicacion = 'prestamo'
    ";

        $parametros = [];

        if ($idEquipo !== null) {
            $sql .= " AND e.id = :idEquipo";
            $parametros["idEquipo"] = $idEquipo;
        }

        $sql .= "
        GROUP BY e.id

        HAVING COUNT(p.id) = 0
        AND COUNT(t.id) = 0

        ORDER BY e.id
    ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);
        $equipos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta->closeCursor();
        return $equipos;
    }

    public function registrarPrestamo(string $ciTecnico, string $nombrePrestado, string $ciPrestado, string $fechaFin, string $horaFin, string $idEquipo): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sqlPrestamo = "
                INSERT INTO PRESTAMO (
                    nombrePrestado,
                    ciPrestado,
                    fechaFin,
                    horaFin,
                    devuelto
                )
                VALUES (
                    :nombrePrestado,
                    :ciPrestado,
                    :fechaFin,
                    :horaFin,
                    0
                )
            ";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute(["nombrePrestado" => $nombrePrestado, "ciPrestado" => $ciPrestado, "fechaFin" => $fechaFin, "horaFin" => $horaFin]);

            $idPrestamo = $this->conexion->lastInsertId();
            $consultaPrestamo->closeCursor();


            $sqlEquipo = "
                INSERT INTO prestamo_corresponde_equipo (
                    idPrestamo,
                    idEquipo
                )
                VALUES (
                    :idPrestamo,
                    :idEquipo
                )
            ";

            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute(["idPrestamo" => $idPrestamo, "idEquipo" => $idEquipo]);
            $consultaEquipo->closeCursor();


            $sqlTecnico = "
                INSERT INTO tecnico_tramita_prestamo (
                    ciTecnico,
                    idPrestamo,
                    fecha,
                    hora,
                    tipoInteraccion
                )
                VALUES (
                    :ciTecnico,
                    :idPrestamo,
                    CURDATE(),
                    CURTIME(),
                    'prestamo'
                )
            ";

            $consultaTecnico = $this->conexion->prepare($sqlTecnico);
            $consultaTecnico->execute(["ciTecnico" => $ciTecnico, "idPrestamo" => $idPrestamo]);
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


    public function obtenerEstadoPrestamo(string $idPrestamo): ?bool
    {
        $sql = "
            SELECT devuelto
            FROM PRESTAMO
            WHERE id = :idPrestamo
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["idPrestamo" => $idPrestamo]);

        $estado = $consulta->fetchColumn();

        $consulta->closeCursor();

        if ($estado === false) {
            return null;
        }

        return (bool) $estado;
    }


    public function devolverPrestamo(
        string $idPrestamo,
        string $ciTecnico
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlPrestamo = "
                UPDATE PRESTAMO
                SET devuelto = 1
                WHERE id = :idPrestamo
                AND devuelto = 0
            ";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute(["idPrestamo" => $idPrestamo]);

            if ($consultaPrestamo->rowCount() === 0) {
                $consultaPrestamo->closeCursor();
                $this->conexion->rollBack();

                return false;
            }

            $consultaPrestamo->closeCursor();


            $sqlTecnico = "
                INSERT INTO tecnico_tramita_prestamo (
                    ciTecnico,
                    idPrestamo,
                    fecha,
                    hora,
                    tipoInteraccion
                )
                VALUES (
                    :ciTecnico,
                    :idPrestamo,
                    CURDATE(),
                    CURTIME(),
                    'devolucion'
                )
            ";

            $consultaTecnico = $this->conexion->prepare($sqlTecnico);
            $consultaTecnico->execute(["ciTecnico" => $ciTecnico, "idPrestamo" => $idPrestamo]);
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