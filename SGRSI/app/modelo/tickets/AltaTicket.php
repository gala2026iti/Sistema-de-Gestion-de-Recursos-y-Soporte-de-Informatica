<?php

/**
 * @brief Gestiona el registro de nuevos usuarios.
 *
 * Inserta usuarios en la tabla USUARIO y los asocia con uno o varios
 * roles mediante las tablas ADMINISTRADOR, TECNICO y DOCENTE.
 */
class AltaTicket
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
    public function registrarTicket(
        string $id,
        string $tipo,
        string $asunto,
        string $descripcion,
        string $gravedad,
        string $estado,
        string $fechaCreacion,
        string $horaCreacion,
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlTicket = "
                INSERT INTO TICKET (id, tipo, asunto, descripcion, gravedad, estado, fechaCreacion, horaCreacion, justificacion)
                VALUES (:id, :tipo, :asunto, :descripcion, :gravedad, :estado, :fechaCreacion, :horaCreacion, NULL)
            ";

            $consultaTicket = $this->conexion->prepare($sqlTicket);
            $consultaTicket->execute([
                "id" => $id,
                "tipo" => $tipo,
                "asunto" => $asunto,
                "descripcion" => $descripcion,
                "gravedad" => $gravedad,
                "estado" => $estado,
                "fechaCreacion" => $fechaCreacion,
                "horaCreacion" => $horaCreacion,
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