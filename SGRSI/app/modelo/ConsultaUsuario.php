<?php

class ConsultaUsuario {

private array $datos = [];

public function __construct(){
    $this->datos = [
            [
            "cedula" => "11111111",
            "clave" => password_hash("12121212", PASSWORD_DEFAULT),
            "activo" => true,
            "rol" => "docente",
            "correo" => "correo@correo.com",
            "nombre" => "Nombre Nombretastico"

        ],
            [
            "cedula" => "22222222",
            "clave" => password_hash("23232323", PASSWORD_DEFAULT),
            "activo" => true,
            "rol" => "administrador",
            "correo" => "correo2@correo.com",
            "nombre" => "Fulano Fulanez"

        ],
            [
            "cedula" => "33333333",
            "clave" => password_hash("34343434", PASSWORD_DEFAULT),
            "activo" => true,
            "rol" => "tecnico",
            "correo" => "correo3@correo.com",
            "nombre" => "Elvis Tek"

        ],
            [
            "cedula" => "44444444",
            "clave" => password_hash("45454545", PASSWORD_DEFAULT),
            "activo" => true,
            "rol" => "director",
            "correo" => "correo4@correo.com",
            "nombre" => "Lucas Ferreira"

        ],

        ];
}

    public function buscarUsuario(string $cedula): ?Usuario {



        foreach ($this->datos as $usuario) {
        if ($cedula === $usuario["cedula"]) {
            return new Usuario (
            $usuario["cedula"],
            $usuario["clave"],
            $usuario["activo"],
            $usuario["rol"],
            $usuario["correo"],
            $usuario["nombre"]

        );
        }
        }

        return null;
    }
}

?>