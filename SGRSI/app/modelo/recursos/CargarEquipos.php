<?php

require_once __DIR__ . "/../Equipo.php";

/**
 * @brief Gestiona las consultas relacionadas con los usuarios.
 *
 * Consulta la información de los usuarios y determina sus roles
 * mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */
class CargarEquipos
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
     * @return Equipo|null Usuario encontrado; null si no existe.
     */
public function listarEquipos(string $estado = "", string $cantIncidencias = "", string $ordenIntervencion = ""): array
    {
        $sql = "
            SELECT
                e.id AS idEquipo,
                e.fechaCreacion,
                e.horaCreacion,
                e.ultimaIntervencion,
                e.activo,
                COUNT(eugt.idTicket) AS totalIncidencias
            FROM EQUIPO AS e
            LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
                ON e.id = eugt.idEquipo
        ";
        $condiciones = [];
        $parametros = [];
        $orden = [];

        if ($estado === "activo") {
            $condiciones[] = "e.activo = :activo";
            $parametros["activo"] = 1;
        } elseif ($estado === "inactivo") {
            $condiciones[] = "e.activo = :activo";
            $parametros["activo"] = 0;
        }

        if ($cantIncidencias === "mayor") {
            $orden[] = "totalIncidencias DESC";
        } elseif ($cantIncidencias === "menor") {
            $orden[] = "totalIncidencias ASC";
        }

        if ($ordenIntervencion === "reciente") {
            $orden[] = "STR_TO_DATE(e.ultimaIntervencion, '%d/%m/%Y') DESC";
        } elseif ($ordenIntervencion === "antiguo") {
            $orden[] = "STR_TO_DATE(e.ultimaIntervencion, '%d/%m/%Y') ASC";
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " GROUP BY e.id, e.fechaCreacion, e.horaCreacion, e.ultimaIntervencion, e.activo";

        if (!empty($orden)) {
            $sql .= " ORDER BY " . implode(", ", $orden);
        } else {
            $sql .= " ORDER BY e.id ASC";
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $equipos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $equipos;
    }
}
