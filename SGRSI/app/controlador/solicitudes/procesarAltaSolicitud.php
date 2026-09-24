<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * @file procesarAltaSolicitud.php
 *
 * @brief Procesa el registro de nuevas solicitudes.
 *
 * Valida los datos recibidos y solicita al modelo el registro de la solicitud.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/solicitudes/AltaSolicitud.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/docente/pagsolicitudes.php?error="
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

if (!($_SESSION["docente"] ?? false)) {
    $mensaje = "Acceso denegado: no tiene permisos para realizar esta operación.";

    header(
        "Location: ../index.php?error="
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
        "Location: pagsolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

 $asunto = htmlspecialchars(trim($_POST["asunto"] ?? ""));
 $descripcion = htmlspecialchars(trim($_POST["descripcion"] ?? ""));
 $fechaLimite = htmlspecialchars(trim($_POST["fecha"] ?? ""));
 $ciDocente = htmlspecialchars(trim($_SESSION["cedula"] ?? ""));

 
$fecha = DateTime::createFromFormat('Y-m-d\TH:i', $fechaLimite);

$fechaFin = $fecha->format('Y/m/d');
$horaFin = $fecha->format('H:i');

$ahora = new DateTime();

if ($fecha <= $ahora) {
    $mensaje = "La fecha y hora deben ser posteriores al momento actual.";
    header("Location: ../../public/paginaWeb/docente/pagsolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if (
    $asunto === "" ||
    $descripcion === "" ||
    $fechaFin === "" ||
    $horaFin === ""
) {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (strlen($asunto) < 10 || strlen($asunto) > 30) {
    $mensaje = "El asunto debe tener entre 10 y 30 caracteres.";

    header(
        "Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (strlen($descripcion) < 10 || strlen($descripcion) > 200) {
    $mensaje = "La descripción debe tener entre 10 y 200 caracteres.";

    header(
        "Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$fechaIngresada = DateTime::createFromFormat('Y/m/d H:i', $fechaFin . ' ' . $horaFin);

if (!$fechaIngresada) {
    $mensaje = "La fecha u hora ingresadas no son válidas.";
    header("Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

$ahora = new DateTime();

if ($fechaIngresada <= $ahora) {
    $mensaje = "La fecha y hora deben ser posteriores al momento actual.";
    header("Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if(!strlen($ciDocente) === 8 || !is_numeric($ciDocente)) {
    $mensaje = "La cédula del docente debe tener 8 dígitos.";
    header("Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error=" . urlencode($mensaje));
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
        "Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$AltaSolicitud = new AltaSolicitud($conexion);

$resultado = $AltaSolicitud->registrarSolicitud(
    $asunto,
    $descripcion,
    $fechaFin,
    $horaFin,
    $ciDocente
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar la solicitud.";


    header(
        "Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Solicitud registrada correctamente.";

header(
    "Location: ../../../public/paginaWeb/docente/pagsolicitudes.php?resultado="
    . urlencode($mensaje)
);

exit();