<?php

class Usuario {
    private string $cedula;
    private string $nombre;
    private string $correo;
    private string $claveHash;
    private string $rol;
    private bool $activo;

    public function __construct(string $cedula, string $nombre, string $correo, string $claveHash, string $rol, bool $activo) {
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->claveHash = $claveHash;
        $this->rol = $rol;
        $this->activo = $activo;
    }

    public function getCedula(): string {
        return $this->cedula;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getCorreo(): string {
        return $this->correo;
    }

    public function getClaveHash(): string {
        return $this->claveHash;
    }

    public function getRol(): string {
        return $this->rol;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

}

?>