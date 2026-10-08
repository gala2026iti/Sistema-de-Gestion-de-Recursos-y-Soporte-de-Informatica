<?php

/**
 * @brief Representa una ubicación del sistema.
 *
 * Almacena el identificador y el tipo de la ubicación.
 */
class Ubicacion
{
    /**
     * @brief Identificador de la ubicación.
     */
    private string $id;

    /**
     * @brief Tipo de la ubicación.
     */
    private string $tipo;

      /**
     * @brief Construye un objeto Ubicacion.
     *
     * @param string $id Identificador de la ubicación.
     * @param string $tipo Tipo de ubicación.
     */

    public function __construct(
        string $id,
        string $tipo
    ) {
        $this->id = $id;
        $this->tipo = $tipo;
    }

    /**
     * @brief Obtiene el identificador de la ubicación.
     *
     * @return string Identificador de la ubicación.
     */

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @brief Obtiene el tipo de ubicación.
     *
     * @return string Tipo de ubicación.
     */
    
    public function getTipo(): string
    {
        return $this->tipo;
    }

}
