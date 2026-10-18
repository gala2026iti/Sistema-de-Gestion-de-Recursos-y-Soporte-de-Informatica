<?php

/**
 * @brief Representa una solicitud del sistema.
 *
 * Almacena los datos de la solicitud y la información del docente asociado.
 */
class Prestamo
{
    /**
     * @brief Identificador de la solicitud.
     */
    private string $id;

    /**
     * @brief Asunto de la solicitud.
     */
    private string $ciTecnico;

    /**
     * @brief Descripción de la solicitud.
     */
    private string $nombrePrestado;

    /**
     * @brief Fecha límite de la solicitud.
     */
    private string $ciPrestado;

    /**
     * @brief Hora límite de la solicitud.
     */
    private string $fechaFin;

    /**
     * @brief Indica si la solicitud está finalizada.
     */
    private bool $horaFin;

        /**
     * @brief Indica si la solicitud está finalizada.
     */
    private string $nombreTecnico;

        /**
     * @brief Indica si la solicitud está finalizada.
     */
    private bool $devuelto;

    

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
        string $ciTecnico,
        string $nombrePrestado,
        string $ciPrestado,
        string $fechaFin,
        string $horaFin,
        string $nombreTecnico,
        bool $devuelto
        ) {
        $this->id = $id;
        $this->ciTecnico = $ciTecnico;
        $this->nombrePrestado = $nombrePrestado;
        $this->ciPrestado = $ciPrestado;
        $this->fechaFin = $fechaFin;
        $this->horaFin = $horaFin;
        $this->nombreTecnico = $nombreTecnico;
        $this->devuelto = $devuelto;
    }

    /**
     * @brief Obtiene el identificador de la solicitud.
     *
     * @return string Identificador de la solicitud.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene el asunto de la solicitud.
     *
     * @return string Asunto de la solicitud.
     */
    public function getCiTecnico(): string
    {
        return $this->ciTecnico;
    }

    /**
     * @brief Obtiene la descripción de la solicitud.
     *
     * @return string Descripción de la solicitud.
     */
    public function getNombrePrestado(): string
    {
        return $this->nombrePrestado;
    }

    /**
     * @brief Obtiene la cédula del prestado.
     *
     * @return string Cédula del prestado.
     */
    public function getCiPrestado(): string
    {
        return $this->ciPrestado;
    }

    /**
     * @brief Obtiene la fecha de finalización.
     *
     * @return string Fecha de finalización.
     */
    public function getFechaFin(): string
    {
        return $this->fechaFin;
    }

    /**
     * @brief Obtiene la hora de finalización.
     *
     * @return string Hora de finalización.
     */
    public function getHoraFin(): string
    {
        return $this->horaFin;
    }

    /**
     * @brief Obtiene el nombre del técnico.
     *
     * @return string Nombre del técnico.
     */
    public function getNombreTecnico(): string
    {
        return $this->nombreTecnico;
    }

    /**
     * @brief Comprueba si la solicitud está finalizada.
     *
     * @return bool true si está finalizada;
     *              false en caso contrario.
     */

    public function estaDevuelto(): bool
    {
        return $this->devuelto;
    }


}