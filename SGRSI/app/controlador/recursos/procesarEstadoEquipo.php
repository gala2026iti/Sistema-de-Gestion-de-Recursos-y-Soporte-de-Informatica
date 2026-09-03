<?php

/**
 * @file procesarEstadoEquipo.php
 *
 * @brief Procesa la activación o desactivación de equipos.
 *
 * Valida la solicitud y solicita al modelo actualizar el estado del equipo.
 */

require_once __DIR__ . "/../../../config/config.php";

require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/recursos/EstadoDatosEquipo.php";

session_start();

function insertarFiltros(string $url): string {
 $estado = $_POST['estado'] ?? '';
 $orden = $_POST['orden'] ?? '';
 $ubicacion = $_POST['ubicacion'] ?? '';
 $tipoUbicacion = $_POST['tipoUbicacion'] ?? '';

 return $url . (strpos($url, '?') !== false ? '&' : '?') . "estado=$estado&orden=$orden&ubicacion=$ubicacion&tipoUbicacion=$tipoUbicacion";
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";

    header(
        insertarFiltros(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje))
    );
    exit();
}

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso denegado: debe iniciar sesión.";

    header(
        insertarFiltros(
        "Location: ../../../public/paginaWeb/index.php?error="
        . urlencode($mensaje))
    );
    exit();
}

if (!($_SESSION["administrador"] ?? false)) {
    $mensaje = "Acceso denegado: no tiene permisos para realizar esta operación.";

    header(
        insertarFiltros(
        "Location: ../../../public/paginaWeb/index.php?error="
        . urlencode($mensaje))
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
        insertarFiltros(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje))
    );
    exit();
}

$idEquipo = trim($_POST["idEquipo"] ?? "");
$accion = strtolower(trim($_POST["accion"] ?? ""));

if ($idEquipo === "" || $accion === "") {
    $mensaje = "No se recibieron los datos necesarios.";

    header(
        insertarFiltros(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje))
    );
    exit();
}

if ($accion === "activar") {
    $activo = true;
} elseif ($accion === "desactivar") {
    $activo = false;
} else {
    $mensaje = "La acción solicitada no es válida.";

    header(
        insertarFiltros(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje))
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
        insertarFiltros(
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje))
    );
    exit();
}

$estadoDatosEquipo = new EstadoDatosEquipo($conexion);

$resultado = $estadoDatosEquipo->cambiarEstadoEquipo(
    $idEquipo,
    $activo
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo modificar el estado del equipo.";

    header(
        insertarFiltros(
            "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
            . urlencode($mensaje)
        )
    );
    exit();
}

$mensaje = $activo
    ? "Equipo activado correctamente."
    : "Equipo desactivado correctamente.";

header(
    insertarFiltros(
    "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?resultado="
    . urlencode($mensaje))
);

exit();