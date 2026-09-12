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

public function listarPrestamos(string $estado, string $id): array
    {
        $sql ="
            SELECT
                p.id,
                p.nombrePrestado,
                p.ciPrestado,
                p.fechaFin,
                p.horaFin,
                p.devuelto,
                e.idEquipo,
                t.ciTecnico,
                u.nombre AS nombreTecnico
            
            FROM PRESTAMO AS p
            
            LEFT JOIN (
                SELECT
                    idPrestamo,
                    MIN(idEquipo) AS idEquipo
                FROM prestamo_corresponde_equipo
                GROUP BY idPrestamo
            ) AS e
                ON e.idPrestamo = p.id
            
            LEFT JOIN (
                SELECT
                    idPrestamo,
                    MIN(ciTecnico) AS ciTecnico
                FROM tecnico_tramita_prestamo
                GROUP BY idPrestamo
            ) AS t
                ON t.idPrestamo = p.id
            
            LEFT JOIN USUARIO AS u
                ON u.ci = t.ciTecnico;
        ";

        $condiciones = [];
        $parametros = [];

        if($estado === "pendiente") {
            $condiciones[] = "p.devuelto = FALSE";
        } elseif($estado === "devuelto") {
            $condiciones[] = "p.devuelto = TRUE";        
            }

        if(is_numeric($id) && $id > 0) {
            $condiciones[] = "p.id = :id";
            $parametros["id"] = $id;
        }

            if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY p.id";


        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $prestamos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $prestamos;
    }
}