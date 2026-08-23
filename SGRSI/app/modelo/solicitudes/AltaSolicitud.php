<?php

/**
 * @brief Gestiona el registro de nuevos usuarios.
 *
 * Inserta usuarios en la tabla USUARIO y los asocia con uno o varios
 * roles mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */
class AltaSolicitud
{
    /**
     * @brief Conexión con la base de datos.
     */
    private PDO $conexion;

    /**
     * @brief Construye el acceso para registrar usuarios.
     *
     * @param PDO $conexion Conexión PDO con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * @brief Registra un nuevo usuario y sus roles.
     *
     * Utiliza una transacción para garantizar que el usuario y sus
     * roles se registren como una única operación.
     *
     * @param string $cedula Cédula de identidad del usuario.
     * @param string $nombre Nombre del usuario.
     * @param string $correo Correo electrónico del usuario.
     * @param string $claveHash Contraseña almacenada mediante hash.
     * @param array $roles Roles que tendrá el usuario.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió algún error.
     */
    public function registrarSolicitud(
        string $id,
        string $asunto,
        string $descripcion,
        string $fechaLimite,
        string $horaLimite,
        string $ciDocente,
        string $fecha,
        string $hora
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlSolicitud = "
                INSERT INTO SOLICITUD (id, asunto, descripcion, fecha_limite, hora_limite, finalizada)
                VALUES (:idSolicitud, :asunto, :descripcion, :fechaLimite, :horaLimite, FALSE)
            ";

            $sqlDocente = "
                INSERT INTO docente_ingresa_solicitud (ciDocente, idSolicitud, fecha, hora)
                VALUES (:ciDocente, :idSolicitud, :fecha, :hora)
            ";

            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "idSolicitud" => $id,
                "asunto" => $asunto,
                "descripcion" => $descripcion,
                "fechaLimite" => $fechaLimite,
                "horaLimite" => $horaLimite
            ]);

            $consultaDocente = $this->conexion->prepare($sqlDocente);
            $consultaDocente->execute([
                "ciDocente" => $ciDocente,
                "idSolicitud" => $id,
                "fecha" => $fecha,
                "hora" => $hora
            ]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }
}