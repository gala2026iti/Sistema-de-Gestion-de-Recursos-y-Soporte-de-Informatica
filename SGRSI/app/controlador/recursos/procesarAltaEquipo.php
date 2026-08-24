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
require_once RUTA_MODELO . "/recursos/AltaEquipo.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
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
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$id = trim($_POST["id"] ?? "");
$fechaCreacion = trim($_POST["fechaCreacion"] ?? "");
$horaCreacion = trim($_POST["horaCreacion"] ?? "");
$ultimaIntervencion = $_POST["ultimaIntervencion"] ?? "";
$idUbicacion = $_POST["idUbicacion"] ?? "";
$tipoUbicacion = $_POST["tipoUbicacion"] ?? "";
$posicion = $_POST["posicion"] ?? "";

// $activo = $_POST["activo"] ?? ""; Activo existe, pero no en POST



if (
    $id === "" ||
    $fechaCreacion === "" ||
    $horaCreacion === "" ||
    $ultimaIntervencion === ""
) {
    $mensaje = "Existen campos vacíos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(!is_numeric($id) && strlen($id) > 6)  {
    $mensaje = "El ID debe ser un número entero de hasta 6 dígitos.";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($fechaCreacion) !== 10) {
    $mensaje = "La fecha no tiene el formato valido: (DD/MM/AAAA)";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if(strlen($horaCreacion) !== 5) {
    $mensaje = "La hora no tiene el formato valido: (HH:MM)";
    header("Location: ../../public/paginaWeb/tecnico/gestionSolicitudes.php?error=" . urlencode($mensaje));
    exit();
}

if($ultimaIntervencion !== null) {
 $mensaje = "Los equipos dados de alta no cuentan con última intervención";
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

$AltaEquipo = new AltaEquipo($conexion);

$resultado = $AltaEquipo->registrarEquipo(
    $id,
    $fechaCreacion,
    $horaCreacion,
    $ultimaIntervencion,
    true,
    $idUbicacion,
    $tipoUbicacion,
    $posicion
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el equipo.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Equipo registrado correctamente.";

header(
    "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?resultado="
    . urlencode($mensaje)
);

exit();