<?php
// Para que se pueda entender el trabajo, dejo comentarios sobre todo lo que pueda, y ta, eso, veremos q sale
// La idea es tipo tener una guia para despues poder extender todo esto a lo demas

require_once __DIR__ . "/../modelo/Usuario.php";
require_once __DIR__ . "/../modelo/ConsultaUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";

// El require once busca un archivo, para incluirlo en la ejecucion del código, si no esta, tira error fatal y tipo implosiona. el "once" es para que asegurarse que el archivo se incluya sola vez y que no redeclare variables
// El __DIR __ es ubicarse donde uno esta parado, eso, el punto, y el resto de la URL forman un texto especifico, unido por el punto ese


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit("Error al obtener datos.");
}

// El if verifica si el método no es igual a POST, y como POST es el unico que precisamos, si no esta, no se avanza
// El objetivo del request method es que, tipooo, si o si se ingrese por el formulario, sino nada, tambien evita que salteen el inicio de sesion cambiando la URL
// El header es para redireccionar al usuario a login
// El exit es para finalizar la ejecucion del PHP, esto para que no avance y no tire errores con las demas cosas que hay abajo

$cedula = trim($_POST["cedula"]);
$clave = trim($_POST["clave"]);

// Trim es para recortar los espacios al inicio y al fin, El post es para obtener el valor del post, y la clave es el name que se le asigno a lo que queremos recuperar

$consultaUsuario = new ConsultaUsuario();
$login = new Login($consultaUsuario);

// Ya se consiguió el login, y aca se crea la consultaUsuario, donde se obtienen los valores ya creados manualmente

$usuario = $login->autenticar($cedula, $clave);

// Aca se crea una variable en base a un metodo de otra variable, en este caso, se usa login para autenticar si el user es valido o nada q ver

if ($usuario === null) {
    exit("La cedula o la clave son incorrectas.");
}

// Tampoco es que deba explicar que hace esta cosa


session_start(); // Esta cosa inicia la sesion o verifica si ya esta iniciada, esta es temporal
session_regenerate_id(true); // Cambia el codigo de la sesion actual por otro nuevo y aleatorio, no ando seguro contra que ataques protege, pero es una capa de seguridad

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["nombre"] = $usuario->getNombre();
$_SESSION["rol"] = $usuario->getRol();
$_SESSION["correo"] = $usuario->getCorreo();

// Se asignan los datos del que inicio sesion a la misma sesion

switch($_SESSION["rol"]){ // Este switch verifica cual es el rol, y depenfiendo del mismo, redirige al usuario
case "administrador":
    header("Location: homeAdmin.php");
break;
case "docente":
    header("Location: homeDocente.php");
break;
case "director":
    header("Location: homeDirector.php");
break;
case "tecnico":
header("Location: homeAdmin.php");
break;

}
exit; // y ta, el exit final, ya que no hay nada mas que hacer, no estoy seguro si va o no, pero como sé que deja de ejecutar el php lo dejo.

?>