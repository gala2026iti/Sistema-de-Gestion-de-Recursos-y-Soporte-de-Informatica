<?php

class Usuario {
    private string $cedula;
    private string $clave;
    private bool $activo;
    private string $rol;
    private string $correo;
    private string $nombre;


    public function __construct(string $cedula, string $clave, bool $activo, string $rol, string $correo, string $nombre) {
        $this->cedula = $cedula;
        $this->clave = $clave;
        $this->activo = $activo;
        $this->rol = $rol;
        $this->correo = $correo;
        $this->nombre = $nombre;
    }

    public function getCedula(): string {
        return $this->cedula;
    }

    public function getClave(): string {
        return $this->clave;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

    public function getRol(): string {
        return $this->rol;
    }

    public function getCorreo(): string {
        return $this->correo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }
}


?>