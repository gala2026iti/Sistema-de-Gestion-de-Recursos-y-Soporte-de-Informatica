<?php

/**
 * @brief Controla el acceso al panel docente.
 *
 * Inicia la sesión PHP, evita que el navegador almacene en caché
 * la página y verifica que exista una sesión iniciada y que el
 * usuario posea el rol de docente.
 *
 * Si el usuario no cumple alguna de las condiciones, es redirigido
 * al inicio de sesión. Si las cumple, se carga la vista del panel
 * docente.
 */

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


/*
 * Verificamos que exista una sesión iniciada.
 */
if (!isset($_SESSION["cedula"])) {

    header("Location: index.php?error=Acceso Denegado: Sesión no iniciada");

    exit();
}


/*
 * Verificamos que el usuario posea el rol de docente.
 */
if (!isset($_SESSION["docente"]) || $_SESSION["docente"] !== true) {

    header("Location: index.php?error=Acceso Denegado: Acceso a la zona correspondiente no autorizado");

    exit();
}


/*
 * Si las comprobaciones son correctas,
 * cargamos la vista del panel.
 */
require_once __DIR__ . "/../../app/vista/homeDocente.php";