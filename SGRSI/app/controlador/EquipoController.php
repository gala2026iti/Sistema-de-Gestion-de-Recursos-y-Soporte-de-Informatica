<?php

require_once RUTA_MODELO . "/EquipoDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class EquipoController
{
    public function gestionar(string $metodo, ?string $idRecurso = null, ?string $subrecurso = null, array $segmentosRuta = []): void
    {
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        if ($idRecurso === "filtros") {                          
            if ($metodo !== "GET") {
                RespuestaJson::error("Método no permitido", 405);
            }

            $segmentosFiltros = array_slice($segmentosRuta, 1);
            $this->listar(null, $segmentosFiltros);
            return;
        }

        if (count($segmentosRuta) > 2) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        if ($subrecurso !== null) {
            if ($subrecurso === "ubicacion") {
                if ($metodo !== "PUT") {
                    RespuestaJson::error("Método no permitido", 405);
                }
                $this->modificarUbicacion($idRecurso);
                return;
            }

            RespuestaJson::error("Subrecurso no encontrado", 404);
        }

        match ($metodo) {
            "GET" => $this->listar($idRecurso),
            "POST" => $this->alta(),
            "PATCH" => $this->cambiarEstado($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }

    private function listar(?string $idRecurso, array $segmentosFiltros = []): void
    {
        $rolActual = $_SESSION["rolActual"] ?? "";

        if ($rolActual !== "administrador" && $rolActual !== "tecnico" && $rolActual !== "docente") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
        $orden = "";
        $estado = "";
        $ubicacion = "";
        $tipoUbicacion = "";
        $idEquipo = "";

        if ($idRecurso !== null && $idRecurso !== "") {
            $idEquipo = trim($idRecurso);
            $this->validarIdEquipo($idEquipo, "El código del equipo no es válido.");
        } else {
            $filtros = $this->leerFiltrosRuta($segmentosFiltros);
            $orden = $filtros["orden"];
            $estado = $filtros["estado"];
            $ubicacion = $filtros["ubicacion"];
            $tipoUbicacion = $filtros["tipoUbicacion"];
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);
        $equipos = $dao->listarEquipos($rolActual, $orden, $estado, $ubicacion, $tipoUbicacion, $idEquipo);
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

        $this->validarIdEquipo($idEquipo, "El código del equipo debe contener entre 1 y 6 dígitos.");

        $this->validarUbicacion($tipoUbicacion, $idUbicacion, $posicion);

        if ($tipoUbicacion === "prestamo" || $tipoUbicacion === "ninguna") {
            $idUbicacion = "";
            $posicion = "";
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $resultado = $dao->registrarEquipo($idEquipo, $idUbicacion, $tipoUbicacion, $posicion, $_SESSION["cedula"]);
        unset($dao, $conexion);
        Conexion::desconectar();
        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar el equipo. El código o la ubicación pueden estar ya registrados.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Equipo registrado correctamente."], 201);
    }


    private function modificarUbicacion(?string $idEquipo): void
    {
        $this->verificarAdministrador();
        Token::verificarCSRF();

        if ($idEquipo === null) {
            RespuestaJson::error("El código del equipo no es válido.", 422);
        }

        $this->validarIdEquipo($idEquipo, "El código del equipo no es válido.");

        $datos = $this->leerJson();

        $tipoUbicacion = strtolower(trim($datos["tipoUbicacion"] ?? ""));
        $idUbicacion = trim($datos["idUbicacion"] ?? "");
        $posicion = trim($datos["posicion"] ?? "");

        $this->validarUbicacion($tipoUbicacion, $idUbicacion, $posicion);

        if ($tipoUbicacion === "prestamo" || $tipoUbicacion === "ninguna") {
            $idUbicacion = "";
            $posicion = "";
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $resultado = $dao->modificarUbicacionEquipo($idEquipo, $idUbicacion, $tipoUbicacion, $posicion, $_SESSION["cedula"]);
        unset($dao, $conexion);
        Conexion::desconectar();
        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar la ubicación del equipo.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Ubicación del equipo modificada correctamente."]);
    }


    private function cambiarEstado(?string $idEquipo): void
    {
        $this->verificarAdministrador();
        Token::verificarCSRF();

        if ($idEquipo === null) {
            RespuestaJson::error("El código del equipo no es válido.", 422);
        }

        $this->validarIdEquipo($idEquipo, "El código del equipo no es válido.");

        $datos = $this->leerJson();
        $accion = strtolower(trim($datos["accion"] ?? ""));

        if ($accion === "") {
            RespuestaJson::error("No se recibieron los datos necesarios.", 422);
        }

        if ($accion === "activar") {
            $activo = true;

        } elseif ($accion === "desactivar") {
            $activo = false;

        } else {
            RespuestaJson::error("La acción solicitada no es válida.", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new EquipoDAO($conexion);

        $resultado = $dao->cambiarEstadoEquipo($idEquipo, $activo, $_SESSION["cedula"]);
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


    private function validarUbicacion(string $tipoUbicacion, string $idUbicacion, string $posicion): void
    {
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

    private function leerFiltrosRuta(array $segmentos): array
    {
        $filtros = ["orden" => "", "estado" => "", "ubicacion" => "", "tipoUbicacion" => ""];

        if (empty($segmentos)) {
            return $filtros;
        }

        if (count($segmentos) % 2 !== 0) {
            RespuestaJson::error("Los filtros de la ruta no son válidos.", 422);
        }


        for ($i = 0; $i < count($segmentos); $i += 2) {

            $nombre = rawurldecode($segmentos[$i]);
            $valor = rawurldecode($segmentos[$i + 1]);

            if (!array_key_exists($nombre, $filtros)) {
                RespuestaJson::error("El filtro solicitado no es válido.", 422);
            }

            $filtros[$nombre] = trim($valor);
        }


        $filtros["orden"] = strtolower($filtros["orden"]);
        $filtros["estado"] = strtolower($filtros["estado"]);

        $filtros["tipoUbicacion"] = strtolower($filtros["tipoUbicacion"]);

        $ordenesValidos = ["", "masincidencias", "menosincidencias", "reciente", "antiguo"];

        if (!in_array($filtros["orden"], $ordenesValidos, true)) {
            RespuestaJson::error("El orden seleccionado no es válido.", 422);
        }
        $estadosValidos = ["", "activo", "inactivo"];

        if (!in_array($filtros["estado"], $estadosValidos, true)) {
            RespuestaJson::error("El estado seleccionado no es válido.", 422);
        }


        $tiposUbicacionValidos = ["", "laboratorio", "taller", "prestamo", "ninguna"];


        if (!in_array($filtros["tipoUbicacion"], $tiposUbicacionValidos, true)) {
            RespuestaJson::error("El tipo de ubicación no es válido.", 422);
        }

        if ($filtros["ubicacion"] !== "" && !ctype_digit($filtros["ubicacion"])) {
            RespuestaJson::error("La ubicación seleccionada no es válida.", 422);
        }


        return $filtros;
    }
}