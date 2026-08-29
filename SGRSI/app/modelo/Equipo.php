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
     * @brief Identificador del equipo.
     */
    private string $idUbicacion;

    /**
     * @brief Tipo de ubicación del equipo.
     */
    private string $tipoUbicacion;

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
    private bool $activo;

    /**
     * @brief Posicion del equipo dentro de una ubicacion.
     */
    private string $posicion;

    /**
     * @brief Cantidad de incidencias registrada de dicho equipo.
     */
    private string $totalIncidencias;




    /**
     * @brief Construye un objeto Equipo.
     *
     * @param string $id Identificador del equipo.
     * @param string $fechaCreacion Fecha de creación del equipo.
     * @param string $horaCreacion Hora de creación del equipo.
     * @param string $ultimaIntervencion Fecha de la última intervención.
     * @param bool $activo Estado de actividad del equipo.
     * @param string $idUbicacion Identificador de la ubicación del equipo.
     * @param string $tipoUbicacion Tipo de ubicación del equipo.
     * @param string $posicion Posición del equipo dentro de la ubicación.
     * @param string $totalIncidencias Cantidad de incidencias registradas del equipo.
     */

    public function __construct(
        string $idEquipo,
        string $fechaCreacion,
        string $horaCreacion,
        string $ultimaIntervencion,
        bool $activo,
        string $idUbicacion,
        string $tipoUbicacion,
        string $posicion,
        string $totalIncidencias
    ) {
        $this->id = $idEquipo;
        $this->idUbicacion = $idUbicacion;
        $this->tipoUbicacion = $tipoUbicacion;
        $this->fechaCreacion = $fechaCreacion;
        $this->horaCreacion = $horaCreacion;
        $this->ultimaIntervencion = $ultimaIntervencion;
        $this->activo = $activo;
        $this->posicion = $posicion;
        $this->totalIncidencias = $totalIncidencias;
    }

    /**
     * @brief Obtiene el identificador del equipo.
     *
     * @return string Identificador del equipo.
     */
    public function getIdEquipo(): string
    {
        return $this->id;
    }

     /**
     * @brief Obtiene la posicion del equipo dentro del salon.
     *
     * @return string Identificador de equipo dentro del salon.
     */
    public function getPosicion(): string
    {
        return $this->posicion;
    }

    /**
     * @brief Obtiene el Numero de la ubicación del equipo.
     *
     * @return string Numero de Ubicacion del equipo.
     */
    public function getUbicacion(): string
    {
        return $this->idUbicacion;
    }

        /**
     * @brief Obtiene el tipo de ubicación del equipo.
     *
     * @return string Tipo de ubicación del equipo.
     */
    public function getTipoUbicacion(): string
    {
        return $this->tipoUbicacion;
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
