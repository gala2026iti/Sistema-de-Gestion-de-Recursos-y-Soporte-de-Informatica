<?php

class Usuario {
    private string $cedula;
    private string $nombre;
    private string $correo;
    private string $claveHash;
    private bool $admin;
    private bool $tecnico;
    private bool $docente;
    private bool $activo;

    public function __construct(string $cedula, string $nombre, string $correo, string $claveHash, bool $admin, bool $tecnico, bool $docente, bool $activo) {
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->claveHash = $claveHash;
        $this->admin = $admin;
        $this->tecnico = $tecnico;
        $this->docente = $docente;
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

    public function getAdmin(): bool {
        return $this->admin;
    }

    public function getTecnico(): bool {
        return $this->tecnico;
    }

    public function getDocente(): bool {
        return $this->docente;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

}

?>