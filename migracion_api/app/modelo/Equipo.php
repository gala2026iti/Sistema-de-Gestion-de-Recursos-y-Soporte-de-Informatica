<?php

class Equipo
{
    private string $id;
    private ?string $idUbicacion;
    private ?string $tipoUbicacion;
    private string $fechaCreacion;
    private string $horaCreacion;
    private ?string $ultimaIntervencion;
    private bool $activo;
    private ?string $posicion;
    private int $totalIncidencias;

    public function __construct(
        string $idEquipo,
        string $fechaCreacion,
        string $horaCreacion,
        ?string $ultimaIntervencion,
        bool $activo,
        ?string $idUbicacion,
        ?string $tipoUbicacion,
        ?string $posicion,
        int $totalIncidencias
    ) {
        $this->id = $idEquipo;
        $this->fechaCreacion = $fechaCreacion;
        $this->horaCreacion = $horaCreacion;
        $this->ultimaIntervencion = $ultimaIntervencion;
        $this->activo = $activo;
        $this->idUbicacion = $idUbicacion;
        $this->tipoUbicacion = $tipoUbicacion;
        $this->posicion = $posicion;
        $this->totalIncidencias = $totalIncidencias;
    }

    public function getIdEquipo(): string
    {
        return $this->id;
    }

    public function getUbicacion(): ?string
    {
        return $this->idUbicacion;
    }

    public function getTipoUbicacion(): ?string
    {
        return $this->tipoUbicacion;
    }

    public function getPosicion(): ?string
    {
        return $this->posicion;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function getHoraCreacion(): string
    {
        return $this->horaCreacion;
    }

    public function getUltimaIntervencion(): ?string
    {
        return $this->ultimaIntervencion;
    }

    public function estaActivo(): bool
    {
        return $this->activo;
    }

    public function getTotalIncidencias(): int
    {
        return $this->totalIncidencias;
    }
}