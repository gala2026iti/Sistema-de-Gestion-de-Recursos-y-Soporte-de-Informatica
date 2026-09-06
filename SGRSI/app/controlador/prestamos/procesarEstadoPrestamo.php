<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


/**
 * @file procesarEstadoSolicitud.php
 *
 * @brief Procesa cambios de estado de solicitudes.
 *
 * Valida la solicitud recibida y solicita al modelo actualizar su estado de finalización.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/prestamos/EstadoDatosPrestamo.php";

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

if (!($_SESSION["tecnico"])) {
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
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$id = trim($_POST["id"] ?? "");

if ($id === "") {
    $mensaje = "No se recibieron los datos necesarios.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
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
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$estadoDatosPrestamo = new EstadoDatosPrestamo($conexion);

$resultado = $estadoDatosPrestamo->cambiarEstadoPrestamo(
    $id
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el estado del préstamo.";

    header(
        "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Préstamo finalizado correctamente.";

header(
    "Location: ../../../public/paginaWeb/tecnico/tablaPrestamos.php?resultado="
    . urlencode($mensaje)
);


exit();
