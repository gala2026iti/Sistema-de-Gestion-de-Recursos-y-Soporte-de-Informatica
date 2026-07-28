<?php
require_once __DIR__ . "/usuarios/CargarUsuarios.php";

/**
 * @brief Gestiona la autenticación de los usuarios.
 *
 * Consulta los datos del usuario y verifica su estado y contraseña.
 */
class Login
{
    /**
     * @brief Acceso a los datos de los usuarios.
     */
    private CargarUsuarios $accesoDatosUsuario;

    /**
     * @brief Construye un objeto Login.
     *
     * @param CargarUsuarios $accesoDatosUsuario Objeto utilizado para consultar usuarios.
     */
    public function __construct(CargarUsuarios $accesoDatosUsuario)
    {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }

    /**
     * @brief Autentica a un usuario mediante su cédula y contraseña.
     *
     * @param string $cedula Cédula del usuario.
     * @param string $clave Contraseña ingresada.
     *
     * @return Usuario|null Usuario autenticado si las credenciales son válidas;
     *              null si no existe, está inactivo o la contraseña es incorrecta.
     */
    public function autenticar(string $cedula, string $clave): ?Usuario
    {
        $usuario = $this->accesoDatosUsuario->buscarUsuario($cedula);

        if ($usuario === null) {
            return null;
        }

        if (!$usuario->estaActivo()) {
            return null;
        }

        if (!password_verify($clave, $usuario->getClaveHash())) {
            return null;
        }

        return $usuario;
    }
}