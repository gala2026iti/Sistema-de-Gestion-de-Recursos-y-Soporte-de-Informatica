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
            WHERE u.activo = TRUE
            AND u.tipo IN ('laboratorio', 'taller')
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


    public function registrarUbicacion(
        string $tipo,
        string $ciAdmin
    ): bool {
        try {
            $this->conexion->beginTransaction();

            /*
             * Primero se busca una ubicación inactiva
             * del mismo tipo para reutilizarla.
             */
            $idInactivo = $this->obtenerIdInactivo($tipo);

            if ($idInactivo !== null) {

                $sql = "
                    UPDATE UBICACION
                    SET activo = TRUE
                    WHERE id = :id
                    AND tipo = :tipo
                ";

                $consulta = $this->conexion->prepare($sql);

                $consulta->execute([
                    "id" => $idInactivo,
                    "tipo" => $tipo
                ]);

                $consulta->closeCursor();

                $this->registrarHistorialUbicacion(
                    $ciAdmin,
                    $idInactivo,
                    $tipo,
                    "activacion"
                );

            } else {

                $id = $this->obtenerIdLibre($tipo);

                $sql = "
                    INSERT INTO UBICACION (
                        id,
                        tipo,
                        activo
                    )
                    VALUES (
                        :id,
                        :tipo,
                        TRUE
                    )
                ";

                $consulta = $this->conexion->prepare($sql);

                $consulta->execute([
                    "id" => $id,
                    "tipo" => $tipo
                ]);

                $consulta->closeCursor();

                $this->registrarHistorialUbicacion(
                    $ciAdmin,
                    $id,
                    $tipo,
                    "creacion"
                );
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


    public function eliminarUbicacion(
        string $id,
        string $tipo,
        string $ciAdmin
    ): bool {
        try {
            $this->conexion->beginTransaction();

            /*
             * Se eliminan las relaciones de los equipos.
             * Los equipos siguen existiendo pero quedan
             * sin ubicación.
             */
            $sql = "
                DELETE FROM equipo_reside_ubicacion
                WHERE idUbicacion = :id
                AND tipoUbicacion = :tipo
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "id" => $id,
                "tipo" => $tipo
            ]);

            $consulta->closeCursor();


            /*
             * La ubicación no se elimina físicamente.
             * Se marca como inactiva.
             */
            $sql = "
                UPDATE UBICACION
                SET activo = FALSE
                WHERE id = :id
                AND tipo = :tipo
                AND activo = TRUE
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "id" => $id,
                "tipo" => $tipo
            ]);

            $desactivada = $consulta->rowCount() > 0;

            $consulta->closeCursor();

            if (!$desactivada) {
                $this->conexion->rollBack();

                return false;
            }

            $this->registrarHistorialUbicacion(
                $ciAdmin,
                $id,
                $tipo,
                "desactivacion"
            );

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }


    private function obtenerIdInactivo(
        string $tipo
    ): ?string {
        $sql = "
            SELECT id
            FROM UBICACION
            WHERE tipo = :tipo
            AND activo = FALSE
            ORDER BY id ASC
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "tipo" => $tipo
        ]);

        $id = $consulta->fetchColumn();

        $consulta->closeCursor();

        if ($id === false) {
            return null;
        }

        return (string) $id;
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

            $consulta->execute([
                "id" => $id,
                "tipo" => $tipo
            ]);

            if (!$consulta->fetch()) {
                $consulta->closeCursor();

                return (string) $id;
            }

            $consulta->closeCursor();
        }
    }


    private function registrarHistorialUbicacion(
        string $ciAdmin,
        string $idUbicacion,
        string $tipoUbicacion,
        string $tipoInteraccion
    ): void {
        $sql = "
            INSERT INTO administrador_controla_ubicacion (
                ciAdmin,
                idUbicacion,
                tipoUbicacion,
                tipoInteraccion
            )
            VALUES (
                :ciAdmin,
                :idUbicacion,
                :tipoUbicacion,
                :tipoInteraccion
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "ciAdmin" => $ciAdmin,
            "idUbicacion" => $idUbicacion,
            "tipoUbicacion" => $tipoUbicacion,
            "tipoInteraccion" => $tipoInteraccion
        ]);

        $consulta->closeCursor();
    }
}