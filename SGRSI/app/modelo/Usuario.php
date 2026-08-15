<?php

/**
 * @brief Representa un usuario del sistema.
 *
 * Contiene los datos necesarios para identificar al usuario,
 * validar su acceso y determinar los roles que posee.
 *
 * Un usuario puede pertenecer a uno o varios roles simultáneamente.
 * Los roles se determinan mediante las tablas ADMINISTRADOR,
 * TECNICO y DOCENTE de la base de datos.
 */
class Usuario
{
    /**
     * @brief Cédula de identidad del usuario.
     */
    private string $cedula;

    /**
     * @brief Hash de la contraseña del usuario.
     */
    private string $claveHash;

    /**
     * @brief Indica si el usuario está activo en el sistema.
     */
    private bool $sesionActiva;

    /**
     * @brief Indica si el usuario pertenece al rol administrador.
     */
    private bool $administrador;

    /**
     * @brief Indica si el usuario pertenece al rol técnico.
     */
    private bool $tecnico;

    /**
     * @brief Indica si el usuario pertenece al rol docente.
     */
    private bool $docente;

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
        string $cedula,
        string $claveHash,
        bool $sesionActiva,
        bool $administrador,
        bool $tecnico,
        bool $docente
    ) {
        $this->cedula = $cedula;
        $this->claveHash = $claveHash;
        $this->sesionActiva = $sesionActiva;
        $this->administrador = $administrador;
        $this->tecnico = $tecnico;
        $this->docente = $docente;
    }

    /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getCedula(): string
    {
        return $this->cedula;
    }

    /**
     * @brief Obtiene el hash de la contraseña.
     *
     * @return string Hash de la contraseña.
     */
    public function getClaveHash(): string
    {
        return $this->claveHash;
    }

    /**
     * @brief Comprueba si el usuario está activo.
     *
     * @return bool true si está activo, false en caso contrario.
     */
    public function estaActivo(): bool
    {
        return $this->sesionActiva;
    }

    /**
     * @brief Comprueba si el usuario tiene una sesión activa.
     *
     * Mantiene compatibilidad con la estructura utilizada
     * en la plantilla proporcionada por el docente.
     *
     * @return bool true si está activo, false en caso contrario.
     */
    public function tieneSesionActiva(): bool
    {
        return $this->sesionActiva;
    }

    /**
     * @brief Comprueba si el usuario es administrador.
     *
     * @return bool true si posee el rol administrador.
     */
    public function esAdministrador(): bool
    {
        return $this->administrador;
    }

    /**
     * @brief Comprueba si el usuario es técnico.
     *
     * @return bool true si posee el rol técnico.
     */
    public function esTecnico(): bool
    {
        return $this->tecnico;
    }

    /**
     * @brief Comprueba si el usuario es docente.
     *
     * @return bool true si posee el rol docente.
     */
    public function esDocente(): bool
    {
        return $this->docente;
    }
}