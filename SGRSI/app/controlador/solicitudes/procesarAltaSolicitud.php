<?php

/**
 * @file procesarAltaUsuario.php
 *
 * @brief Procesa el registro de nuevos usuarios.
 *
 * Valida los datos recibidos mediante POST, genera el hash de la
 * contraseña y solicita al modelo el registro del usuario y sus roles.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/solicitudes/AltaSolicitud.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
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

if (!($_SESSION["administrador"] ?? false)) {
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
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

 $id = trim($_POST["id"] ?? "");
 $asunto = trim($_POST["asunto"] ?? "");
 $descripcion = trim($_POST["descripcion"] ?? "");
 $fechaLimite = trim($_POST["fechaLimite"] ?? "");
 $horaLimite = trim($_POST["horaLimite"] ?? "");
 $ciDocente = trim($_POST["ciDocente"] ?? "");
 $fecha = trim($_POST["fecha"] ?? "");
 $hora = trim($_POST["hora"] ?? "");

if (
    $id === "" ||
    $asunto === "" ||
    $descripcion === "" ||
    $fechaLimite === "" ||
    $horaLimite === "" ||
    $ciDocente === "" ||
    $fecha === "" ||
    $hora === ""
) {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (strlen($asunto) < 10 || strlen($asunto) > 30) {
    $mensaje = "El asunto debe tener entre 10 y 30 caracteres.";

    header(
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (strlen($descripcion) < 10 || strlen($descripcion) > 200) {
    $mensaje = "La descripción debe tener entre 10 y 200 caracteres.";

    header(
        "Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if (strlen($fecha) !== 10) {
    $mensaje = "La fecha no tiene el formato valido: (DD/MM/AAAA<L)";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

$fechaIngresada = DateTime::createFromFormat('d/m/Y H:i', $fecha . ' ' . $hora);

if (!$fechaIngresada) {
    $mensaje = "La fecha u hora ingresadas no son válidas.";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

$ahora = new DateTime();

if ($fechaIngresada <= $ahora) {
    $mensaje = "La fecha y hora deben ser posteriores al momento actual.";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if($ciDocente < 10000000 || $ciDocente > 99999999) {
    $mensaje = "La cédula del docente debe tener 8 dígitos.";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if($ciDocente === "") {
    $mensaje = "La cédula del docente es requerida.";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($fecha ) !== 10) {
    $mensaje = "La fecha no tiene el formato valido: (DD/MM/AAAA)";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($hora ) !== 5) {
    $mensaje = "La hora no tiene el formato valido: (HH:MM)";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
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

$AltaSolicitud = new AltaSolicitud($conexion);

$resultado = $AltaSolicitud->registrarSolicitud(
    $id,
    $asunto,
    $descripcion,
    $fechaLimite,
    $horaLimite,
    $ciDocente,
    $fecha,
    $hora
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