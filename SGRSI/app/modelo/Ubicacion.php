<?php

/**
 * @brief Representa un usuario del sistema.
 *
 * Contiene los datos necesarios para identificar al usuario,
 * validar su acceso y determinar los roles que posee.
 */
class Ubicacion
{
    /**
     * @brief Cédula de identidad del usuario.
     */
    private string $id;

    /**
     * @brief Hash de la contraseña del usuario.
     */
    private string $tipo;

    /**
     * @brief Construye un objeto Usuario.
     *
     * @param string $cedula Cédula de identidad del usuario.
     * @param string $claveHash Hash de la contraseña.
     * @param bool $sesionActiva Indica si el usuario está activo.
     * @param bool $administrador Indica si posee el rol administrador.
     * @param bool $tecnico Indica si posee el rol técnico.
     * @param bool $docente Indica si posee el rol docente.
     */
    public function __construct(
        string $id,
        string $tipo
    ) {
        $this->id = $id;
        $this->tipo = $tipo;
    }

    /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
        $this->fechaCreacion = $fechaCreacion;
        $this->horaCreacion = $horaCreacion;
        $this->ultimaIntervencion = $ultimaIntervencion;
        $this->activo = $activo;
    }

    /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene el hash de la contraseña.
     *
     * @return string Hash de la contraseña.
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

}
