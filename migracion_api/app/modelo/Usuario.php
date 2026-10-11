<?php

/**
 * Representa un usuario del sistema.
 */
class Usuario
{
    private string $cedula;
    private string $nombre;
    private string $claveHash;
    private bool $activo;

    private bool $administrador;
    private bool $tecnico;
    private bool $docente;

    public function __construct(
        string $cedula,
        string $nombre,
        string $claveHash,
        bool $activo,
        bool $administrador,
        bool $tecnico,
        bool $docente
    ) {
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->claveHash = $claveHash;
        $this->activo = $activo;

        $this->administrador = $administrador;
        $this->tecnico = $tecnico;
        $this->docente = $docente;
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getClaveHash(): string
    {
        return $this->claveHash;
    }

    public function estaActivo(): bool
    {
        return $this->activo;
    }

    public function esAdministrador(): bool
    {
        return $this->administrador;
    }

    public function esTecnico(): bool
    {
        return $this->tecnico;
    }

    public function esDocente(): bool
    {
        return $this->docente;
    }
}