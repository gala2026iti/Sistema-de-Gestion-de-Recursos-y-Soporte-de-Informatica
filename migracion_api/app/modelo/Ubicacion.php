<?php

class Ubicacion
{
    private string $id;
    private string $tipo;

    public function __construct(
        string $id,
        string $tipo
    ) {
        $this->id = $id;
        $this->tipo = $tipo;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }
}