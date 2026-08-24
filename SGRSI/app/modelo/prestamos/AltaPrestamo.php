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
    public function registrarPrestamo(
        string $idPrestamo,
        string $ciTecnico,
        string $nombrePrestado,
        string $ciPrestado,
        string $fechaFin,
        string $horaFin,
        string $fecha,
        string $hora,
        string $tipoInteraccion
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlPrestamo = "
                INSERT INTO PRESTAMO (id, nombrePrestado, ciPrestado, fechaFin, horaFin, devuelto)
                VALUES (:idPrestamo, :nombrePrestado, :ciPrestado, :fechaFin, :horaFin, FALSE)
            ";

            $sqlTecnico = "
                INSERT INTO tecnico_tramita_prestamo (id, ciTecnico, idPrestamo, fecha, hora, tipoInteraccion)
                VALUES (:id, :ciTecnico, :idPrestamo, :fecha, :hora, :tipoInteraccion)
            ";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "idPrestamo" => $idPrestamo,
                "nombrePrestado" => $nombrePrestado,
                "ciPrestado" => $ciPrestado,
                "fechaFin" => $fechaFin,
                "horaFin" => $horaFin
            ]);

            $consultaTecnico = $this->conexion->prepare($sqlTecnico);
            $consultaTecnico->execute([
                "ciTecnico" => $ciTecnico,
                "idPrestamo" => $idPrestamo,
                "fecha" => $fecha,
                "hora" => $hora,
                "tipoInteraccion" => $tipoInteraccion
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