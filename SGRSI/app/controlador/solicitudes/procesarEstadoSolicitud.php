<?php

/**
 * @file procesarEstadoSolicitud.php
 *
 * @brief Procesa cambios de estado de solicitudes.
 *
 * Valida la solicitud recibida y solicita al modelo actualizar su estado de finalización.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/solicitudes/EstadoDatosSolicitud.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../../public/paginaWeb/solicitudes/gestionSolicitudes.php?error="
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
        "Location: ../../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$idSolicitud = trim($_POST["idSolicitud"] ?? "");
$accion = strtolower(trim($_POST["finalizar"] ?? ""));

if ($idSolicitud === "" || $accion === "") {
    $mensaje = "No se recibieron los datos necesarios.";

    header(
        "Location: ../../../public/paginaWeb/solicitudes/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if ($accion === "finalizar") {
    $finalizada = true;
} else {
    $mensaje = "La acción solicitada no es válida.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
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
        "Location: ../../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$estadoDatosSolicitud = new EstadoDatosSolicitud($conexion);

$resultado = $estadoDatosSolicitud->cambiarEstadoSolicitud(
    $idSolicitud,
    $finalizada
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el estado de la solicitud.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/gestionSolicitudes.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = $finalizada
    ? "Solicitud finalizada correctamente."
    : "Solicitud no finalizada correctamente.";

header(
    "Location: ../../../public/paginaWeb/tecnico/gestionSolicitudes.php?resultado="
    . urlencode($mensaje)
);


exit();
