<?php

require_once __DIR__ . "/../Prestamo.php";

/**
 * @brief Gestiona las consultas relacionadas con los préstamos.
 *
 * Permite obtener los préstamos pendientes de devolución con sus datos asociados.
 */
class CargarPrestamos
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
 * @brief Obtiene los préstamos pendientes de devolución.
 *
 * @return array Lista de préstamos encontrados con sus datos asociados.
 */
public function listarPrestamos(): array
    {
        $sql ="
            SELECT
                p.id,
                p.nombrePrestado,
                p.ciPrestado,
                p.fechaFin,
                p.horaFin,
                p.devuelto, 
                ttp.ciTecnico,
                pce.idEquipo,
                u.nombre AS nombreTecnico

            FROM PRESTAMO AS p

            INNER JOIN prestamo_corresponde_equipo AS pce
            ON pce.idPrestamo = p.id

            INNER JOIN tecnico_tramita_prestamo AS ttp
            ON ttp.idPrestamo = p.id

            INNER JOIN USUARIO AS u
            ON u.ci = ttp.ciTecnico

            WHERE p.devuelto = FALSE

            ORDER BY p.id
        ";

        $sql .= " ORDER BY s.id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $solicitudes;
    }
}