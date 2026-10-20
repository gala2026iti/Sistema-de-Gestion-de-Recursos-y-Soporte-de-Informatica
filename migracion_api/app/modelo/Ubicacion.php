<?php

class Ubicacion
{
    private string $id;
    private string $tipo;
    private bool $activo;

    public function __construct(
        string $id,
        string $tipo,
        bool $activo
    ) {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->activo = $activo;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }
}