<?php

/**
 * @brief Cierra la sesión del usuario.
 *
 * Inicia la sesión actual, elimina todos los datos almacenados
 * en $_SESSION y destruye la sesión PHP.
 *
 * También evita que el navegador almacene en caché páginas
 * protegidas y finalmente redirige al usuario al inicio de sesión.
 */

session_start();


/*
 * Eliminamos todos los datos almacenados
 * actualmente en la sesión.
 */
$_SESSION = [];


/*
 * Destruimos la sesión PHP.
 */
session_destroy();


/*
 * Evitamos que el navegador utilice páginas protegidas
 * almacenadas previamente en caché.
 */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


/*
 * Volvemos al inicio de sesión.
 */
header("Location: index.php");

exit();