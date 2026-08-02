<?php
require_once __DIR__ . "/Usuario.php";

class Login {
    private string $cedula;
    private string $clave;

    public function __construct(string $cedula, string $clave) {
        $this->cedula = trim($cedula);
        $this->clave  = $clave;
    }

    public function getCedula(): string {
        return $this->cedula;
    }

    public function getClave(): string {
        return $this->clave;
    }

    public function esClaveValida(Usuario $usuario): bool {
        return password_verify($this->clave, $usuario->getClaveHash());
    }
}