<?php

require_once __DIR__ . "/../Ticket.php";

/**
 * @brief Gestiona las consultas relacionadas con los tickets.
 *
 * Permite buscar un ticket por su identificador y obtener listados con filtros.
 */
class CargarTickets
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
     * @brief Busca un ticket por su identificador.
     *
     * @param string $id Identificador del ticket.



    /**
     * @brief Obtiene un listado aplicando filtros opcionales.
     *
     * @param string $tiempo Criterio de orden temporal.
     * @param string $gravedad Gravedad por la cual filtrar.
     * @param string $tipo Clasificación por la cual filtrar.
     * @param string $estado Estado por el cual filtrar.
     *
     * @return array Lista de registros obtenidos.
     */
    public function obtenerURL(): array
    {
        $url = $_SERVER["REQUEST_URI"];
        $urlDividida = explode("/", $url);

        $ticketsRegistrados = false;
        $ticketsPersonales = false;
        $detalleTicket = false;


        foreach ($urlDividida as $seccion) {
            $subseccion = explode("?", $seccion);
            foreach ($subseccion as $subsubseccion) {
                if ($subsubseccion === "homeTecnico.php") {
                    $ticketsRegistrados = true;
                    break;
                } elseif ($subsubseccion === "ticketsPersonales.php") {
                    $ticketsPersonales = true;
                } elseif ($subsubseccion === "detalleTicket.php") {
                    $detalleTicket = true;
                }
            }
        }
        return [$ticketsRegistrados, $ticketsPersonales, $detalleTicket];
    }

    public function listarTickets(string $ciTecnico, ?string $orden, ?string $gravedad, ?string $tipo, ?string $estado, ?string $idEquipo, ?string $idReporte): array
    {
        $resultadoURL = $this->obtenerURL();

        $ticketsRegistrados = $resultadoURL[0];
        $ticketsPersonales = $resultadoURL[1];
        $detalleTicket = $resultadoURL[2];

        $condiciones = [];
        $parametros = [];

        if ($ticketsRegistrados) {
            $sql = "
            SELECT 
                t.idReporte AS id,
                t.tipo,
                t.asunto,
                t.descripcion,
                t.gravedad,
                t.estado,
                t.fechaCreacion,
                t.horaCreacion,
                r.idEquipo,
                EXISTS (
                    SELECT 1
                    FROM COLABORADOR AS c
                    WHERE c.idReporte = t.idReporte
                      AND c.ciTecnico = :ciTecnico
                ) AS esColaborador
            FROM TICKET AS t

            LEFT JOIN REPORTE AS r
            ON r.id = t.idReporte
";


            if (!empty($estado)) {
                $condiciones[] = "t.estado = :estado";
                $parametros["estado"] = $estado;
            }

            if (!empty($gravedad)) {
                $condiciones[] = "t.gravedad = :gravedad";
                $parametros["gravedad"] = $gravedad;
            }

            if (!empty($idReporte)) {
                $condiciones[] = "t.idReporte = :idReporte";
                $parametros["idReporte"] = $idReporte;
            }

            if (!empty($tipo)) {
                $condiciones[] = "t.tipo = :tipo";
                $parametros["tipo"] = $tipo;
            }
        } else if ($ticketsPersonales) {
            $sql = "
        SELECT 
            t.idReporte AS id,
            t.estado,
            t.asunto
            FROM TICKET AS t

            LEFT JOIN COLABORADOR AS c
                ON c.idReporte = t.idReporte

            WHERE c.ciTecnico = :ciTecnico;
        ";
        } else if ($detalleTicket) {
            $sql = "
SELECT 
    t.idReporte as id,
    r.idEquipo,
    t.tipo,
    t.persona,
    t.asunto,
    t.descripcion,
    t.gravedad,
    t.estado,
    t.fechaCreacion,
    t.horaCreacion,

    (
        SELECT GROUP_CONCAT(
            c.ciTecnico
            ORDER BY c.ciTecnico
            SEPARATOR ','
        )
        FROM COLABORADOR AS c
        WHERE c.idReporte = t.idReporte

    ) AS colaboradores 
    FROM TICKET AS t

    LEFT JOIN REPORTE AS r ON 
        r.id = t.idReporte
        ";

            $condiciones[] = "t.idReporte = :idReporte";
            $parametros["idReporte"] = $idReporte;
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        if (!$detalleTicket) {
            $parametros["ciTecnico"] = $ciTecnico;

            if ($orden === "antiguo") {
                $sql .= " ORDER BY t.id ASC";
            } else if ($orden === "reciente") {
                $sql .= " ORDER BY t.id DESC";
            }
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $tickets = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta = null;

        return $tickets;
    }
}
