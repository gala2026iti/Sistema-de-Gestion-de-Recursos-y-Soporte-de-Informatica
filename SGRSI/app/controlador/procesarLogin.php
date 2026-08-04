<?php

require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";
require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/Login.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/paginaWeb/index.php");
    exit();
}

$cedulaInput = $_POST["cedula"] ?? "";
$claveInput  = $_POST["clave"] ?? "";

$login = new Login($cedulaInput, $claveInput);
$accesoDatos = new AccesoDatosUsuario();
$usuario = $accesoDatos->buscarUsuarioPorCedula($login->getCedula());

// 1. Validar Credenciales
if ($usuario === null || !$login->esClaveValida($usuario)) {
    header("Location: ../../public/paginaWeb/index.php?error=credenciales");
    exit();
}

// 2. Validar Estado
if (!$usuario->estaActivo()) {
    header("Location: ../../public/paginaWeb/index.php?error=inactivo");
    exit();
}

// 3. Validar Roles
if (!$usuario->getAdmin() && !$usuario->getTecnico() && !$usuario->getDocente()) {
    header("Location: ../../public/paginaWeb/index.php?error=sin_roles");
    exit();
}

// Iniciar sesión
session_start();
session_regenerate_id(true);

$_SESSION["cedula"]  = $usuario->getCedula();
$_SESSION["nombre"]  = $usuario->getNombre();
$_SESSION["admin"]   = $usuario->getAdmin();
$_SESSION["tecnico"] = $usuario->getTecnico();
$_SESSION["docente"] = $usuario->getDocente();

// Redirección según Roles
if ($_SESSION["admin"] && $_SESSION["tecnico"]) {
    header("Location: ../../public/paginaWeb/homeAdmin.php");
    exit();
} elseif ($_SESSION["admin"]) {
    header("Location: ../../public/paginaWeb/homeAdmin.php");
    exit();
} elseif ($_SESSION["tecnico"]) {
    header("Location: ../../public/paginaWeb/homeTecnico.php");
    exit();
} elseif ($_SESSION["docente"]) {
    header("Location: ../../public/paginaWeb/homeDocente.php");
    exit();
}