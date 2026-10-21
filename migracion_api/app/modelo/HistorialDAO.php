<?php

class HistorialDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    public function listarUsuarios(string $fecha = ""): array
    {
        $sql = "
        SELECT
            h.fecha,
            h.hora,
            administrador.nombre AS nombreActor,
            h.ciAdmin AS ciActor,

            CASE h.tipoInteraccion
                WHEN 'creacion' THEN 'Registró un usuario'
                WHEN 'modificacion' THEN 'Modificó un usuario'
                WHEN 'activacion' THEN 'Activó un usuario'
                WHEN 'desactivacion' THEN 'Desactivó un usuario'
                ELSE h.tipoInteraccion
            END AS accion,

            CONCAT(
                usuario.nombre,
                ' (',
                h.ciUsuario,
                ')'
            ) AS recurso,

            '' AS detalle

        FROM administrador_modifica_usuario AS h

        INNER JOIN USUARIO AS administrador
            ON administrador.ci = h.ciAdmin

        INNER JOIN USUARIO AS usuario
            ON usuario.ci = h.ciUsuario
    ";

        $parametros = [];

        if ($fecha !== "") {
            $sql .= "
            WHERE h.fecha = :fecha
        ";

            $parametros["fecha"] = $fecha;
        }

        $sql .= "
        ORDER BY
            h.fecha DESC,
            h.hora DESC,
            h.id DESC
    ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function listarEquipos(string $fecha = ""): array
    {
        $sql = "
            SELECT
                h.fecha AS fecha,
                h.hora AS hora,
                actor.nombre AS nombreActor,
                h.ciAdmin AS ciActor,

                CASE h.tipoInteraccion
                    WHEN 'creacion' THEN 'Registró un equipo'
                    WHEN 'modificacion' THEN 'Modificó un equipo'
                    WHEN 'activacion' THEN 'Activó un equipo'
                    WHEN 'desactivacion' THEN 'Desactivó un equipo'
                    ELSE h.tipoInteraccion
                END AS accion,

                CONCAT(
                    'Equipo #',
                    h.idEquipo
                ) AS recurso,

                '' AS detalle

            FROM administrador_maneja_equipo AS h

            INNER JOIN USUARIO AS actor
                ON actor.ci = h.ciAdmin

            INNER JOIN EQUIPO AS e
                ON e.id = h.idEquipo
        ";

        $parametros = [];

        if ($fecha !== "") {
            $sql .= " WHERE h.fecha = :fecha";
            $parametros["fecha"] = $fecha;
        }

        $sql .= "
            ORDER BY
                h.fecha DESC,
                h.hora DESC,
                h.id DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function listarUbicaciones(string $fecha = ""): array
    {
        $sql = "
            SELECT
                h.fecha AS fecha,
                h.hora AS hora,
                actor.nombre AS nombreActor,
                h.ciAdmin AS ciActor,

                CASE h.tipoInteraccion
                    WHEN 'creacion' THEN 'Registró una ubicación'
                    WHEN 'modificacion' THEN 'Modificó una ubicación'
                    WHEN 'activacion' THEN 'Activó una ubicación'
                    WHEN 'desactivacion' THEN 'Desactivó una ubicación'
                    WHEN 'eliminacion' THEN 'Eliminó una ubicación'
                    ELSE h.tipoInteraccion
                END AS accion,

                CONCAT(
                    UPPER(LEFT(h.tipoUbicacion, 1)),
                    SUBSTRING(h.tipoUbicacion, 2),
                    ' ',
                    h.idUbicacion
                ) AS recurso,

                '' AS detalle

            FROM administrador_controla_ubicacion AS h

            INNER JOIN USUARIO AS actor
                ON actor.ci = h.ciAdmin

            INNER JOIN UBICACION AS u
                ON u.id = h.idUbicacion
                AND u.tipo = h.tipoUbicacion
        ";

        $parametros = [];

        if ($fecha !== "") {
            $sql .= " WHERE h.fecha = :fecha";
            $parametros["fecha"] = $fecha;
        }

        $sql .= "
            ORDER BY
                h.fecha DESC,
                h.hora DESC,
                h.id DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function listarTickets(string $fecha = ""): array
    {
        $sql = "
        SELECT
            historial.fecha,
            historial.hora,
            historial.nombreActor,
            historial.ciActor,
            historial.accion,
            historial.recurso,
            historial.detalle

        FROM (

            /*
             * CREACIÓN DEL TICKET
             */
            SELECT
                t.fechaCreacion AS fecha,
                t.horaCreacion AS hora,
                u.nombre AS nombreActor,
                d.ciDocente AS ciActor,

                'Creó el ticket' AS accion,

                CONCAT(
                    'Ticket #',
                    t.id,
                    ' - ',
                    t.asunto
                ) AS recurso,

                t.descripcion AS detalle

            FROM docente_reporta_ticket AS d

            INNER JOIN TICKET AS t
                ON t.id = d.idTicket

            INNER JOIN USUARIO AS u
                ON u.ci = d.ciDocente


            UNION ALL


            /*
             * GESTIÓN DEL TICKET
             */
            SELECT
                g.fecha AS fecha,
                g.hora AS hora,
                u.nombre AS nombreActor,
                g.ciTecnico AS ciActor,

                CASE g.tipoInteraccion
                    WHEN 'asignacion'
                        THEN 'Se asignó al ticket'
                    WHEN 'creacion' 
                        THEN 'Creó el ticket'
                    WHEN 'desasignacion'
                        THEN 'Se desasignó del ticket'

                    WHEN 'modificacion'
                        THEN 'Modificó el ticket'

                    WHEN 'cierre'
                        THEN 'Cerró el ticket'

                    WHEN 'resolucion'
                        THEN 'Cerró el ticket'

                    ELSE g.tipoInteraccion
                END AS accion,

                CONCAT(
                    'Ticket #',
                    t.id,
                    ' - ',
                    t.asunto
                ) AS recurso,

                CASE
                    WHEN g.tipoInteraccion IN ('cierre', 'resolucion')
                        THEN COALESCE(t.justificacion, '')
                    ELSE ''
                END AS detalle

            FROM tecnico_gestiona_ticket AS g

            INNER JOIN TICKET AS t
                ON t.id = g.idTicket

            INNER JOIN USUARIO AS u
                ON u.ci = g.ciTecnico


            UNION ALL


            /*
             * COMENTARIOS
             */
            SELECT
                c.fecha AS fecha,
                c.hora AS hora,
                u.nombre AS nombreActor,
                c.ciTecnico AS ciActor,

                'Agregó un comentario' AS accion,

                CONCAT(
                    'Ticket #',
                    t.id,
                    ' - ',
                    t.asunto
                ) AS recurso,

                c.texto AS detalle

            FROM tecnico_comenta_ticket AS c

            INNER JOIN TICKET AS t
                ON t.id = c.idTicket

            INNER JOIN USUARIO AS u
                ON u.ci = c.ciTecnico

        ) AS historial
    ";

        $parametros = [];

        if ($fecha !== "") {
            $sql .= "WHERE historial.fecha = :fecha";

            $parametros["fecha"] = $fecha;
        }

        $sql .= "
        ORDER BY
            historial.fecha DESC,
            historial.hora DESC
    ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function listarSolicitudes(string $fecha = ""): array
    {
        $sql = "
            SELECT
                historial.fecha,
                historial.hora,
                historial.nombreActor,
                historial.ciActor,
                historial.accion,
                historial.recurso,
                historial.detalle

            FROM (
                SELECT
                    i.fecha AS fecha,
                    i.hora AS hora,
                    u.nombre AS nombreActor,
                    i.ciDocente AS ciActor,
                    'Registró una solicitud' AS accion,

                    CONCAT(
                        'Solicitud #',
                        s.id,
                        ' - ',
                        s.asunto
                    ) AS recurso,

                    s.descripcion AS detalle

                FROM docente_ingresa_solicitud AS i

                INNER JOIN SOLICITUD AS s
                    ON s.id = i.idSolicitud

                INNER JOIN USUARIO AS u
                    ON u.ci = i.ciDocente


                UNION ALL


                SELECT
                    f.fecha AS fecha,
                    f.hora AS hora,
                    u.nombre AS nombreActor,
                    f.ciTecnico AS ciActor,
                    'Finalizó una solicitud' AS accion,

                    CONCAT(
                        'Solicitud #',
                        s.id,
                        ' - ',
                        s.asunto
                    ) AS recurso,

                    '' AS detalle

                FROM tecnico_finaliza_solicitud AS f

                INNER JOIN SOLICITUD AS s
                    ON s.id = f.idSolicitud

                INNER JOIN USUARIO AS u
                    ON u.ci = f.ciTecnico

            ) AS historial
        ";

        $parametros = [];

        if ($fecha !== "") {
            $sql .= " WHERE historial.fecha = :fecha";
            $parametros["fecha"] = $fecha;
        }

        $sql .= "
            ORDER BY
                historial.fecha DESC,
                historial.hora DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function listarPrestamos(string $fecha = ""): array
    {
        $sql = "
            SELECT
                h.fecha AS fecha,
                h.hora AS hora,
                u.nombre AS nombreActor,
                h.ciTecnico AS ciActor,

                CASE h.tipoInteraccion
                    WHEN 'prestamo' THEN 'Registró un préstamo'
                    WHEN 'devolucion' THEN 'Registró la devolución de un préstamo'
                    ELSE h.tipoInteraccion
                END AS accion,

                CONCAT(
                    'Préstamo #',
                    p.id,
                    ' - ',
                    p.nombrePrestado,
                    ' (',
                    p.ciPrestado,
                    ')'
                ) AS recurso,

                CONCAT(
                    'Equipo(s): ',
                    GROUP_CONCAT(
                        pe.idEquipo
                        ORDER BY pe.idEquipo
                        SEPARATOR ', '
                    )
                ) AS detalle

            FROM tecnico_tramita_prestamo AS h

            INNER JOIN PRESTAMO AS p
                ON p.id = h.idPrestamo

            INNER JOIN USUARIO AS u
                ON u.ci = h.ciTecnico

            LEFT JOIN prestamo_corresponde_equipo AS pe
                ON pe.idPrestamo = p.id
        ";

        $parametros = [];

        if ($fecha !== "") {
            $sql .= " WHERE h.fecha = :fecha";
            $parametros["fecha"] = $fecha;
        }

        $sql .= "
            GROUP BY
                h.id,
                h.fecha,
                h.hora,
                u.nombre,
                h.ciTecnico,
                h.tipoInteraccion,
                p.id,
                p.nombrePrestado,
                p.ciPrestado

            ORDER BY
                h.fecha DESC,
                h.hora DESC,
                h.id DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}