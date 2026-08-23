<?php

/**
 * @brief Gestiona el registro de nuevos equipos.
 *
 * Inserta equipos en la tabla EQUIPO y los asocia con uno o varios
 * datos mediante las tablas administrador_maneja_equipo, equipo_ubicacion_genera_ticket, equipo_reside_ubicacion, entre otros.
 */
class AltaEquipo
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
    public function registrarEquipo(
        string $idEquipo,
        string $fechaCreacion,
        string $horaCreacion,
        string $ultimaIntervencion,
        bool $activo,
        string $idUbicacion,
        string $tipoUbicacion,
        string $posicion
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlEquipo = "
                INSERT INTO EQUIPO (id, fechaCreacion, horaCreacion, ultimaIntervencion, activo)
                VALUES (:idEquipo, :fechaCreacion, :horaCreacion, NULL, TRUE)
            ";

            $sqlUbicacion = "
                INSERT INTO equipo_reside_ubicacion (idEquipo, idUbicacion, tipoUbicacion, posicion)
                VALUES (:idEquipo, :idUbicacion, :tipoUbicacion, :posicion)
            ";

            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute([
                "id" => $idEquipo,
                "fechaCreacion" => $fechaCreacion,
                "horaCreacion" => $horaCreacion,
                "ultimaIntervencion" => $ultimaIntervencion,
                "activo" => $activo
            ]);

            $consultaUbicacion = $this->conexion->prepare($sqlUbicacion);
            $consultaUbicacion->execute([
                "idEquipo" => $idEquipo,
                "idUbicacion" => $idUbicacion,
                "tipoUbicacion" => $tipoUbicacion,
                "posicion" => $posicion
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