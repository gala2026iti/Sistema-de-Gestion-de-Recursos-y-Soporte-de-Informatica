<?php
// app/modelo/Usuario.php

class Usuario {
    private string $cedula;
    private string $claveHash;
    private bool $sesionActiva;
    private bool $administrador;
    private bool $tecnico;
    private bool $docente;

    /**
     * Constructor parametrizado con las propiedades del usuario
     */
    public function __construct(
        string $cedula,
        string $claveHash,
        bool $sesionActiva,
        bool $administrador,
        bool $tecnico,
        bool $docente
    ) {
        $this->cedula        = $cedula;
        $this->claveHash    = $claveHash;
        $this->sesionActiva  = $sesionActiva;
        $this->administrador = $administrador;
        $this->tecnico       = $tecnico;
        $this->docente       = $docente;
    }

    // --- GETTERS ---
    public function getCedula(): string { 
        return $this->cedula; 
    }

    public function getClaveHash(): string { 
        return $this->claveHash; 
    }

    // Devuelve si el usuario está activo/habilitado en el sistema
    public function estaActivo(): bool { 
        return $this->sesionActiva; 
    }

    // Alias compatible con la plantilla del docente
    public function tieneSesionActiva(): bool { 
        return $this->sesionActiva; 
    }

    // Roles del SGRSI
    public function esAdministrador(): bool { 
        return $this->administrador; 
    }

    public function esTecnico(): bool { 
        return $this->tecnico; 
    }

    public function esDocente(): bool { 
        return $this->docente; 
    }
}