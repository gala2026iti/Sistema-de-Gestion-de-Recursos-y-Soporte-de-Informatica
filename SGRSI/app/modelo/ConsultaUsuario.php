<?php

/**
 * Clase que simula una recuperación de credenciales correspondientes a la base de datos.
 */
class ConsultaUsuario {
    /**
     * Simula la recuperación de un usuario desde una base de datos.
     *
     * Más adelante, el contenido de esta función será reemplazado
     * por una consulta mediante PDO.
     */
    public function buscarUsuario(string $cedula): ?Usuario {
        $datos_debug_1 = [
            "cedula" => "12345678",
            "nombre" => "Juan de los palotes",
            "correo" => "juan@ejemplo.com",
            "claveHash" => password_hash("adminITI", PASSWORD_DEFAULT),
            "rol" => "admin",
            "activo" => true
        ];

        $datos_debug_2 = [
            "cedula" => "11111111",
            "nombre" => "Juan de los palotes",
            "correo" => "juan@ejemplo.com",
            "claveHash" => password_hash("direccionITI", PASSWORD_DEFAULT),
            "rol" => "direccion",
            "activo" => true
        ];

        $datos_debug_3 = [
            "cedula" => "22222222",
            "nombre" => "Juan de los palotes",
            "correo" => "juan@ejemplo.com",
            "claveHash" => password_hash("tecnicoITI", PASSWORD_DEFAULT),
            "rol" => "tecnico",
            "activo" => true
        ];
    
        $datos_debug_4= [
            "cedula" => "33333333",
            "nombre" => "Juan de los palotes",
            "correo" => "juan@ejemplo.com",
            "claveHash" => password_hash("docenteITI", PASSWORD_DEFAULT),
            "rol" => "docente",
            "activo" => true
        ];

        if ($cedula === $datos_debug_1["cedula"]) {
            return new Usuario (
                $datos_debug_1["cedula"],
                $datos_debug_1["nombre"],
                $datos_debug_1["correo"],
                $datos_debug_1["claveHash"],
                $datos_debug_1["rol"],
                $datos_debug_1["activo"]
            );
        }

                if ($cedula === $datos_debug_2["cedula"]) {
            return new Usuario (
                $datos_debug_2["cedula"],
                $datos_debug_2["nombre"],
                $datos_debug_2["correo"],
                $datos_debug_2["claveHash"],
                $datos_debug_2["rol"],
                $datos_debug_2["activo"]
            );
        }

                if ($cedula === $datos_debug_3["cedula"]) {
            return new Usuario (
                $datos_debug_3["cedula"],
                $datos_debug_3["nombre"],
                $datos_debug_3["correo"],
                $datos_debug_3["claveHash"],
                $datos_debug_3["rol"],
                $datos_debug_3["activo"]
            );
        }

                if ($cedula === $datos_debug_4["cedula"]) {
            return new Usuario (
                $datos_debug_4["cedula"],
                $datos_debug_4["nombre"],
                $datos_debug_4["correo"],
                $datos_debug_4["claveHash"],
                $datos_debug_4["rol"],
                $datos_debug_4["activo"]
            );
        }


    }
}

?>