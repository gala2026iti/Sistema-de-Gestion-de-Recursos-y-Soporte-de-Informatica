<?php

/**
 * @file procesarModificarTicket.php
 *
 * @brief Procesa la asignación o desasignación de tickets.
 *
 * Valida la solicitud y utiliza el modelo para modificar la relación del técnico con el ticket.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/tickets/ModificarDatosTicket.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/tecnico/gestionTickets.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso denegado: debe iniciar sesión.";

    header(
        "Location: ../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!($_SESSION["tecnico"] && $_SESSION["rolActual"] === "tecnico")) {
    $mensaje = "Acceso denegado: No tiene permisos para realizar esta operación.";

    header(
        "Location: ../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

/*
 * Comprobamos que la solicitud incluya
 * el token CSRF de la sesión.
 */
$csrfToken = $_POST["csrfToken"] ?? "";

if (
    !isset($_SESSION["csrfToken"]) ||
    !is_string($csrfToken) ||
    !hash_equals($_SESSION["csrfToken"], $csrfToken)
) {
    $mensaje = "Solicitud rechazada: token de seguridad inválido.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$idTicket = htmlspecialchars(trim($_POST["idReporte"] ?? ""));
$csrfToken = htmlspecialchars(trim($_POST["csrfToken"] ?? ""));
$ciTecnico = htmlspecialchars(trim($_SESSION["cedula"] ?? ""));
$idEquipo = htmlspecialchars(trim($_POST["idEquipo"] ?? ""));
$idReporte = htmlspecialchars(trim($_POST["idReporte"] ?? ""));
$accion = htmlspecialchars(trim($_POST["accion"] ?? ""));


if (!is_numeric($idTicket)) {
    $mensaje = "El ID del ticket debe ser un número entero.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(!is_numeric($ciTecnico)) {
    $mensaje = "La cédula del técnico debe ser un número entero.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(!strlen($ciTecnico) === 8) {
    $mensaje = "La cédula del técnico debe tener 8 dígitos.";
    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($accion !== "asignarse" && $accion !== "desasignarse") {
    $mensaje = "Acción no válida, debe ser 'asignarse' o 'desasignarse'.";
    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$conectorPDO = new ConectorPDO(
    $_ENV['DB_HOST'] . ":" . 
    $_ENV['DB_PUERTO'], 
    $_ENV['DB_USUARIO'], 
    $_ENV['DB_CLAVE'], 
    $_ENV['DB_NOMBRE']
);

$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$modificarDatosTicket = new ModificarDatosTicket($conexion);

if($accion === "asignarse") {
    $resultado = $modificarDatosTicket->asignarme(
        $ciTecnico,
        $idReporte
            );
} else {
    $resultado = $modificarDatosTicket->desasignarme(
        $idReporte
            );
}

$conectorPDO->desconectar();

if (!$resultado) {
    if($accion === "asignarse") {
        $mensaje = "No se le pudo asignar el ticket.";
    } else {
        $mensaje = "No se le pudo desasignar el ticket.";
    }

    header(
        "Location: ../../../public/paginaWeb/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($accion === "asignarse") {
    $mensaje = "Ticket asignado correctamente.";
} else {
    $mensaje = "Ticket desasignado correctamente.";
}

header(
    "Location: ../../../public/paginaWeb/homeTecnico.php?resultado="
    . urlencode($mensaje)
);

exit();