<?php

require_once __DIR__ . "/../Solicitud.php";

/**
 * @brief Gestiona las consultas relacionadas con las solicitudes.
 *
 * Permite obtener solicitudes y filtrarlas por su estado de finalización.
 */
class CargarSolicitudes
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
 * @brief Obtiene las solicitudes registradas.
 *
 * @param string $estado Estado por el cual filtrar: pendiente o finalizado.
 *
 * @return array Lista de solicitudes encontradas.
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
            u.nombre

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