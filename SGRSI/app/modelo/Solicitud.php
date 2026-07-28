<?php

/**
 * @brief Representa una solicitud del sistema.
 *
 * Almacena los datos de la solicitud y la información del docente asociado.
 */
class Solicitud
{
    /**
     * @brief Identificador de la solicitud.
     */
    private string $id;

    /**
     * @brief Asunto de la solicitud.
     */
    private string $asunto;

    /**
     * @brief Descripción de la solicitud.
     */
    private string $descripcion;

    /**
     * @brief Fecha límite de la solicitud.
     */
    private string $fechaLimite;

    /**
     * @brief Hora límite de la solicitud.
     */
    private string $horaLimite;

    /**
     * @brief Indica si la solicitud está finalizada.
     */
    private bool $finalizada;

    /**
     * @brief Cédula del docente asociado a la solicitud.
     */
    private string $ciDocente;

    /**
     * @brief Nombre del docente asociado a la solicitud.
     */
    private string $nombre;

    /**
     * @brief Construye un objeto Solicitud.
     *
     * @param string $id Identificador de la solicitud.
     * @param string $asunto Asunto de la solicitud.
     * @param string $descripcion Descripción de la solicitud.
     * @param string $fechaLimite Fecha límite de la solicitud.
     * @param string $horaLimite Hora límite de la solicitud.
     * @param bool $finalizada Indica si la solicitud está finalizada.
     * @param string $ciDocente Cédula del docente asociado.
     * @param string $nombre Nombre del docente asociado.
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
     * @brief Obtiene el identificador de la solicitud.
     *
     * @return string Identificador de la solicitud.
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene el asunto de la solicitud.
     *
     * @return string Asunto de la solicitud.
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
     * @brief Comprueba si la solicitud está finalizada.
     *
     * @return bool true si está finalizada;
     *              false en caso contrario.
     */

    public function estaFinalizada(): bool
    {
        return $this->finalizada;
    }

    /**
     * @brief Obtiene la fecha límite de la solicitud.
     *
     * @return string Fecha límite de la solicitud.
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
    public function getNombre(): string
    {
        return $this->nombre;
    }

}