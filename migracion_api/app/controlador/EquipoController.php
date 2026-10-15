<?php

require_once RUTA_MODELO . "/EquipoDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class EquipoController
{
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "PUT" => $this->modificarUbicacion(),
            "PATCH" => $this->cambiarEstado(),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(): void
    {
        $rolActual = $_SESSION["rolActual"] ?? "";

        if ($rolActual !== "administrador" && $rolActual !== "tecnico" && $rolActual !== "docente") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        $orden = strtolower(trim($_GET["orden"] ?? ""));
        $estado = strtolower(trim($_GET["estado"] ?? ""));
        $ubicacion = trim($_GET["ubicacion"] ?? "");
        $tipoUbicacion = strtolower(trim($_GET["tipoUbicacion"] ?? ""));
        $id = trim($_GET["id"] ?? "");

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $equipos = $dao->listarEquipos($rolActual, $orden, $estado, $ubicacion, $tipoUbicacion, $id);

        unset($dao, $conexion);
        Conexion::desconectar();

        RespuestaJson::exito($equipos);
    }


    private function alta(): void
    {
        $this->verificarAdministrador();
        Token::verificarCSRF();

        $datos = $this->leerJson();

        $idEquipo = trim($datos["idEquipo"] ?? "");
        $tipoUbicacion = strtolower(trim($datos["tipoUbicacion"] ?? ""));
        $idUbicacion = trim($datos["idUbicacion"] ?? "");
        $posicion = trim($datos["posicion"] ?? "");

        if ($idEquipo === "") {
            RespuestaJson::error("El código del equipo es obligatorio.", 422);
        }

        $this->validarIdEquipo(
            $idEquipo,
            "El código del equipo debe contener entre 1 y 6 dígitos."
        );

        $this->validarUbicacion($tipoUbicacion, $idUbicacion, $posicion);

        if ($tipoUbicacion === "prestamo" || $tipoUbicacion === "ninguna") {
            $idUbicacion = "";
            $posicion = "";
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $resultado = $dao->registrarEquipo($idEquipo, $idUbicacion, $tipoUbicacion, $posicion);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error(
                "No se pudo registrar el equipo. El código o la ubicación pueden estar ya registrados.",
                400
            );
        }

        RespuestaJson::exito(["mensaje" => "Equipo registrado correctamente."], 201);
    }


    private function modificarUbicacion(): void
    {
        $this->verificarAdministrador();
        Token::verificarCSRF();

        $datos = $this->leerJson();

        $idEquipo = trim($datos["idEquipo"] ?? "");
        $tipoUbicacion = strtolower(trim($datos["tipoUbicacion"] ?? ""));
        $idUbicacion = trim($datos["idUbicacion"] ?? "");
        $posicion = trim($datos["posicion"] ?? "");

        $this->validarIdEquipo(
            $idEquipo,
            "El código del equipo no es válido."
        );

        $this->validarUbicacion($tipoUbicacion, $idUbicacion, $posicion);

        if ($tipoUbicacion === "prestamo" || $tipoUbicacion === "ninguna") {
            $idUbicacion = "";
            $posicion = "";
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $resultado = $dao->modificarUbicacionEquipo($idEquipo, $idUbicacion, $tipoUbicacion, $posicion);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar la ubicación del equipo.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ubicación del equipo modificada correctamente."]);
    }


    private function cambiarEstado(): void
    {
        $this->verificarAdministrador();
        Token::verificarCSRF();

        $datos = $this->leerJson();

        $idEquipo = trim($datos["idEquipo"] ?? "");
        $accion = strtolower(trim($datos["accion"] ?? ""));

        if ($idEquipo === "" || $accion === "") {
            RespuestaJson::error("No se recibieron los datos necesarios.", 422);
        }

        $this->validarIdEquipo(
            $idEquipo,
            "El código del equipo no es válido."
        );

        if ($accion === "activar") {
            $activo = true;

        } elseif ($accion === "desactivar") {
            $activo = false;

        } else {
            RespuestaJson::error("La acción solicitada no es válida.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $resultado = $dao->cambiarEstadoEquipo($idEquipo, $activo);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar el estado del equipo.", 400);
        }

        $mensaje = $activo ? "Equipo activado correctamente." : "Equipo desactivado correctamente.";

        RespuestaJson::exito(["mensaje" => $mensaje]);
    }


    private function verificarAdministrador(): void
    {
        Sesion::verificarRol("administrador");

        if (($_SESSION["rolActual"] ?? "") !== "administrador") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
    }


    private function leerJson(): array
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        return $datos;
    }


    private function validarIdEquipo(string $idEquipo, string $mensaje): void
    {
        if (!preg_match("/^[1-9][0-9]{0,5}$/", $idEquipo)) {
            RespuestaJson::error($mensaje, 422);
        }
    }


    private function validarUbicacion(
        string $tipoUbicacion,
        string $idUbicacion,
        string $posicion
    ): void {
        $tiposValidos = ["laboratorio", "taller", "prestamo", "ninguna"];

        if (!in_array($tipoUbicacion, $tiposValidos, true)) {
            RespuestaJson::error("La ubicación seleccionada no es válida.", 422);
        }

        if ($tipoUbicacion === "laboratorio" || $tipoUbicacion === "taller") {

            if ($idUbicacion === "" || !ctype_digit($idUbicacion)) {
                RespuestaJson::error("La ubicación seleccionada no es válida.", 422);
            }

            if ($posicion === "" || !ctype_digit($posicion) || (int) $posicion < 1) {
                RespuestaJson::error("La posición del equipo no es válida.", 422);
            }
        }
    }
}