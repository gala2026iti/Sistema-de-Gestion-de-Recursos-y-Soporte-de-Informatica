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
     *
     * @return Ticket|null Ticket encontrado; null si no existe.
     */
    public function buscarTicket(string $id): ?Ticket
    {
        $sql = "
            SELECT
                t.id,
                t.tipo,
                t.asunto,
                t.descripcion,
                t.gravedad,
                t.estado,
                t.fechaCreacion,
                t.horaCreacion,
                t.justificacion,
                drt.ciDocente,
                u_doc.nombre AS nombreDocente,
                eugt.idEquipo,
                eugt.idUbicacion
            FROM TICKET AS t
            LEFT JOIN docente_reporta_ticket AS drt ON drt.idTicket = t.id
            LEFT JOIN USUARIO AS u_doc ON u_doc.ci = drt.ciDocente
            LEFT JOIN equipo_ubicacion_genera_ticket AS eugt ON eugt.idTicket = t.id
            WHERE t.id = :id
            ORDER BY t.id DESC

        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            "id" => $id
        ]);

        $ticket = $consulta->fetch(PDO::FETCH_ASSOC);
        $consulta = null;

        if ($ticket === false) {
            return null;
        }

        return new Ticket(
            $ticket["id"],
            $ticket["tipo"],
            $ticket["asunto"],
            $ticket["descripcion"],
            $ticket["gravedad"],
            $ticket["estado"],
            $ticket["fechaCreacion"],
            $ticket["horaCreacion"],
            $ticket["justificacion"],
            $ticket["ciDocente"],
            $ticket["nombreDocente"],
            $ticket["idEquipo"],
            $ticket["idUbicacion"],
            $ticket["tipoUbicacion"]
        );
    }

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

    public function listarTickets(string $ciTecnico, string $orden, ?string $gravedad, ?string $tipo, ?string $estado, ?string $idTicket): array
    {
        $resultadoURL = $this->obtenerURL();

        $ticketsRegistrados = $resultadoURL[0];
        $ticketsPersonales = $resultadoURL[1];

        if ($ticketsRegistrados) {
            $sql = "
            SELECT 
                t.id,
                t.tipo,
                t.asunto,
                t.descripcion,
                t.gravedad,
                t.estado,
                t.fechaCreacion,
                t.horaCreacion,
                EXISTS (
                    SELECT 1
                    FROM COLABORADOR AS c
                    WHERE c.idTicket = t.id
                      AND c.ciTecnico = :ciTecnico
                ) AS esColaborador
            FROM TICKET AS t
                    ";

            $condiciones = [];
            $parametros = [];

            if (!empty($estado)) {
                $condiciones[] = "t.estado = :estado";
                $parametros["estado"] = $estado;
            }

            if (!empty($gravedad)) {
                $condiciones[] = "t.gravedad = :gravedad";
                $parametros["gravedad"] = $gravedad;
            }

            if (!empty($idTicket)) {
                $condiciones[] = "t.id = :idTicket";
                $parametros["idTicket"] = $idTicket;
            }

            if (!empty($tipo)) {
                $condiciones[] = "t.tipo = :tipo";
                $parametros["tipo"] = $tipo;
            }

        } else if ($ticketsPersonales) {
            $sql = "
        SELECT 
            t.id,
            t.estado,
            t.asunto
            FROM TICKET AS t

            LEFT JOIN COLABORADOR AS c
                ON c.idTicket = t.id

            WHERE c.ciTecnico = :ciTecnico;
        ";
        }
        $parametros["ciTecnico"] = $ciTecnico;


        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        if ($orden === "antiguo") {
            $sql .= " ORDER BY t.id ASC";
        } else if ($orden === "reciente") {
            $sql .= " ORDER BY t.id DESC";
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $tickets = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta = null;

        return $tickets;
    }
}
