<?php

require_once __DIR__ . "/Usuario.php";

/**
 * @brief Gestiona las consultas relacionadas con los usuarios.
 *
 * Esta clase pertenece a la capa modelo del patrón MVC y se encarga
 * de consultar la información de los usuarios en la base de datos.
 *
 * Los roles se determinan mediante las tablas ADMINISTRADOR, TECNICO
 * y DOCENTE. Un usuario puede pertenecer a uno o varios roles
 * simultáneamente.
 */
class AccesoDatosUsuario
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
     * Se utiliza principalmente durante el inicio de sesión.
     * Además de los datos del usuario, determina independientemente
     * los roles administrador, técnico y docente.
     *
     * @param string $cedula Cédula del usuario sin puntos ni guiones.
     *
     * @return Usuario|null Objeto Usuario si existe; null si no se encuentra.
     */
    public function buscarUsuario(string $cedula): ?Usuario
    {
        $sql = "
            SELECT
                u.ci AS cedula,
                u.clave AS claveHash,
                u.activo AS sesionActiva,

                CASE
                    WHEN a.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS administrador,

                CASE
                    WHEN t.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS tecnico,

                CASE
                    WHEN d.ci IS NOT NULL THEN TRUE
                    ELSE FALSE
                END AS docente

            FROM USUARIO AS u

            LEFT JOIN ADMINISTRADOR AS a
                ON a.ci = u.ci

            LEFT JOIN TECNICO AS t
                ON t.ci = u.ci

            LEFT JOIN DOCENTE AS d
                ON d.ci = u.ci

            WHERE u.ci = :cedula
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        $consulta = null;

        if ($usuario === false) {
            return null;
        }

        return new Usuario(
            $usuario["cedula"],
            $usuario["claveHash"],
            (bool) $usuario["sesionActiva"],
            (bool) $usuario["administrador"],
            (bool) $usuario["tecnico"],
            (bool) $usuario["docente"]
        );
    }

    /**
     * @brief Obtiene los usuarios registrados aplicando filtros opcionales.
     *
     * El filtro de rol comprueba las tablas específicas de cada rol,
     * permitiendo encontrar usuarios que pertenezcan a varios roles.
     *
     * @param string $rol Rol por el cual filtrar.
     * @param string $estado Estado por el cual filtrar.
     *
     * @return array Lista de usuarios encontrados.
     */
    public function listarUsuarios(string $rol = "", string $estado = ""): array
    {
        $sql = "
        SELECT
            u.ci AS cedula,
            u.nombre,
            u.correo,
            u.activo,

            CASE
                WHEN a.ci IS NOT NULL THEN TRUE
                ELSE FALSE
            END AS administrador,

            CASE
                WHEN t.ci IS NOT NULL THEN TRUE
                ELSE FALSE
            END AS tecnico,

            CASE
                WHEN d.ci IS NOT NULL THEN TRUE
                ELSE FALSE
            END AS docente

        FROM USUARIO AS u

        LEFT JOIN ADMINISTRADOR AS a
            ON a.ci = u.ci

        LEFT JOIN TECNICO AS t
            ON t.ci = u.ci

        LEFT JOIN DOCENTE AS d
            ON d.ci = u.ci
        ";

        $condiciones = [];
        $parametros = [];

        if ($estado === "activo") {
            $condiciones[] = "u.activo = :activo";
            $parametros["activo"] = true;

        } elseif ($estado === "inactivo") {
            $condiciones[] = "u.activo = :activo";
            $parametros["activo"] = false;
        }

        if ($rol === "administrador") {
            $condiciones[] = "a.ci IS NOT NULL";

        } elseif ($rol === "tecnico") {
            $condiciones[] = "t.ci IS NOT NULL";

        } elseif ($rol === "docente") {
            $condiciones[] = "d.ci IS NOT NULL";
        }

        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }

        $sql .= " ORDER BY u.ci";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute($parametros);

        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta = null;

        return $usuarios;
    }
        /**
     * @brief Cambia el estado de un usuario.
     *
     * Actualiza el campo "activo" de la tabla USUARIO.
     * Este método permite activar o desactivar un usuario
     * sin modificar los roles que tenga asignados.
     *
     * @param string $cedula Cédula del usuario cuyo estado se modificará.
     * @param bool $activo Nuevo estado del usuario.
     *
     * @return bool true si el usuario fue actualizado correctamente;
     *              false si ocurrió un error.
     */
    public function cambiarEstadoUsuario(string $cedula, bool $activo): bool
    {
        $sql = "
            UPDATE USUARIO
            SET activo = :activo
            WHERE ci = :cedula
        ";

        try {

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "activo" => $activo,
                "cedula" => $cedula
            ]);

            return $consulta->rowCount() > 0;

        } catch (PDOException $error) {

            return false;
        }
    }
        /**
     * @brief Modifica los datos principales y los roles de un usuario.
     *
     * Actualiza el nombre, correo y roles del usuario.
     * La contraseña solamente se modifica si se proporciona
     * un nuevo hash.
     *
     * @param string $cedula Cédula del usuario que se modificará.
     * @param string $nombre Nuevo nombre del usuario.
     * @param string $correo Nuevo correo del usuario.
     * @param array $roles Roles que tendrá el usuario.
     * @param string|null $claveHash Nuevo hash de contraseña,
     *        o null para conservar la contraseña actual.
     *
     * @return bool true si la modificación se realizó correctamente;
     *              false si ocurrió algún error.
     */
    public function modificarUsuario(
        string $cedula,
        string $nombre,
        string $correo,
        array $roles,
        ?string $claveHash = null
    ): bool {

        try {

            /*
             * Comenzamos una transacción para que
             * todas las modificaciones se realicen juntas.
             */
            $this->conexion->beginTransaction();


            /*
             * Actualizamos los datos principales del usuario.
             */
            if ($claveHash === null) {

                $sqlUsuario = "
                    UPDATE USUARIO
                    SET
                        nombre = :nombre,
                        correo = :correo
                    WHERE ci = :cedula
                ";

                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "correo" => $correo,
                    "cedula" => $cedula
                ];

            } else {

                $sqlUsuario = "
                    UPDATE USUARIO
                    SET
                        nombre = :nombre,
                        correo = :correo,
                        clave = :clave
                    WHERE ci = :cedula
                ";

                $parametrosUsuario = [
                    "nombre" => $nombre,
                    "correo" => $correo,
                    "clave" => $claveHash,
                    "cedula" => $cedula
                ];
            }


            $consultaUsuario =
                $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute($parametrosUsuario);


            /*
             * Eliminamos las relaciones actuales
             * del usuario con sus roles.
             */
            $tablasRoles = [
                "ADMINISTRADOR",
                "TECNICO",
                "DOCENTE"
            ];

            foreach ($tablasRoles as $tabla) {

                $sqlEliminarRol = "
                    DELETE FROM $tabla
                    WHERE ci = :cedula
                ";

                $consultaEliminarRol =
                    $this->conexion->prepare($sqlEliminarRol);

                $consultaEliminarRol->execute([
                    "cedula" => $cedula
                ]);
            }


            /*
             * Volvemos a insertar el usuario
             * en las tablas correspondientes
             * a los roles seleccionados.
             */
            foreach ($roles as $rol) {

                switch ($rol) {

                    case "administrador":

                        $sqlRol = "
                            INSERT INTO ADMINISTRADOR (ci)
                            VALUES (:cedula)
                        ";

                        break;

                    case "tecnico":

                        $sqlRol = "
                            INSERT INTO TECNICO (ci)
                            VALUES (:cedula)
                        ";

                        break;

                    case "docente":

                        $sqlRol = "
                            INSERT INTO DOCENTE (ci)
                            VALUES (:cedula)
                        ";

                        break;

                    default:

                        $this->conexion->rollBack();

                        return false;
                }


                $consultaRol =
                    $this->conexion->prepare($sqlRol);

                $consultaRol->execute([
                    "cedula" => $cedula
                ]);
            }


            /*
             * Confirmamos todos los cambios.
             */
            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {

            /*
             * Si ocurre algún error, deshacemos
             * todas las modificaciones.
             */
            if ($this->conexion->inTransaction()) {

                $this->conexion->rollBack();
            }

            return false;
        }
    }
}