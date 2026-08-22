<?php
require_once __DIR__ . "/usuarios/CargarUsuarios.php";

/**
 * @brief Gestiona la autenticación de los usuarios.
 *
 * Utiliza AccesoDatosUsuario para obtener los datos del usuario
 * y verifica que se encuentre activo y que la contraseña ingresada
 * coincida con el hash almacenado en la base de datos.
 */
class Login
{
    /**
     * @brief Acceso a los datos de los usuarios.
     */
    private AccesoDatosUsuario $accesoDatosUsuario;

    /**
     * @brief Construye un objeto Login.
     *
     * @param AccesoDatosUsuario $accesoDatosUsuario
     *        Objeto utilizado para consultar los usuarios en la base de datos.
     */
    public function __construct(AccesoDatosUsuario $accesoDatosUsuario)
    {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }

    /**
     * @brief Autentica a un usuario mediante su cédula y contraseña.
     *
     * Primero busca al usuario en la base de datos. Si existe,
     * comprueba que se encuentre activo y posteriormente verifica
     * la contraseña utilizando el hash almacenado.
     *
     * @param string $cedula Cédula del usuario.
     * @param string $clave Contraseña ingresada por el usuario.
     *
     * @return Usuario|null
     *         Devuelve el objeto Usuario si la autenticación es correcta.
     *         Devuelve null si el usuario no existe, está inactivo
     *         o la contraseña es incorrecta.
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