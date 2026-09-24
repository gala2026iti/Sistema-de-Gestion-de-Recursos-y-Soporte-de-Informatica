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
     * @param string $orden Ordenamiento solicitado.
     * @param string $estado Estado por el cual filtrar.
     *
     * @return array Lista de equipos encontrados.
     */
    public function listarEquipos(?string $orden = "", ?string $estado = "", ?string $ubicacion = "", ?string $tipoUbicacion = ""): array
    {
        $parametros = [];

        if ($_SESSION['tecnico']):
            $sql = "
                    SELECT
                        e.id AS idEquipo,
                        e.activo,
                        eru.idUbicacion,
                        eru.tipoUbicacion,
                        eru.posicion
                    FROM EQUIPO AS e
                    
                    LEFT JOIN equipo_reside_ubicacion AS eru
                        ON e.id = eru.idEquipo

                ";

        elseif ($_SESSION['administrador']):
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
                        COUNT(eugt.idEquipo) AS totalIncidencias
                    FROM EQUIPO AS e
                    LEFT JOIN equipo_reside_ubicacion AS eru
                        ON e.id = eru.idEquipo
                    LEFT JOIN equipo_ubicacion_genera_ticket AS eugt
                        ON e.id = eugt.idEquipo
                ";

            if (!empty($estado)) {
                $condiciones[] = "e.activo = :activo";
                $parametros["activo"] = $estado === "activo" ? 1 : 0;
            }

        endif;

        $condiciones = [];

        if (!empty($tipoUbicacion)) {
            $condiciones[] = "eru.tipoUbicacion = :tipoUbicacion";
            $parametros["tipoUbicacion"] = $tipoUbicacion;
        }

        if (!empty($ubicacion)) {
            $condiciones[] = "eru.idUbicacion = :ubicacion";
            $parametros["ubicacion"] = $ubicacion;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        if($_SESSION['tecnico']) {
        $sql .= " AND e.activo = TRUE";

        }

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

            if (!empty($orden)) {
                switch ($orden):
                    case "reciente":
                        $sql .= "ORDER BY e.ultimaIntervencion DESC";
                        break;
                    case "antiguo":
                        $sql .= "ORDER BY e.ultimaIntervencion ASC";
                        break;
                    case "masincidencias":
                        $sql .= "ORDER BY totalIncidencias DESC";
                        break;
                    case "menosincidencias":
                        $sql .= "ORDER BY totalIncidencias ASC";
                        break;
                endswitch;
            } else {
                $sql .= "
                        ORDER BY e.id ASC
                    ";
            }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $equipos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta = null;


        return $equipos;
    }
}
