<?php

/**
 * @file procesarEstadoTicket.php
 *
 * @brief Procesa cambios de estado o gravedad de tickets.
 *
 * Valida la solicitud recibida y solicita al modelo la actualización indicada.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/tickets/EstadoDatosTicket.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso denegado: debe iniciar sesión.";

    header(
        "Location: ../../../public/paginaWeb/index.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!($_SESSION["tecnico"] ?? false)) {
    $mensaje = "Acceso denegado: no tiene permisos para realizar esta operación.";

    header(
        "Location: ../../../public/paginaWeb/index.php?error="
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
    $mensaje = "Solicitud rechazada: token inválido.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$id = trim($_POST["idTicket"] ?? "");
$estado = strtolower(trim($_POST["estado"] ?? ""));
$gravedad = strtolower(trim($_POST["gravedad"] ?? ""));

if ($id === "" || ( $estado === "" && $gravedad === "" )) {
    $mensaje = "No se recibieron los datos necesarios (estado, gravedad) o los valores ingresados son inválidos.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/homeTecnico.php?error="
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
        "Location: ../../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$estadoDatosTicket = new EstadoDatosTicket($conexion);

if($estado) {
$resultado = $estadoDatosTicket->cambiarEstadoTicket(
    $id,
    $estado
    );
}

if($gravedad) {
    $resultado = $estadoDatosTicket->cambiarGravedadTicket(
        $id,
        $gravedad
    );
}

$conectorPDO->desconectar();



if (!$resultado) {
    $mensaje = "No se pudo modificar el estado del ticket.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/homeTecnico.php?error="
        . urlencode($mensaje)
    );
    exit();
} else {
    $mensaje = "Ticket modificado correctamente.";
}

header(
    "Location: ../../../public/paginaWeb/tecnico/homeTecnico.php?resultado="
    . urlencode($mensaje)
);

exit();