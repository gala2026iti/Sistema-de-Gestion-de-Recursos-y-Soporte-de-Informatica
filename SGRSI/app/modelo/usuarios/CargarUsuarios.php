<?php

require_once __DIR__ . "/Usuario.php";

/**
 * @brief Gestiona las consultas relacionadas con los usuarios.
 *
 * Consulta la información de los usuarios y determina sus roles
 * mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */
class AccesoDatosUsuario
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye el acceso a datos de usuarios.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * @brief Busca un usuario por su cédula.
     *
     * Obtiene los datos necesarios para la autenticación y determina
     * los roles administrador, técnico y docente del usuario.
     *
     * @param string $cedula Cédula del usuario sin puntos ni guiones.
     *
     * @return Usuario|null Usuario encontrado; null si no existe.
     */
    public function buscarUsuario(string $cedula): ?Usuario
    {
        $sql = "
            SELECT
                u.ci AS cedula,
                u.clave AS claveHash,
                u.activo AS sesionActiva,
                CASE WHEN a.ci IS NOT NULL THEN TRUE ELSE FALSE END AS administrador,
                CASE WHEN t.ci IS NOT NULL THEN TRUE ELSE FALSE END AS tecnico,
                CASE WHEN d.ci IS NOT NULL THEN TRUE ELSE FALSE END AS docente
            FROM USUARIO AS u
            LEFT JOIN ADMINISTRADOR AS a ON a.ci = u.ci
            LEFT JOIN TECNICO AS t ON t.ci = u.ci
            LEFT JOIN DOCENTE AS d ON d.ci = u.ci
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
            $usuario["claveHash"],
            (bool) $usuario["sesionActiva"],
            (bool) $usuario["administrador"],
            (bool) $usuario["tecnico"],
            (bool) $usuario["docente"]
        );
    }

    /**
     * @brief Obtiene los usuarios registrados aplicando filtros opcionales.
     *
     * Permite filtrar los usuarios por rol y estado.
     *
     * @param string $rol Rol por el cual filtrar.
     * @param string $estado Estado por el cual filtrar.
     *
     * @return array Lista de usuarios encontrados.
     */
public function listarUsuarios(string $rol = "", string $estado = ""): array
    {
        $sql ="
            SELECT
                u.ci AS cedula,
                u.nombre,
                c.correo,
                u.activo,
                CASE WHEN a.ci IS NOT NULL THEN TRUE ELSE FALSE END AS administrador,
                CASE WHEN t.ci IS NOT NULL THEN TRUE ELSE FALSE END AS tecnico,
                CASE WHEN d.ci IS NOT NULL THEN TRUE ELSE FALSE END AS docente
            FROM USUARIO AS u
            LEFT JOIN CORREO AS c ON c.ci = u.ci
            LEFT JOIN ADMINISTRADOR AS a ON a.ci = u.ci
            LEFT JOIN TECNICO AS t ON t.ci = u.ci
            LEFT JOIN DOCENTE AS d ON d.ci = u.ci
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
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY u.ci";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $usuarios;
    }
}