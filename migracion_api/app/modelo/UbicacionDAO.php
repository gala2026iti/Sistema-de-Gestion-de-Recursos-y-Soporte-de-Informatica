<?php

require_once __DIR__ . "/Ubicacion.php";

class UbicacionDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarUbicaciones(): array
    {
        $sql = "
            SELECT
                u.id,
                u.tipo
            FROM UBICACION AS u
            ORDER BY
                u.tipo,
                u.id
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $ubicaciones = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta->closeCursor();

        return $ubicaciones;
    }

    public function registrarUbicacion(string $tipo): bool
    {
        $id = $this->obtenerIdLibre($tipo);

        try {
            $sql = "
                INSERT INTO UBICACION (id,tipo)
                VALUES (:id,:tipo) ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute(["id" => $id, "tipo" => $tipo ]);

            $consulta->closeCursor();

            return true;

        } catch (PDOException $error) {
            return false;
        }
    }

    public function eliminarUbicacion( string $id, string $tipo): bool {
        try {
            $this->conexion->beginTransaction();

            /*
             * Primero se elimina la relación de los equipos
             * con la ubicación que se va a eliminar.
             */
            $sql = "
                DELETE FROM equipo_reside_ubicacion
                WHERE idUbicacion = :id
                AND tipoUbicacion = :tipo
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([ "id" => $id, "tipo" => $tipo ]);

            $consulta->closeCursor();


            /*
             * Luego se elimina la ubicación.
             */
            $sql = "
                DELETE FROM UBICACION
                WHERE id = :id
                AND tipo = :tipo
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute(["id" => $id,"tipo" => $tipo]);

            $eliminada = $consulta->rowCount() > 0;

            $consulta->closeCursor();

            if (!$eliminada) {
                $this->conexion->rollBack();

                return false;
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

    private function obtenerIdLibre(string $tipo): string
    {
        for ($id = 1; ; $id++) {
            $sql = "
                SELECT id
                FROM UBICACION
                WHERE id = :id
                AND tipo = :tipo
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute(["id" => $id,"tipo" => $tipo]);

            if (!$consulta->fetch()) {
                $consulta->closeCursor();

                return (string) $id;
            }

            $consulta->closeCursor();
        }
    }
}