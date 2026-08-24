<?php

require_once __DIR__ . "/../Prestamo.php";

/**
 * @brief Gestiona las consultas relacionadas con las solicitudes.
 *
 * Consulta la información de las solicitudes y proporciona métodos para su gestión.
 */
class CargarPrestamos
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
     * @return Prestamo|null Usuario encontrado; null si no existe.
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