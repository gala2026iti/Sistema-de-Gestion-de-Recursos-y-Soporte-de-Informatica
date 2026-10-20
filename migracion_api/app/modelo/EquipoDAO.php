<?php

require_once __DIR__ . "/Equipo.php";

class EquipoDAO
{
    private PDO $conexion;
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarEquipos(string $rolActual, string $orden = "", string $estado = "", string $ubicacion = "", string $tipoUbicacion = "", string $id = ""): array {
        $condiciones = [];
        $parametros = [];

        if ($rolActual === "administrador") {
            $sql = "
                SELECT
                    e.id AS idEquipo,
                    e.fechaCreacion,
                    e.horaCreacion,
                    e.ultimaIntervencion,
                    e.activo,
                    eru.idUbicacion,
                    eru.tipoUbicacion,
                    eru.posicion,
                    COUNT(DISTINCT eugt.idTicket) AS totalIncidencias

                FROM EQUIPO AS e

                LEFT JOIN equipo_reside_ubicacion AS eru
                    ON e.id = eru.idEquipo

                LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
                    ON eugt.idEquipo = e.id
            ";

            if ($estado === "activo") {
                $condiciones[] = "e.activo = :activo";
                $parametros["activo"] = 1;

            } elseif ($estado === "inactivo") {
                $condiciones[] = "e.activo = :activo";
                $parametros["activo"] = 0;
            }

        } elseif ($rolActual === "tecnico" || $rolActual === "docente") {
            $sql = "
                SELECT
                    e.id AS idEquipo,
                    e.ultimaIntervencion,
                    e.activo,
                    eru.idUbicacion,
                    eru.tipoUbicacion,
                    eru.posicion

                FROM EQUIPO AS e

                LEFT JOIN equipo_reside_ubicacion AS eru
                    ON e.id = eru.idEquipo
            ";

            $condiciones[] = "e.activo = TRUE";

        } else {
            return [];
        }

        if ($tipoUbicacion !== "") {
            $condiciones[] = "eru.tipoUbicacion = :tipoUbicacion";
            $parametros["tipoUbicacion"] = $tipoUbicacion;
        }

        if ($ubicacion !== "") {
            $condiciones[] = "eru.idUbicacion = :ubicacion";
            $parametros["ubicacion"] = $ubicacion;
        }

        if ($id !== "") {
            $condiciones[] = "e.id = :id";
            $parametros["id"] = $id;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        if ($rolActual === "administrador") {
            $sql .= "
                GROUP BY
                    e.id,
                    e.fechaCreacion,
                    e.horaCreacion,
                    e.ultimaIntervencion,
                    e.activo,
                    eru.idUbicacion,
                    eru.tipoUbicacion,
                    eru.posicion
            ";
        }

        switch ($orden) {
            case "reciente":
                $sql .= " ORDER BY e.ultimaIntervencion DESC";
                break;

            case "antiguo":
                $sql .= " ORDER BY e.ultimaIntervencion ASC";
                break;

            case "masincidencias":
                if ($rolActual === "administrador") {
                    $sql .= " ORDER BY totalIncidencias DESC";
                } else {
                    $sql .= " ORDER BY e.id ASC";
                }
                break;

            case "menosincidencias":
                if ($rolActual === "administrador") {
                    $sql .= " ORDER BY totalIncidencias ASC";
                } else {
                    $sql .= " ORDER BY e.id ASC";
                }
                break;

            default:
                $sql .= " ORDER BY e.id ASC";
                break;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);
        $equipos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta->closeCursor();
        return $equipos;
    }

    public function registrarEquipo(string $idEquipo, string $idUbicacion, string $tipoUbicacion, string $posicion, string $ciAdmin): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sqlEquipo = "
                INSERT INTO EQUIPO (id,fechaCreacion,horaCreacion)
                VALUES (:idEquipo,CURDATE(),CURTIME())
            ";

            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute(["idEquipo" => $idEquipo]);
            $consultaEquipo->closeCursor();

            if ($tipoUbicacion !== "ninguna") {

                if ($tipoUbicacion === "prestamo") {
                    $idUbicacion = "0";
                    $posicion = $this->obtenerPosicionLibrePrestamo();
                }

                $sqlUbicacion = "
                    INSERT INTO equipo_reside_ubicacion (
                        idEquipo,
                        idUbicacion,
                        tipoUbicacion,
                        posicion
                    )
                    VALUES (
                        :idEquipo,
                        :idUbicacion,
                        :tipoUbicacion,
                        :posicion
                    )
                ";

                $consultaUbicacion = $this->conexion->prepare($sqlUbicacion);
                $consultaUbicacion->execute(["idEquipo" => $idEquipo, "idUbicacion" => $idUbicacion, "tipoUbicacion" => $tipoUbicacion, "posicion" => $posicion]);
                $consultaUbicacion->closeCursor();
            }
            $this->registrarHistorialEquipo($ciAdmin, $idEquipo, "creacion");

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function modificarUbicacionEquipo(string $idEquipo, string $idUbicacion, string $tipoUbicacion, string $posicion, string $ciAdmin): bool
    {
        try {
            $this->conexion->beginTransaction();

            /*
             * Primero eliminamos la ubicación anterior.
             */
            $sql = "
                DELETE FROM equipo_reside_ubicacion
                WHERE idEquipo = :idEquipo
            ";

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute(["idEquipo" => $idEquipo]);

            $consulta->closeCursor();

            /*
             * Si la nueva opción es "ninguna",
             * el equipo queda sin relación de ubicación.
             */
            if ($tipoUbicacion !== "ninguna") {

                /*
                 * Para préstamo se utiliza nuevamente
                 * la ubicación interna 0.
                 */
                if ($tipoUbicacion === "prestamo") {
                    $idUbicacion = "0";
                    $posicion = $this->obtenerPosicionLibrePrestamo();
                }

                $sql = "
                    INSERT INTO equipo_reside_ubicacion (
                        idEquipo,
                        idUbicacion,
                        tipoUbicacion,
                        posicion
                    )
                    VALUES (
                        :idEquipo,
                        :idUbicacion,
                        :tipoUbicacion,
                        :posicion
                    )
                ";

                $consulta = $this->conexion->prepare($sql);
                $consulta->execute(["idEquipo" => $idEquipo, "idUbicacion" => $idUbicacion, "tipoUbicacion" => $tipoUbicacion, "posicion" => $posicion]);

                $consulta->closeCursor();
            }
            $this->registrarHistorialEquipo($ciAdmin, $idEquipo, "modificacion");

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function cambiarEstadoEquipo(string $idEquipo, bool $activo, string $ciAdmin): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "
            UPDATE EQUIPO
            SET activo = :activo
            WHERE id = :idEquipo
        ";

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute(["activo" => $activo ? 1 : 0, "idEquipo" => $idEquipo]);

            $actualizado = $consulta->rowCount() > 0;
            $consulta->closeCursor();

            if (!$actualizado) {
                $this->conexion->rollBack();
                return false;
            }

            $tipoInteraccion = $activo ? "activacion" : "desactivacion";

            $this->registrarHistorialEquipo($ciAdmin, $idEquipo, $tipoInteraccion);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }

    private function obtenerPosicionLibrePrestamo(): string
    {
        $sql = "
            SELECT posicion
            FROM equipo_reside_ubicacion
            WHERE tipoUbicacion = 'prestamo'
            AND idUbicacion = 0
            ORDER BY posicion
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $posicionesOcupadas = $consulta->fetchAll(PDO::FETCH_COLUMN);

        $posicion = 1;

        while (in_array($posicion, $posicionesOcupadas)) {
            $posicion++;
        }
        $consulta->closeCursor();

        return (string) $posicion;
    }

    private function registrarHistorialEquipo(string $ciAdmin, string $idEquipo, string $tipoInteraccion): void
    {
        $sql = "
        INSERT INTO administrador_maneja_equipo (
            ciAdmin,
            idEquipo,
            tipoInteraccion
        )
        VALUES (
            :ciAdmin,
            :idEquipo,
            :tipoInteraccion
        )
    ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["ciAdmin" => $ciAdmin, "idEquipo" => $idEquipo, "tipoInteraccion" => $tipoInteraccion]);
        $consulta->closeCursor();
    }
}