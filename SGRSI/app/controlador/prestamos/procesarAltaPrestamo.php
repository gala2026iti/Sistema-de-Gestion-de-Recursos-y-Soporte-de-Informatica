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
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
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
    exit();
}

 $id = htmlspecialchars(trim($_POST["idPrestamo"] ?? ""));
 $ciTecnico = htmlspecialchars(trim($_POST["ciTecnico"] ?? ""));
 $nombrePrestado = htmlspecialchars(trim($_POST["nombrePrestado"] ?? ""));
 $ciPrestado = htmlspecialchars(trim($_POST["ciPrestado"] ?? ""));
 $fechaFin = htmlspecialchars(trim($_POST["fechaFin"] ?? ""));
 $horaFin = htmlspecialchars(trim($_POST["horaFin"] ?? ""));

 // No se les hace validación ni formateo ya que no son objetos proporcionados por el usuario
 $fecha = date('d/m/Y');
 $hora = date('H:i');
 $tipoIntervencion = "creacion";

if (
    $id === "" ||
    $ciTecnico === "" ||
    $nombrePrestado === "" ||
    $ciPrestado === "" ||
    $fechaFin === "" ||
    $horaFin === ""

) {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (!is_integer($id)) {
    $mensaje = "El ID debe ser un número entero.";

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

if (strlen($fechaFin) !== 10) {
    $mensaje = "La fecha no tiene el formato valido: (DD/MM/AAAA)";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

$fechaIngresada = DateTime::createFromFormat('d/m/Y H:i', $fechaFin . ' ' . $horaFin);

if (!$fechaIngresada) {
    $mensaje = "La fecha u hora ingresadas no son válidas.";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

$ahora = new DateTime();

if ($fechaIngresada <= $ahora) {
    $mensaje = "La fecha y hora deben ser posteriores al momento actual.";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($ciPrestado) !== 8 || !is_numeric($ciPrestado)) {
    $mensaje = "La cédula del prestado debe tener 8 dígitos y ser un número válido.";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($ciTecnico) !== 8 || !is_numeric($ciTecnico)) {
    $mensaje = "La cédula del técnico debe tener 8 dígitos y ser un número válido.";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
    exit();
}


if(strlen($horaFin) !== 5) {
    $mensaje = "La hora no tiene el formato valido: (HH:MM)";
    header("Location: ../../public/paginaWeb/tecnico/tablaPrestamos.php?error=" . urlencode($mensaje));
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

$AltaPrestamo = new AltaPrestamo($conexion);

$resultado = $AltaPrestamo->registrarPrestamo(
 $id,
 $ciTecnico,
 $nombrePrestado,
 $ciPrestado,
 $fechaFin,
 $horaFin,
 $fecha,
 $hora,
 $tipoIntervencion
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar la solicitud.";


    header(
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Solicitud registrada correctamente.";

header(
    "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?resultado="
    . urlencode($mensaje)
);

exit();