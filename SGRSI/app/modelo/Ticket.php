<?php

/**
 * @brief Representa un usuario del sistema.
 *
 * Contiene los datos necesarios para identificar al usuario,
 * validar su acceso y determinar los roles que posee.
 */
class Ticket
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
     * @brief Indica si el usuario está activo en el sistema.
     */
    private string $asunto;

    /**
     * @brief Indica si posee el rol administrador.
     */
    private string $descripcion;

    /**
     * @brief Indica si posee el rol técnico.
     */
    private string $gravedad;

    /**
     * @brief Indica si posee el rol docente.
     */
    private string $estado;

        /**
     * @brief Indica si posee el rol docente.
     */
    private string $fechaCreacion;

        /**
     * @brief Indica si posee el rol docente.
     */
    private string $horaCreacion;

        /**
     * @brief Indica si posee el rol docente.
     */
    private string $justificación;

        /**
     * @brief Indica si posee el rol docente.
     */
    private string $ciDocente;

        /**
     * @brief Indica si posee el rol docente.
     */
    private string $nombreDocente;

        /**
     * @brief Indica si posee el rol docente.
     */
    private string $idEquipo;

            /**
         * @brief Indica si posee el rol docente.
         */
        private string $idUbicacion;

                /**
     * @brief Indica si posee el rol docente.
     */
    private string $tipoUbicacion;

            /**
     * @brief Indica si posee el rol docente.
     */
    private string $justificacion;

    /**
     * @brief Construye un objeto Usuario.
     *
     * @param string $id Cédula de identidad del usuario.
     * @param string $tipo Tipo de ticket.
     * @param string $asunto Asunto del ticket.
     * @param string $descripcion Descripción del ticket.
     * @param string $gravedad Gravedad del ticket.
     * @param string $estado Estado del ticket.
     * @param string $fechaCreacion Fecha de creación del ticket.
     * @param string $horaCreacion Hora de creación del ticket.
     * @param string $justificacion Justificación del ticket.
     * @param string $ciDocente Cédula del docente asociado.
     * @param string $nombreDocente Nombre del docente asociado.
     * @param string $idEquipo ID del equipo asociado.
     * @param string $idUbicacion ID de la ubicación asociada.
     */
    public function __construct(
        string $id,
        string $tipo,
        string $asunto,
        string $descripcion,
        string $gravedad,
        string $estado,
        string $fechaCreacion,
        string $horaCreacion,
        string $justificacion,
        string $ciDocente,
        string $nombreDocente,
        string $idEquipo,
        string $idUbicacion,
        string $tipoUbicacion
    ) {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->asunto = $asunto;
        $this->descripcion = $descripcion;
        $this->gravedad = $gravedad;
        $this->estado = $estado;
        $this->fechaCreacion = $fechaCreacion;
        $this->horaCreacion = $horaCreacion;
        $this->justificacion = $justificacion;
        $this->ciDocente = $ciDocente;
        $this->nombreDocente = $nombreDocente;
        $this->idEquipo = $idEquipo;
        $this->idUbicacion = $idUbicacion;
        $this->tipoUbicacion = $tipoUbicacion;
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
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getAsunto(): string
    {
        return $this->asunto;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getGravedad(): string
    {
        return $this->gravedad;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getEstado(): string
    {
        return $this->estado;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getHoraCreacion(): string
    {
        return $this->horaCreacion;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getJustificacion(): string
    {
        return $this->justificacion;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getCiDocente(): string
    {
        return $this->ciDocente;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getNombreDocente(): string
    {
        return $this->nombreDocente;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getIdEquipo(): string
    {
        return $this->idEquipo;
    }

        /**
     * @brief Obtiene la cédula del usuario.
     *
     * @return string Cédula del usuario.
     */
    public function getTipoUbicacion(): string
    {
        return $this->tipoUbicacion;
    }

}