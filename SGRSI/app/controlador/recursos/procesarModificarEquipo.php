<?php

/**
 * @file procesarModificarUsuario.php
 *
 * @brief Procesa la modificación de usuarios.
 *
 * Valida los datos recibidos mediante POST y solicita al modelo
 * la actualización de los datos, contraseña y roles del usuario.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/ModificarUbicacionEquipo.php";

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
        "Location: ../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$idEquipo = trim($_POST["idEquipo"] ?? "");
$idUbicacion = trim($_POST["idUbicacion"] ?? "");
$tipoUbicacion = trim($_POST["tipoUbicacion"] ?? "");



if (!is_numeric($idEquipo) || strlen($idEquipo) > 6) {
    $mensaje = "El ID del equipo debe ser un número entero de hasta 6 dígitos.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if(!is_numeric($idUbicacion)) {
    $mensaje = "El ID de la ubicación debe ser un número entero.";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
        . urlencode($mensaje)
    );
    exit();
}

if($tipoUbicacion !== "laboratorio" && $tipoUbicacion !== "salon") {
    $mensaje = "El tipo de ubicación no es válido: solo se permiten 'laboratorio' o 'salon'";

    header(
        "Location: ../../public/paginaWeb/administracion/gestionEquipos.php?error="
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

$modificarUbicacionEquipo = new ModificarUbicacionEquipo($conexion);

$resultado = $modificarUbicacionEquipo->modificarUbicacionEquipo(
    $idEquipo,
    $idUbicacion,
    $tipoUbicacion
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el usuario. La cédula o el correo pueden estar registrados.";

    header(
        "Location: ../../../public/paginaWeb/administracion/gestionUsuarios.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Usuario modificado correctamente.";

header(
    "Location: ../../../public/paginaWeb/administracion/gestionUsuarios.php?resultado="
    . urlencode($mensaje)
);

exit();