<?php
require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosUsuario.php";
require_once RUTA_MODELO . "/Usuario.php";
require_once RUTA_MODELO . "/Login.php";

/**
 * @brief Procesa el inicio de sesión del usuario.
 *
 * Recibe las credenciales enviadas mediante POST, establece la
 * conexión con la base de datos y utiliza las clases del modelo
 * para autenticar al usuario.
 *
 * Si la autenticación es correcta, inicia una sesión PHP y almacena
 * la cédula y los roles del usuario. Finalmente, redirige al usuario
 * a la página correspondiente según los roles que posee.
 *
 * Si ocurre algún error, redirige nuevamente al inicio de sesión
 * mostrando el mensaje correspondiente.
 */


/*
 * Solamente se permite el envío del formulario mediante POST.
 */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $mensaje = "Acceso Denegado: Petición incorrecta";

    header(
        "Location: ../vista/index.php?error="
        . urlencode($mensaje)
    );

    exit;
}


/*
 * Recuperamos las credenciales enviadas por el formulario.
 */
$cedula = trim($_POST["cedula"] ?? "");
$clave  = $_POST["clave"] ?? "";


/*
 * Establecemos la conexión con la base de datos.
 */
$conectorPDO = new ConectorPDO(
    "127.0.0.1:3306",
    "root",
    "",
    "sgrsi"
);

$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {

    $mensaje = "Acceso Denegado: Problemas con la conexión.";

    header(
        "Location: ../vista/index.php?error="
        . urlencode($mensaje)
    );

    exit;
}


/*
 * Creamos los objetos necesarios para realizar
 * la autenticación.
 */
$accesoDatosUsuario = new AccesoDatosUsuario($conexion);

$login = new Login($accesoDatosUsuario);

$usuario = $login->autenticar($cedula, $clave);


/*
 * La conexión ya no es necesaria después
 * de completar la autenticación.
 */
$conectorPDO->desconectar();


/*
 * Si la autenticación falla, no se crea una sesión.
 */
if ($usuario === null) {

    $mensaje = "Acceso Denegado: La cédula o la contraseña son incorrectas o la sesión ya está activa.";

    header(
        "Location: ../vista/index.php?error="
        . urlencode($mensaje)
    );

    exit;
}


/*
 * Iniciamos la sesión PHP y regeneramos su identificador
 * para evitar reutilizar el identificador anterior.
 */
session_start();

session_regenerate_id(true);


/*
 * Guardamos en la sesión los datos necesarios
 * para identificar al usuario y controlar sus permisos.
 */
$_SESSION["cedula"]        = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"]       = $usuario->esTecnico();
$_SESSION["docente"]       = $usuario->esDocente();


/*
 * Redirigimos al usuario según los roles que posee.
 *
 * Como un usuario puede tener más de un rol, se comprueba
 * cada uno de forma independiente. En este controlador se
 * utiliza el primer rol encontrado según este orden.
 */
if ($_SESSION["administrador"]) {

    header("Location: ../../public/paginaWeb/homeAdmin.php");

} elseif ($_SESSION["tecnico"]) {

    header("Location: ../../public/paginaWeb/homeTecnico.php");

} elseif ($_SESSION["docente"]) {

    header("Location: ../../public/paginaWeb/homeDocente.php");
}

exit;