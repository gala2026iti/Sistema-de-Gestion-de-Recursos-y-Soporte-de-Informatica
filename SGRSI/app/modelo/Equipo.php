<?php

/**
 * @brief Representa un equipo tecnológico del sistema.
 *
 * Almacena su identificador, fecha y hora de creación, última intervención
 * y estado de actividad.
 */
class Equipo
{
    /**
     * @brief Identificador del equipo.
     */
    private string $id;

    /**
     * @brief Fecha de creación del equipo.
     */
    private string $fechaCreacion;

    /**
     * @brief Hora de creación del equipo.
     */
    private string $horaCreacion;

    /**
     * @brief Fecha de la última intervención del equipo.
     */
    private string $ultimaIntervencion;


    /**
     * @brief Estado de actividad del equipo.
     */
    private string $activo;


    /**
     * @brief Construye un objeto Equipo.
     *
     * @param string $id Identificador del equipo.
     * @param string $fechaCreacion Fecha de creación del equipo.
     * @param string $horaCreacion Hora de creación del equipo.
     * @param string $ultimaIntervencion Fecha de la última intervención.
     * @param string $activo Estado de actividad del equipo.
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
     * @brief Obtiene el identificador del equipo.
     *
     * @return string Identificador del equipo.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene la fecha de creación del equipo.
     *
     * @return string Fecha de creación.
     */
    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    /**
     * @brief Obtiene la hora de creación del equipo.
     *
     * @return string Hora de creación.
     */
    public function getHoraCreacion(): string
    {
        return $this->horaCreacion;
    }

    /**
     * @brief Obtiene la fecha de la última intervención.
     *
     * @return string Fecha de la última intervención.
     */
    public function getUltimaIntervencion(): string
    {
        return $this->ultimaIntervencion;
    }

        /**
     * @brief Comprueba si el equipo está activo.
     *
     * @return bool true si está activo;
     *              false en caso contrario.
     */
    public function estaActivo(): bool
    {
        return $this->activo;
    }
}
