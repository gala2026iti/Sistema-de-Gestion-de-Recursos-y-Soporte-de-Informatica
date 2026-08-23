<?php

/**
 * @brief Representa un usuario del sistema.
 *
 * Contiene los datos necesarios para identificar al usuario,
 * validar su acceso y determinar los roles que posee.
 */
class Solicitud
{
    /**
     * @brief Cédula de identidad del usuario.
     */
    private string $id;

    /**
     * @brief Hash de la contraseña del usuario.
     */
    private string $asunto;

    /**
     * @brief Indica si el usuario está activo en el sistema.
     */
    private string $descripcion;

    /**
     * @brief Indica si posee el rol administrador.
     */
    private string $fechaLimite;

    /**
     * @brief Indica si posee el rol técnico.
     */
    private string $horaLimite;

        /**
     * @brief Indica si posee el rol técnico.
     */
    private bool $finalizada;

    /**
     * @brief Indica si posee el rol docente.
     */
    private string $ciDocente;

      /**
     * @brief Indica si posee el rol docente.
     */
    private string $nombre;

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
        string $asunto,
        string $descripcion,
        string $fechaLimite,
        string $horaLimite,
        bool $finalizada,
        string $ciDocente,
        string $nombre
    ) {
        $this->id = $id;
        $this->asunto = $asunto;
        $this->descripcion = $descripcion;
        $this->fechaLimite = $fechaLimite;
        $this->horaLimite = $horaLimite;
        $this->finalizada = $finalizada;
        $this->ciDocente = $ciDocente;
        $this->nombre = $nombre;
    }

    /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene el hash de la contraseña.
     *
     * @return string Hash de la contraseña.
     */
    public function getAsunto(): string
    {
        return $this->asunto;
    }

    /**
     * @brief Obtiene la descripción de la solicitud.
     *
     * @return string Descripción de la solicitud.
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * @brief Comprueba si el usuario está activo.
     *
     * @return bool true si está activo; false en caso contrario.
     */

    public function estaFinalizada(): bool
    {
        return $this->finalizada;
    }

    /**
     * @brief Comprueba si el usuario tiene una sesión activa.
     *
     * @return bool true si está activo; false en caso contrario.
     */
    public function getFechaLimite(): string
    {
        return $this->fechaLimite;
    }

    /**
     * @brief Obtiene la hora límite de la solicitud.
     *
     * @return string Hora límite de la solicitud.
     */
    public function getHoraLimite(): string
    {
        return $this->horaLimite;
    }

    /**
     * @brief Comprueba si el usuario es administrador.
     *
     * @return bool true si posee el rol administrador.
     */
    public function getCiDocente(): string
    {
        return $this->ciDocente;
    }

    /**
     * @brief Comprueba si el usuario es técnico.
     *
     * @return bool true si posee el rol técnico.
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

}