<?php
// app/modelo/AccesoDatosUsuario.php

require_once __DIR__ . "/Usuario.php";

/**
 * Clase DAO que gestiona las consultas de usuarios en la base de datos SGRSI
 */
class AccesoDatosUsuario {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión activa a la BD
     */
    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Busca un usuario por su CI y determina sus roles mediante LEFT JOIN
     */
    public function buscarUsuario(string $cedula): ?Usuario {
        $sql = "
            SELECT
                u.ci AS cedula,
                u.clave AS claveHash,
                u.activo AS sesionActiva,

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
        $consulta->execute(["cedula" => $cedula]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        // Desconectar el objeto PDOStatement
        $consulta = null;

        if ($usuario === false) {
            return null;
        }

        return new Usuario(
            $usuario["cedula"],
            $usuario["claveHash"],
            (bool) $usuario["sesionActiva"],
            (bool) $usuario["administrador"],
            (bool) $usuario["tecnico"],
            (bool) $usuario["docente"]
        );
    }

    /**
     * Devuelve el listado completo de usuarios
     */
    public function listarUsuarios(): array {
        $sql = "
            SELECT
                u.ci AS cedula,
                u.nombre,
                u.correo,
                u.activo AS sesionActiva,

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
        ";

        $consulta = $this->conexion->query($sql);
        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $usuarios;
    }
}