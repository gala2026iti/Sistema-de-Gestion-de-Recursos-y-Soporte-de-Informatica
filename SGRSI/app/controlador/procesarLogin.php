<?php

require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/ConsultaUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";


ini_set('display_errors', 1);
error_reporting(E_ALL);

//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/paginaWeb/index.php");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$clave = $_POST["clave"] ?? "";

$consultaUsuario = new ConsultaUsuario();
$login = new Login($consultaUsuario);

$usuario = $login->autenticar($cedula, $clave);

//Si las credenciales no coinciden, muestra el error y detiene el proceso
if ($usuario === null) {
    header("Location: ../../public/paginaWeb/index.php");
    exit("La cédula o la contraseña son incorrectas.");
}

session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["nombre"] = $usuario->getNombre();
$_SESSION["correo"] = $usuario->getCorreo();
$_SESSION["admin"] = $usuario->getAdmin();
$_SESSION["tecnico"] = $usuario->getTecnico();
$_SESSION["docente"] = $usuario->getDocente();

//Comprueba que rol tiene asignado
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) { 
    header("Location: ../../public/paginaWeb/homeAdmin.php");
    exit;
}


if (isset($_SESSION["docente"]) && $_SESSION["docente"] === true) { 
    header("Location: ../../public/paginaWeb/homeDocente.php");
    exit;
}

if (isset($_SESSION["tecnico"]) && $_SESSION["tecnico"] === true) { 
    header("Location: ../../public/paginaWeb/homeTecnico.php");
    exit;
}

exit("Rol de usuario no reconocido.");

?>