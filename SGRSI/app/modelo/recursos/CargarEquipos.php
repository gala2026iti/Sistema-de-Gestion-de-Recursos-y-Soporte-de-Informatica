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
    public function listarEquipos(string $orden = "", string $estado = ""): array
    {
        $ubicacion = trim(htmlspecialchars($_GET["ubicacion"] ?? ""));

        $url = $_SERVER["REQUEST_URI"];
        $urlDividida = explode("/", $url);

        $paginaPrestamos = false;
        $paginaInventarioPrestamos = false;

        foreach ($urlDividida as $seccion) {
            if ($seccion === "tablaPrestamos.php") {
                $paginaPrestamos = true;
            } elseif ($seccion === "inventarioEquipos.php") {
                $paginaInventarioPrestamos = true;
            }
        }

        $parametros = [];

        if ($paginaPrestamos) {

            $sql = "
                SELECT
                    e.id AS idEquipo

                FROM EQUIPO AS e

                LEFT JOIN equipo_reside_ubicacion AS eru
                    ON e.id = eru.idEquipo

                LEFT JOIN prestamo_corresponde_equipo AS pce
                    ON e.id = pce.idEquipo

                WHERE eru.tipoUbicacion = 'prestamo'
                  AND pce.idPrestamo IS NULL

                ORDER BY e.id ASC
            ";

        } elseif ($paginaInventarioPrestamos) {

            $sql = "
                SELECT
                    e.id AS idEquipo,
                    e.activo,
                    p.id AS idPrestamo,
                    p.nombrePrestado,
                    p.ciPrestado,
                    p.fechaFin,
                    p.horaFin

                FROM EQUIPO AS e

                LEFT JOIN (
                    SELECT
                        pce.idEquipo,
                        p.id,
                        p.nombrePrestado,
                        p.ciPrestado,
                        p.fechaFin,
                        p.horaFin

                    FROM prestamo_corresponde_equipo AS pce

                    INNER JOIN PRESTAMO AS p
                        ON p.id = pce.idPrestamo

                    WHERE p.devuelto = FALSE
                ) AS p
                    ON p.idEquipo = e.id

                ORDER BY e.id ASC
            ";

        } else {

            $tipoUbicacion = $_GET["tipoUbicacion"] ?? "";

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

            $condiciones = [];

            if (
                $tipoUbicacion === "prestamo" ||
                $tipoUbicacion === "laboratorio" ||
                $tipoUbicacion === "taller"
            ) {
                $condiciones[] = "eru.tipoUbicacion = :tipoUbicacion";
                $parametros["tipoUbicacion"] = $tipoUbicacion;
            }

            if (is_numeric($ubicacion) && $ubicacion > 0) {
                $condiciones[] = "eru.idUbicacion = :ubicacion";
                $parametros["ubicacion"] = $ubicacion;
            }

            if ($estado === "activo") {
                $condiciones[] = "e.activo = :activo";
                $parametros["activo"] = 1;
            } elseif ($estado === "inactivo") {
                $condiciones[] = "e.activo = :activo";
                $parametros["activo"] = 0;
            }

            if (!empty($condiciones)) {
                $sql .= " WHERE " . implode(" AND ", $condiciones);
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

            if ($orden === "reciente") {
                $sql .= "
                    ORDER BY e.ultimaIntervencion DESC
                ";
            } elseif ($orden === "antiguo") {
                $sql .= "
                    ORDER BY e.ultimaIntervencion ASC
                ";
            } elseif ($orden === "masincidencias") {
                $sql .= "
                    ORDER BY totalIncidencias DESC
                ";
            } elseif ($orden === "menosincidencias") {
                $sql .= "
                    ORDER BY totalIncidencias ASC
                ";
            } else {
                $sql .= "
                    ORDER BY e.id ASC
                ";
            }
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        $equipos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $consulta = null;

        return $equipos;
    }
}