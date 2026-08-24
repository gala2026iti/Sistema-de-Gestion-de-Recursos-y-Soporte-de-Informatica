<?php

require_once __DIR__ . "/../Equipo.php";

/**
 * @brief Gestiona las consultas relacionadas con los equipos.
 *
 * Permite listar equipos aplicando filtros y criterios de ordenamiento.
 */
class CargarEquipos
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye el acceso a datos.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

/**
 * @brief Obtiene los equipos registrados aplicando filtros opcionales.
 *
 * @param string $estado Estado por el cual filtrar.
 * @param string $cantIncidencias Orden por cantidad de incidencias.
 * @param string $ordenIntervencion Orden por fecha de última intervención.
 *
 * @return array Lista de equipos encontrados.
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
