<?php

require_once __DIR__ . "/Usuario.php";

class UsuarioDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    /**
     * Busca un usuario por cédula.
     * Se utiliza principalmente durante el inicio de sesión.
     */
    public function buscarUsuario(string $cedula): ?Usuario
    {
        $sql = "
            SELECT
                u.ci AS cedula,
                u.nombre,
                u.clave AS claveHash,
                u.activo,

                CASE
                    WHEN a.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS administrador,

                CASE
                    WHEN t.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS tecnico,

                CASE
                    WHEN d.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS docente

            FROM USUARIO AS u

            LEFT JOIN ADMINISTRADOR AS a
                ON a.ci = u.ci

            LEFT JOIN TECNICO AS t
                ON t.ci = u.ci

            LEFT JOIN DOCENTE AS d
                ON d.ci = u.ci

            WHERE u.ci = :cedula
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "cedula" => $cedula
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
        $consulta = null;

        if ($usuario === false) {
            return null;
        }

        return new Usuario(
            $usuario["cedula"],
            $usuario["nombre"],
            $usuario["claveHash"],
            (bool) $usuario["activo"],
            (bool) $usuario["administrador"],
            (bool) $usuario["tecnico"],
            (bool) $usuario["docente"]
        );
    }


    /**
     * Lista los usuarios registrados.
     * Permite filtrar opcionalmente por rol y estado.
     */
    public function listarUsuarios(
        string $rol = "",
        string $estado = ""
    ): array {
        $sql = "
            SELECT
                u.ci AS cedula,
                u.nombre,
                c.correo,
                u.activo,

                CASE
                    WHEN a.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS administrador,

                CASE
                    WHEN t.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS tecnico,

                CASE
                    WHEN d.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS docente

            FROM USUARIO AS u

            LEFT JOIN CORREO AS c
                ON c.ci = u.ci

            LEFT JOIN ADMINISTRADOR AS a
                ON a.ci = u.ci

            LEFT JOIN TECNICO AS t
                ON t.ci = u.ci

            LEFT JOIN DOCENTE AS d
                ON d.ci = u.ci
        ";

        $condiciones = [];
        $parametros = [];

        if ($estado === "activo") {
            $condiciones[] = "u.activo = :activo";
            $parametros["activo"] = 1;

        } elseif ($estado === "inactivo") {
            $condiciones[] = "u.activo = :activo";
            $parametros["activo"] = 0;
        }

        if ($rol === "administrador") {
            $condiciones[] = "a.ci IS NOT NULL";

        } elseif ($rol === "tecnico") {
            $condiciones[] = "t.ci IS NOT NULL";

        } elseif ($rol === "docente") {
            $condiciones[] = "d.ci IS NOT NULL";
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " .
                implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY u.ci";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $usuarios = $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );

        $consulta = null;

        return $usuarios;
    }


    /**
     * Registra un nuevo usuario, su correo y sus roles.
     */
    public function registrarUsuario(
        string $cedula,
        string $nombre,
        string $correo,
        string $claveHash,
        array $roles,
        string $ciAdmin
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlUsuario = "
                INSERT INTO USUARIO (
                    ci,
                    nombre,
                    clave,
                    activo
                )
                VALUES (
                    :cedula,
                    :nombre,
                    :clave,
                    TRUE
                )
            ";

            $consultaUsuario =
                $this->conexion->prepare(
                    $sqlUsuario
                );

            $consultaUsuario->execute([
                "cedula" => $cedula,
                "nombre" => $nombre,
                "clave" => $claveHash
            ]);

            $consultaUsuario->closeCursor();


            $sqlCorreo = "
                INSERT INTO CORREO (
                    ci,
                    correo
                )
                VALUES (
                    :cedula,
                    :correo
                )
            ";

            $consultaCorreo =
                $this->conexion->prepare(
                    $sqlCorreo
                );

            $consultaCorreo->execute([
                "cedula" => $cedula,
                "correo" => $correo
            ]);

            $consultaCorreo->closeCursor();


            foreach ($roles as $rol) {
                switch (strtolower($rol)) {
                    case "administrador":
                        $sqlRol = "
                            INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    case "tecnico":
                        $sqlRol = "
                            INSERT INTO TECNICO (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    case "docente":
                        $sqlRol = "
                            INSERT INTO DOCENTE (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    default:
                        $this->conexion->rollBack();
                        return false;
                }

                $consultaRol =
                    $this->conexion->prepare(
                        $sqlRol
                    );

                $consultaRol->execute([
                    "cedula" => $cedula
                ]);

                $consultaRol->closeCursor();
            }
            $this->registrarHistorialUsuario(
                $ciAdmin,
                $cedula,
                "creacion"
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


    /**
     * Cambia el estado activo/inactivo de un usuario.
     */
    public function cambiarEstadoUsuario(
        string $cedula,
        bool $activo,
        string $ciAdmin
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "
            UPDATE USUARIO
            SET activo = :activo
            WHERE ci = :cedula
        ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "activo" => $activo ? 1 : 0,
                "cedula" => $cedula
            ]);

            $actualizado = $consulta->rowCount() > 0;

            $consulta->closeCursor();

            if (!$actualizado) {
                $this->conexion->rollBack();
                return false;
            }

            $tipoInteraccion =
                $activo
                ? "activacion"
                : "desactivacion";

            $this->registrarHistorialUsuario(
                $ciAdmin,
                $cedula,
                $tipoInteraccion
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


    /**
     * Modifica los datos y roles de un usuario.
     */
    public function modificarUsuario(
        string $cedula,
        string $nombre,
        string $correo,
        array $roles,
        string $ciAdmin,
        ?string $claveHash = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            if ($claveHash !== null) {
                $sqlUsuario = "
                    UPDATE USUARIO
                    SET
                        nombre = :nombre,
                        clave = :clave
                    WHERE ci = :cedula
                ";

                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "clave" => $claveHash,
                    "cedula" => $cedula
                ];

            } else {
                $sqlUsuario = "
                    UPDATE USUARIO
                    SET nombre = :nombre
                    WHERE ci = :cedula
                ";

                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "cedula" => $cedula
                ];
            }

            $consultaUsuario =
                $this->conexion->prepare(
                    $sqlUsuario
                );

            $consultaUsuario->execute(
                $parametrosUsuario
            );

            $consultaUsuario->closeCursor();


            $sqlCorreo = "
                UPDATE CORREO
                SET correo = :correo
                WHERE ci = :cedula
            ";

            $consultaCorreo =
                $this->conexion->prepare(
                    $sqlCorreo
                );

            $consultaCorreo->execute([
                "correo" => $correo,
                "cedula" => $cedula
            ]);

            $consultaCorreo->closeCursor();


            $tablasRoles = [
                "ADMINISTRADOR",
                "TECNICO",
                "DOCENTE"
            ];

            foreach ($tablasRoles as $tabla) {
                $sqlEliminarRol = "
                    DELETE FROM $tabla
                    WHERE ci = :cedula
                ";

                $consultaEliminarRol =
                    $this->conexion->prepare(
                        $sqlEliminarRol
                    );

                $consultaEliminarRol->execute([
                    "cedula" => $cedula
                ]);

                $consultaEliminarRol->closeCursor();
            }


            foreach ($roles as $rol) {
                switch (strtolower($rol)) {
                    case "administrador":
                        $sqlRol = "
                            INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    case "tecnico":
                        $sqlRol = "
                            INSERT INTO TECNICO (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    case "docente":
                        $sqlRol = "
                            INSERT INTO DOCENTE (ci)
                            VALUES (:cedula)
                        ";
                        break;

                    default:
                        if (
                            $this->conexion
                                ->inTransaction()
                        ) {
                            $this->conexion
                                ->rollBack();
                        }

                        return false;
                }

                $consultaRol =
                    $this->conexion->prepare(
                        $sqlRol
                    );

                $consultaRol->execute([
                    "cedula" => $cedula
                ]);

                $consultaRol->closeCursor();
            }
            $this->registrarHistorialUsuario(
                $ciAdmin,
                $cedula,
                "modificacion"
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

    private function registrarHistorialUsuario(
        string $ciAdmin,
        string $ciUsuario,
        string $tipoInteraccion
    ): void {
        $sql = "
        INSERT INTO administrador_modifica_usuario (
            ciAdmin,
            ciUsuario,
            tipoInteraccion
        )
        VALUES (
            :ciAdmin,
            :ciUsuario,
            :tipoInteraccion
        )
    ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "ciAdmin" => $ciAdmin,
            "ciUsuario" => $ciUsuario,
            "tipoInteraccion" => $tipoInteraccion
        ]);

        $consulta->closeCursor();
    }
}