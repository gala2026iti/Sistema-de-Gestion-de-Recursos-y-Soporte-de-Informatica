<?php

/**
 * @brief Representa un ticket del sistema.
 *
 * Almacena sus datos principales y la información relacionada con el docente,
 * equipo y ubicación asociados.
 */
class Ticket
{
     /**
     * @brief Identificador del ticket.
     */
    private string $id;

    /**
     * @brief Tipo del ticket.
     */
    private string $tipo;

     /**
     * @brief Asunto del ticket.
     */
    private string $asunto;

    /**
     * @brief Descripción del ticket.
     */
    private string $descripcion;

    /**
     * @brief Nivel de gravedad del ticket.
     */
    private string $gravedad;

    /**
     * @brief Estado actual del ticket.
     */
    private string $estado;

    /**
     * @brief Fecha de creación del ticket.
     */
    private string $fechaCreacion;

   /**
     * @brief Hora de creación del ticket.
     */
    private string $horaCreacion;

    /**
     * @brief Justificación asociada al ticket.
     */
    private string $justificación;

    /**
     * @brief Cédula del docente asociado al ticket.
     */
    private string $ciDocente;

    /**
     * @brief Nombre del docente asociado al ticket.
     */
    private string $nombreDocente;

    /**
     * @brief Identificador del equipo asociado al ticket.
     */
    private string $idEquipo;

    /**
    * @brief Identificador de la ubicación asociada al ticket.
    */
    private string $idUbicacion;

    /**
     * @brief Tipo de la ubicación asociada al ticket.
     */
    private string $tipoUbicacion;

    /**
     * @brief Justificación asociada al ticket.
     */
    private string $justificacion;

    /**
     * @brief Construye un objeto Ticket.
     *
     * @param string $id Identificador del ticket.
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
     * @param string $idEquipo Identificador del equipo asociado.
     * @param string $idUbicacion Identificador de la ubicación asociada.
     * @param string $tipoUbicacion Tipo de la ubicación asociada.
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
     * @brief Obtiene el identificador del ticket.
     *
     * @return string Identificador del ticket.
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene el tipo del ticket.
     *
     * @return string Tipo del ticket.
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

    /**
     * @brief Obtiene el asunto del ticket.
     *
     * @return string Asunto del ticket.
     */
    public function getAsunto(): string
    {
        return $this->asunto;
    }

    /**
     * @brief Obtiene la descripción del ticket.
     *
     * @return string Descripción del ticket.
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * @brief Obtiene la gravedad del ticket.
     *
     * @return string Gravedad del ticket.
     */
    public function getGravedad(): string
    {
        return $this->gravedad;
    }

    /**
     * @brief Obtiene el estado del ticket.
     *
     * @return string Estado del ticket.
     */
    public function getEstado(): string
    {
        return $this->estado;
    }

    /**
     * @brief Obtiene la fecha de creación del ticket.
     *
     * @return string Fecha de creación.
     */
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    /**
     * @brief Obtiene la hora de creación del ticket.
     *
     * @return string Hora de creación.
     */
    public function getHoraCreacion(): string
    {
        return $this->horaCreacion;
    }

    /**
     * @brief Obtiene la justificación del ticket.
     *
     * @return string Justificación del ticket.
     */
    public function getJustificacion(): string
    {
        return $this->justificacion;
    }

    /**
     * @brief Obtiene la cédula del docente asociado.
     *
     * @return string Cédula del docente.
     */
    public function getCiDocente(): string
    {
        return $this->ciDocente;
    }

    /**
     * @brief Obtiene el nombre del docente asociado.
     *
     * @return string Nombre del docente.
     */
    public function getNombreDocente(): string
    {
        return $this->nombreDocente;
    }

    /**
     * @brief Obtiene el identificador del equipo asociado.
     *
     * @return string Identificador del equipo.
     */
    public function getIdEquipo(): string
    {
        return $this->idEquipo;
    }

    /**
     * @brief Obtiene el tipo de ubicación asociado.
     *
     * @return string Tipo de ubicación.
     */
    public function getTipoUbicacion(): string
    {
        return $this->tipoUbicacion;
    }

}