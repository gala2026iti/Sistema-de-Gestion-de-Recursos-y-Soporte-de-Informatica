<?php

require_once __DIR__ . "/../Solicitud.php";

/**
 * @brief Gestiona las consultas relacionadas con las solicitudes.
 *
 * Consulta la información de las solicitudes y proporciona métodos para su gestión.
 */
class CargarSolicitudes
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
 * mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */

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
     * @return Solicitud|null Usuario encontrado; null si no existe.
     */

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
public function listarSolicitudes(string $estado = ""): array  //Opciones: pendiente, finalizada
    {
        $sql ="
            SELECT
            s.id,
            s.asunto,
            s.descripcion,
            s.fechaLimite,
            s.horaLimite,
            s.finalizada,
            dis.ciDocente,
            u.ci

            FROM SOLICITUD AS s

            INNER JOIN docente_ingresa_solicitud AS dis
            ON dis.idSolicitud = s.id

            INNER JOIN USUARIO AS u
            ON u.ci = dis.ciDocente
        ";

        $condiciones = [];
        $parametros = [];

        if ($estado === "finalizado") {
            $condiciones[] = "s.finalizada = :finalizada";
            $parametros["finalizada"] = 1;
        } elseif ($estado === "pendiente") {
            $condiciones[] = "s.finalizada = :finalizada";
            $parametros["finalizada"] = 0;
        }


        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY s.id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $solicitudes;
    }
}