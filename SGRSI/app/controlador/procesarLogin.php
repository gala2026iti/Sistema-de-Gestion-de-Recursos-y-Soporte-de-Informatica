<?php
// app/controlador/procesarLogin.php

require_once __DIR__ . "/../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDatosUsuario.php";
require_once RUTA_MODELO . "/Usuario.php";
require_once RUTA_MODELO . "/Login.php";

// Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";
    header("Location: ../vista/index.php?error=" . urlencode($mensaje));
    exit;
}

// Recupera las credenciales del formulario
$cedula = trim($_POST["cedula"] ?? "");
$clave  = $_POST["clave"] ?? "";

// Conexión mediante la clase ConectorPDO
$conectorPDO = new ConectorPDO("127.0.0.1:3306", "root", "", "sgrsi");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "Acceso Denegado: Problemas con la conexión.";
    header("Location: ../vista/index.php?error=" . urlencode($mensaje));
    exit;
}

// Proceso de Autenticación
$accesoDatosUsuario = new AccesoDatosUsuario($conexion);
$login = new Login($accesoDatosUsuario);
$usuario = $login->autenticar($cedula, $clave);

// Desconexión limpia de PDO
$conectorPDO->desconectar();

// Si las credenciales son incorrectas o no existe el usuario
if ($usuario === null) {
    $mensaje = "Acceso Denegado: La cédula o la contraseña son incorrectas o la sesión ya está activa.";
    header("Location: ../vista/index.php?error=" . urlencode($mensaje));
    exit;
}

// Iniciar Sesión en PHP
session_start();
session_regenerate_id(true);

$_SESSION["cedula"]        = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"]       = $usuario->esTecnico();
$_SESSION["docente"]       = $usuario->esDocente();

// Redireccionar según el rol activado
if ($_SESSION["administrador"]) {
    header("Location: ../vista/homeAdmin.php");
} elseif ($_SESSION["tecnico"]) {
    header("Location: ../vista/homeTecnico.php");
} elseif ($_SESSION["docente"]) {
    header("Location: ../vista/homeDocente.php");
}

exit;