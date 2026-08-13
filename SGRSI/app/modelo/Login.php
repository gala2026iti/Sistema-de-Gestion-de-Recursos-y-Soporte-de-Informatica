<?php
// app/modelo/Login.php

require_once __DIR__ . "/AccesoDatosUsuario.php";

class Login {
    private AccesoDatosUsuario $accesoDatosUsuario;

    public function __construct(AccesoDatosUsuario $accesoDatosUsuario) {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }

    public function autenticar(string $cedula, string $clave): ?Usuario {
        // 1. Buscar el usuario en la BD
        $usuario = $this->accesoDatosUsuario->buscarUsuario($cedula);

        if ($usuario === null) {
            return null;
        }

        // 2. Si el usuario está dado de baja (activo == false), rechazar
        if (!$usuario->estaActivo()) {
            return null;
        }

        // 3. Verificar contraseña con password_verify
        if (!password_verify($clave, $usuario->getClaveHash())) {
            return null;
        }

        return $usuario;
    }
}