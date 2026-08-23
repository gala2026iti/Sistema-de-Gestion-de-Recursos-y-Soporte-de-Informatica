<?php

require_once __DIR__ . "/../Ubicacion.php";

/**
 * @brief Gestiona las consultas relacionadas con los usuarios.
 *
 * Consulta la información de los usuarios y determina sus roles
 * mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */
class CargarUbicaciones
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
     * @return Ubicacion|null Usuario encontrado; null si no existe.
     */
public function listarUbicaciones(): array
    {
        $sql = "
            SELECT
                u.id,
                u.tipo
            FROM UBICACION AS u
        ";



        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $ubicaciones = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $ubicaciones;
    }
}
