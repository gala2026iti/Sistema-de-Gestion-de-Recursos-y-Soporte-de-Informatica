<?php

/**
 * @brief Representa un usuario del sistema.
 *
 * Contiene los datos necesarios para identificar al usuario,
 * validar su acceso y determinar los roles que posee.
 */
class Equipo
{
    /**
     * @brief Cédula de identidad del usuario.
     */
    private string $id;

    /**
     * @brief Hash de la contraseña del usuario.
     */
    private string $fechaCreacion;

    /**
     * @brief Indica si el usuario está activo en el sistema.
     */
    private string $horaCreacion;

    /**
     * @brief Indica si posee el rol administrador.
     */
    private string $ultimaIntervencion;


    /**
     * @brief Indica si posee el rol administrador.
     */
    private string $activo;


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
        string $fechaCreacion,
        string $horaCreacion,
        string $ultimaIntervencion,
        string $activo
    ) {
        $this->id = $id;
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
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    /**
     * @brief Comprueba si el usuario está activo.
     *
     * @return string true si está activo; false en caso contrario.
     */
    public function getHoraCreacion(): string
    {
        return $this->horaCreacion;
    }

    /**
     * @brief Comprueba si el usuario está activo.
     *
     * @return string true si está activo; false en caso contrario.
     */
    public function getUltimaIntervencion(): string
    {
        return $this->ultimaIntervencion;
    }

    public function estaActivo(): bool
    {
        return $this->activo;
    }
}
