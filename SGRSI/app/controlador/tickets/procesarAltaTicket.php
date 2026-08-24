<?php

/**
 * @file procesarAltaTicket.php
 *
 * @brief Procesa el registro de nuevos tickets.
 *
 * Valida la solicitud recibida, comprueba la sesión y solicita al modelo
 * el registro del ticket.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/tickets/AltaTicket.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
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

if (!($_SESSION["tecnico"] ?? false)) {
    $mensaje = "Acceso denegado: no tiene permisos para realizar esta operación.";

    header(
        "Location: ../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

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

$idTicket = trim($_POST["idTicket"] ?? "");
$tipo = trim($_POST["tipo"] ?? "");
$asunto = trim($_POST["asunto"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$gravedad = trim($_POST["gravedad"] ?? "");
$estado = trim($_POST["estado"] ?? "");
$fechaCreacion = trim($_POST["fechaCreacion"] ?? "");
$horaCreacion = trim($_POST["horaCreacion"] ?? "");
$justificacion = trim($_POST["justificacion"] ?? "");

if (!is_numeric($idTicket)) {
    $mensaje = "El ID del ticket no es válido, debe ser un número entero.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($tipo !== "hardware" && $tipo !== "software" && $tipo !== "red") {
    $mensaje = 'El tipo de ticket no es válido: Solo se permiten "hardware", "software" o "red".';

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(strlen($asunto) < 10 || strlen($asunto) > 50) {
    $mensaje = "El asunto debe contener entre 10 y 50 caracteres.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(strlen($descripcion) < 10 || strlen($descripcion) > 250) {
    $mensaje = "La descripción debe contener entre 10 y 250 caracteres.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
    $mensaje = 'La gravedad del ticket no es válida: Solo se permiten "ligera", "media" o "grave".';

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($estado !== "pendiente") {
    $mensaje = "Los tickets no pueden darse de alta con otro estado que no sea 'pendiente'.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($justificacion !== NULL) {
    $mensaje = "Los tickets no pueden darse de alta con justificación.";

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
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$AltaTicket = new AltaTicket($conexion);

$resultado = $AltaTicket->registrarTicket(
    $idTicket,
    $tipo,
    $asunto,
    $descripcion,
    $gravedad,
    $estado,
    $fechaCreacion,
    $horaCreacion
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el ticket.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el ticket.";

    header(
        "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Ticket registrado correctamente.";

header(
    "Location: ../../public/paginaWeb/tecnico/homeTecnico.php?resultado="
    . urlencode($mensaje)
);

exit();