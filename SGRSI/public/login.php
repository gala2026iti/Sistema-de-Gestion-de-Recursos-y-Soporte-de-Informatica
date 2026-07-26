<?php

session_start();
if (isset($_SESSION["cedula"]) && isset($_SESSION["rol"])) {
switch($_SESSION["rol"]){
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
}

require_once __DIR__ . "/../app/vista/login.php";
?>