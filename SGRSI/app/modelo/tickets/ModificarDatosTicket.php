<?php

/**
 * @brief Gestiona la modificación de los datos de los usuarios.
 *
 * Permite actualizar los datos principales, la contraseña y los
 * roles asociados a un usuario.
 */
class ModificarDatosTicket
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function asignarme(
        string $idTicket
    ): bool {
        try {
            $ciTecnico = $_SESSION['ci'];
            $this->conexion->beginTransaction();

                $sqlColaborador = "
                INSERT INTO COLABORADOR (idTicket, ciTecnico)
                VALUES (:idTicket, :ciTecnico)
                ";

                

            $consultaColaborador = $this->conexion->prepare($sqlColaborador);
            $consultaColaborador->execute([
                "idTicket" => $idTicket,
                "ciTecnico" => $ciTecnico
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

        public function desasignarme(
        string $idTicket
    ): bool {
        try {
            $ciTecnico = $_SESSION['ci'];
            $this->conexion->beginTransaction();

                $sqlColaborador = "
                DELETE FROM COLABORADOR
                WHERE idTicket = :idTicket AND ciTecnico = :ciTecnico
                ";

            $consultaColaborador = $this->conexion->prepare($sqlColaborador);
            $consultaColaborador->execute([
                "idTicket" => $idTicket,
                "ciTecnico" => $ciTecnico
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