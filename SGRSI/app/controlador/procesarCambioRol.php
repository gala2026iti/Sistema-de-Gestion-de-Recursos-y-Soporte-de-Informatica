<?php

require_once __DIR__ . "/../../config/config.php";

session_start();

/**
 * @file procesarLogin.php
 *
 * @brief Procesa el inicio de sesión del usuario.
 *
 * Recibe las credenciales, autentica al usuario, crea la sesión y lo redirige
 * según los roles que posee.
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header(
        "Location: ../vista/index.php?error="
            . urlencode($mensaje)
    );
    exit;
}

$rol = htmlspecialchars(trim($_POST["rol"] ?? ""));
$csrfToken = htmlspecialchars(trim($_POST["csrfToken"] ?? ""));

if (
    !isset($_SESSION["csrfToken"]) ||
    !is_string($csrfToken) ||
    !hash_equals($_SESSION["csrfToken"], $csrfToken)
) {
    $mensaje = "Solicitud rechazada: token de seguridad inválido.";

    $urlAnterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

    header("Location: " . $urlAnterior . "?error=" . urlencode($mensaje));
    exit;
}

if ($rol !== "docente" && $rol !== "tecnico" && $rol !== "administrador") {
    $mensaje = "Rol inválido.";

    $urlAnterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

    header("Location: " . $urlAnterior . "?error=" . urlencode($mensaje));
    exit;
}

$_SESSION["rolActual"] = $rol;

switch ($rol):
    case "administrador":
        if ($_SESSION["administrador"]) {
            header("Location: ../../public/paginaWeb/homeAdmin.php");
        }
        break;
    case "tecnico":
        if ($_SESSION["tecnico"]) {
            header("Location: ../../public/paginaWeb/homeTecnico.php");
        }
        break;
    case "docente":
        if ($_SESSION["docente"]) {
            header("Location: ../../public/paginaWeb/homeDocente.php");
        }
        break;
endswitch;

exit();
