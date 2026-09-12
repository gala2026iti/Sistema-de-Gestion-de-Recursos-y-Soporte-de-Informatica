<?php
/**
 * @file procesarAltaSolicitud.php
 *
 * @brief Procesa el registro de nuevas solicitudes.
 *
 * Valida los datos recibidos y solicita al modelo el registro de la solicitud.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/prestamos/AltaPrestamo.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        /* TOFIX : CORREGIR REDIRECCIONES DE ERRORES*/
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
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
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
}

 $ciTecnico = htmlspecialchars(trim($_SESSION["cedula"] ?? ""));
 $nombrePrestado = htmlspecialchars(trim($_POST["nombrePrestado"] ?? ""));
 $ciPrestado = htmlspecialchars(trim($_POST["ciPrestado"] ?? ""));
 $fechaDevolucion = htmlspecialchars(trim($_POST["final"] ?? ""));
 $idEquipo = htmlspecialchars(trim($_POST["idEquipo"] ?? ""));

if (
    $idEquipo === "" ||
    $ciTecnico === "" ||
    $nombrePrestado === "" ||
    $ciPrestado === "" ||
    $fechaDevolucion === ""

) {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!is_numeric($idEquipo) && $idEquipo > 0) {
    $mensaje = "El ID del equipo debe ser un número entero mayor a 0.";

    header(
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (strlen($ciTecnico) !== 8 || !is_numeric($ciTecnico)) {
    $mensaje = "La cédula del técnico debe tener 8 dígitos y ser un número válido.";

    header(
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!$fechaDevolucion) {
    $mensaje = "La fecha u hora ingresadas no son válidas.";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

if (strlen($fechaDevolucion) !== 16) {
    $mensaje = "Error al recibir la información correspondiente a la fecha y la hora de devolución";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

$fecha = DateTime::createFromFormat('Y-m-d\TH:i', $fechaDevolucion);

$fechaFin = $fecha->format('Y/m/d');
$horaFin = $fecha->format('H:i');

$ahora = new DateTime();

if ($fecha <= $ahora) {
    $mensaje = "La fecha y hora deben ser posteriores al momento actual.";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($ciPrestado) !== 8 || !is_numeric($ciPrestado)) {
    $mensaje = "La cédula del prestado debe tener 8 dígitos y ser un número positivo.";
    header("Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($ciTecnico) !== 8 || !is_numeric($ciTecnico)) {
    $mensaje = "La cédula del técnico debe tener 8 dígitos y ser un número positivo.";
    header("Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}


if(strlen($horaFin) !== 5) {
    $mensaje = "La hora no tiene el formato valido: (HH:MM)";
    header("Location: ../../..public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
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
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$AltaPrestamo = new AltaPrestamo($conexion);

$resultado = $AltaPrestamo->registrarPrestamo(
 $ciTecnico,
 $nombrePrestado,
 $ciPrestado,
 $fechaFin,
 $horaFin,
 $idEquipo
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar la solicitud.";


    header(
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Prestamo registrado correctamente.";

header(
    "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?resultado="
    . urlencode($mensaje)
);

exit();