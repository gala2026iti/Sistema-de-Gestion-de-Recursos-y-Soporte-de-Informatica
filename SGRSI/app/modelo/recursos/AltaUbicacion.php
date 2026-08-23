<?php

/**
 * @brief Gestiona el registro de nuevos equipos.
 *
 * Inserta equipos en la tabla EQUIPO y los asocia con uno o varios
 * datos mediante las tablas administrador_maneja_equipo, equipo_ubicacion_genera_ticket, equipo_reside_ubicacion, entre otros.
 */
class AltaUbicacion
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
     * @param boolean $roles Roles que tendrá el usuario.
     *
     * @return bool true si el registro se realizó correctamente;
     *              false si ocurrió algún error.
     */
    public function registrarUbicacion(
        string $id,
        string $tipo
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlUbicacion = "
                INSERT INTO UBICACION (id, tipo)
                VALUES (:id, :tipo)
            ";

            $consultaEquipo = $this->conexion->prepare($sqlUbicacion);
            $consultaEquipo->execute([
                "id" => $id,
                "tipo" => $tipo
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