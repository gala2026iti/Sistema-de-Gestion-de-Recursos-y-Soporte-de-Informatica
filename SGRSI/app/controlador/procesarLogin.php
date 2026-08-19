<?php

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosUsuario.php";
require_once RUTA_MODELO . "/Login.php";

/**
 * @file procesarLogin.php
 *
 * @brief Procesa el inicio de sesión del usuario.
 *
 * Recibe las credenciales mediante POST, autentica al usuario,
 * crea la sesión y lo redirige según los roles que posee.
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header(
        "Location: ../vista/index.php?error="
        . urlencode($mensaje)
    );
    exit;
}

$cedula = trim($_POST["cedula"] ?? "");
$clave = $_POST["clave"] ?? "";

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

$accesoDatosUsuario = new AccesoDatosUsuario($conexion);
$login = new Login($accesoDatosUsuario);
$usuario = $login->autenticar($cedula, $clave);

$conectorPDO->desconectar();

if ($usuario === null) {
    $mensaje = "Acceso Denegado: La cédula o la contraseña son incorrectas, o el usuario está inactivo.";

    header(
        "Location: ../vista/index.php?error="
        . urlencode($mensaje)
    );
    exit;
}

/*
 * Iniciamos una nueva sesión y regeneramos su identificador
 * antes de almacenar los datos del usuario.
 */
session_start();
session_regenerate_id(true);

/*
 * Generamos el token utilizado para proteger
 * las operaciones sensibles contra solicitudes CSRF.
 */
$_SESSION["csrfToken"] = bin2hex(random_bytes(32));

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"] = $usuario->esTecnico();
$_SESSION["docente"] = $usuario->esDocente();

/*
 * Si posee varios roles, se utiliza el primero
 * según el siguiente orden de prioridad.
 */
if ($_SESSION["administrador"]) {
    header("Location: ../../public/paginaWeb/homeAdmin.php");
} elseif ($_SESSION["tecnico"]) {
    header("Location: ../../public/paginaWeb/homeTecnico.php");
} elseif ($_SESSION["docente"]) {
    header("Location: ../../public/paginaWeb/homeDocente.php");
}

exit;