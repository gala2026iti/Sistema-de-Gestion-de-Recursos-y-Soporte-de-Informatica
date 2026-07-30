<?php

/**
 * Clase que simula una recuperación de credenciales correspondientes a la base de datos.
 */
class ConsultaUsuario
{
    /**
     * Simula la recuperación de un usuario desde una base de datos.
     *
     * Más adelante, el contenido de esta función será reemplazado
     * por una consulta mediante PDO.
     */
    public function buscarUsuario(string $cedula): ?Usuario {
        $datos_globales = [
            [
                "cedula" => "12345678",
                "nombre" => "Juan de los palotes",
                "correo" => "juan@ejemplo.com",
                "claveHash" => password_hash("adminITI", PASSWORD_DEFAULT),
                "admin" => true,
                "tecnico" => false,
                "docente" => false,
                "activo" => true
            ],

            [
                "cedula" => "22222222",
                "nombre" => "Juan de los palotes",
                "correo" => "juan@ejemplo.com",
                "claveHash" => password_hash("tecnicoITI", PASSWORD_DEFAULT),
                "admin" => false,
                "tecnico" => true,
                "docente" => false,
                "activo" => true
            ],

            [
                "cedula" => "33333333",
                "nombre" => "Juan de los palotes",
                "correo" => "juan@ejemplo.com",
                "claveHash" => password_hash("docenteITI", PASSWORD_DEFAULT),
                "admin" => false,
                "tecnico" => false,
                "docente" => true,
                "activo" => true
            ],

            [
                "cedula" => "44444444",
                "nombre" => "Juan de los palotes",
                "correo" => "juan@ejemplo.com",
                "claveHash" => password_hash("pruebaITI", PASSWORD_DEFAULT),
                "admin" => false,
                "tecnico" => true,
                "docente" => true,
                "activo" => true
            ]
        ];

        foreach ($datos_globales as $datos_debug) {
            if ($cedula === $datos_debug["cedula"]) {
                return new Usuario(
                    $datos_debug["cedula"],
                    $datos_debug["nombre"],
                    $datos_debug["correo"],
                    $datos_debug["claveHash"],
                    $datos_debug["admin"],
                    $datos_debug["tecnico"],
                    $datos_debug["docente"],
                    $datos_debug["activo"]
                );
            }
        }
    }
}


?>