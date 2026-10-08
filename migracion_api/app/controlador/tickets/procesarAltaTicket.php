<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/tickets/AltaTicket.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Petición incorrecta.";
    header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
    exit();
}

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso denegado: debe iniciar sesión.";
    header("Location: ../../public/paginaWeb/index.php?error=" . urlencode($mensaje));
    exit();
}

$rolActual = $_SESSION["rolActual"] ?? "";
$tieneRolActivo =
    ($rolActual === "tecnico" && ($_SESSION["tecnico"] ?? false)) ||
    ($rolActual === "docente" && ($_SESSION["docente"] ?? false));

if (!$tieneRolActivo) {
    $mensaje = "Acceso denegado: No tiene permisos para realizar esta operación.";
    header("Location: ../../public/paginaWeb/index.php?error=" . urlencode($mensaje));
    exit();
}

$csrfToken = $_POST["csrfToken"] ?? "";
if (
    !isset($_SESSION["csrfToken"]) ||
    !is_string($csrfToken) ||
    !hash_equals($_SESSION["csrfToken"], $csrfToken)
) {
    $mensaje = "Solicitud rechazada: token de seguridad inválido.";
    header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
    exit();
}

// El navegador manda todos los reportes en un solo campo JSON.
$reportes = json_decode($_POST["equiposReportados"] ?? "[]", true);

// Índices del array definidos en registroIncidencias.js:indiceBorrador.
$indiceIdUbicacion = 0;
$indiceTipoUbicacion = 1;
$indiceIdEquipo = 2;
$indiceEstado = 3;
$indiceTipo = 4;
$indiceAsunto = 5;
$indicePersona = 6;
$indiceGravedad = 7;
$indiceDescripcion = 8;

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
        "Location: ../../../public/paginaWeb/administracion/gestionInventarioTecnologico.php?error="
        . urlencode($mensaje)
    );
    exit();
}

try {
    $conexion->beginTransaction();

    $consultaReporte = $conexion->prepare(
        "INSERT INTO REPORTE (idEquipo) VALUES (:idEquipo)"
    );
    $consultaTicket = $conexion->prepare(
        "INSERT INTO TICKET (idReporte, tipo, asunto, descripcion, gravedad, persona)
         VALUES (:idReporte, :tipo, :asunto, :descripcion, :gravedad, :persona)"
    );
    $consultaDocente = $conexion->prepare(
        "INSERT INTO docente_reporta_reporte (ciDocente, idReporte)
         VALUES (:ciDocente, :idReporte)"
    );
    $consultaTecnico = $conexion->prepare(
        "INSERT INTO tecnico_gestiona_ticket (ciTecnico, idReporte, tipoInteraccion)
         VALUES (:ciTecnico, :idReporte, :tipoInteraccion)"
    );
    $consultaTecnicoComunica = $conexion->prepare(
        "INSERT INTO tecnico_comunica_reporte (idTecnico, idReporte, idEquipo)
         VALUES (:idTecnico, :idReporte, :idEquipo)"
    );

foreach ($reportes as $reporte) {
    $idUbicacion = trim(htmlspecialchars($reporte[$indiceIdUbicacion]));
    $tipoUbicacion = trim(htmlspecialchars(strtolower($reporte[$indiceTipoUbicacion])));
    $idEquipo = trim(htmlspecialchars($reporte[$indiceIdEquipo]));
    $estado = trim(htmlspecialchars(strtolower($reporte[$indiceEstado])));
    $tipo = trim(htmlspecialchars(strtolower($reporte[$indiceTipo])));
    $asunto = trim(htmlspecialchars(strtolower($reporte[$indiceAsunto])));
    $persona = trim(htmlspecialchars(strtolower($reporte[$indicePersona])));
    $gravedad = trim(htmlspecialchars(strtolower($reporte[$indiceGravedad])));
    $descripcion = trim(htmlspecialchars(strtolower($reporte[$indiceDescripcion])));

    // Se muestran las variables del reporte actual mientras se prueba el POST.
    if (empty($estado) || $estado !== "ok" && $estado !== "incidencia") {
        $mensaje = "El estado del ticket es inválido.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }

    if (empty($idUbicacion) || $idUbicacion <= 0) {
        $mensaje = "La ubicación del salón es desconocida.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }

    if (empty($tipoUbicacion) || $tipoUbicacion !== "laboratorio" && $tipoUbicacion !== "taller") {
        $mensaje = "El tipo de ubicación es inválida.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }

    if (empty($idEquipo) || $idEquipo <= 0) {
        $mensaje = "El equipo reportado es inválido.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }

    if($estado === "incidencia") {

    if (empty($tipo) || $tipo !== "hardware" && $tipo !== "software" && $tipo !== "red") {
        $mensaje = "El tipo de ticket es inválido.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }
    if (empty($asunto) || mb_strlen($asunto) > 150) {
        $mensaje = "El asunto del ticket es inválido o es muy largo.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }
    if (empty($persona) || mb_strlen($persona) > 50) {
        $mensaje = "La persona reportada es inválida.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }
    if (empty($gravedad) || $gravedad !== "ligera" && $gravedad !== "media" && $gravedad !== "grave") {
        $mensaje = "La gravedad del ticket es inválida.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }
    if (empty($descripcion) || mb_strlen($descripcion) > 200) {
        $mensaje = "La descripción del ticket es inválida o es muy larga.";
        header("Location: ../../public/paginaWeb/tecnico/homeTecnico.php?error=" . urlencode($mensaje));
        exit();
    }
    
    }
    
    $consultaReporte->execute(["idEquipo" => $idEquipo]);
    $idReporte = $conexion->lastInsertId();

    if ($idReporte === "0") {
    $conectorPDO->desconectar();
    $mensaje = "Error al crear el reporte del ticket.";
    header("Location: ../../public/paginaWeb/homeDocente.php?error=" . urlencode($mensaje));
    $resultado = false;
    exit();    
    }
    

    if ($estado === "incidencia") {
        $consultaTicket->execute([
            "idReporte" => $idReporte,
            "tipo" => $tipo,
            "asunto" => $asunto,
            "descripcion" => $descripcion,
            "gravedad" => $gravedad,
            "persona" => $persona,
        ]);
    }

    if ($rolActual === "docente") {
        $consultaDocente->execute([
            "ciDocente" => $_SESSION["cedula"],
            "idReporte" => $idReporte,
        ]);
    } elseif ($estado === "incidencia") {
        $consultaTecnico->execute([
            "ciTecnico" => $_SESSION["cedula"],
            "idReporte" => $idReporte,
            "tipoInteraccion" => "creacion",
        ]);
    } else {
        $consultaTecnicoComunica->execute([
            "idTecnico" => $_SESSION["cedula"],
            "idReporte" => $idReporte,
            "idEquipo" => $idEquipo,
        ]);
    }
}
    $conexion->commit();

    $resultado = true;
} catch (PDOException $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    $conectorPDO->desconectar();
    $mensaje = "Error al crear los tickets: ";
    header("Location: ../../public/paginaWeb/homeDocente.php?error=" . urlencode($mensaje));
    $resultado = false;
    exit();
}

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudieron registrar los tickets.";

    header(
        "Location: ../../../public/paginaWeb/homeDocente.php?error="
        . urlencode($mensaje)
    );
    exit();
}

$mensaje = "Tickets registrados correctamente.";

header(
    "Location: ../../../public/paginaWeb/homeDocente.php?resultado="
    . urlencode($mensaje)
);

exit();
