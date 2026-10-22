<?php

require_once RUTA_MODELO . "/UsuarioDAO.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

require_once RUTA_VISTA . "/RespuestaJson.php";

class UsuarioController
{
    public function gestionar(string $metodo, ?string $idRecurso = null, array $segmentosRuta = []): void
    {
        Sesion::verificarRol("administrador");
        if (($_SESSION["rolActual"] ?? "") !== "administrador") {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

        if ($idRecurso === "filtros") {
            if ($metodo !== "GET") {
                RespuestaJson::error("Método no permitido", 405);
            }
            $segmentosFiltros = array_slice($segmentosRuta, 1);
            $this->listar($segmentosFiltros);
            return;
        }

        if (count($segmentosRuta) > 1) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }
        if ($metodo === "GET" && $idRecurso !== null) {
            RespuestaJson::error("La ruta solicitada no es válida.", 404);
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "PUT" => $this->modificar($idRecurso),
            "PATCH" => $this->cambiarEstado($idRecurso),
            default => RespuestaJson::error("Método no permitido", 405)
        };
    }


    private function listar(array $segmentosFiltros = []): void
    {
        $filtros = $this->leerFiltrosRuta($segmentosFiltros);

        $rol = $filtros["rol"];
        $estado = $filtros["estado"];

        $conexion = Conexion::conectar();
        $dao = new UsuarioDAO($conexion);

        $usuarios = $dao->listarUsuarios($rol, $estado);
        unset($dao, $conexion);

        Conexion::desconectar();
        RespuestaJson::exito($usuarios);
    }


    private function alta(): void
    {
        Token::verificarCSRF();
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $correo = trim($datos["correo"] ?? "");
        $clave = $datos["clave"] ?? "";
        $confirmarClave = $datos["confirmarClave"] ?? "";
        $roles = $datos["roles"] ?? [];

        if (!is_array($roles)) {
            RespuestaJson::error("Los roles seleccionados no son válidos.", 422);
        }

        $roles = array_map(
            function ($rol) {
                return strtolower(trim($rol));
            },
            $roles
        );

        $roles = array_unique($roles);

        if ($cedula === "" || $nombre === "" || $correo === "" || $clave === "" || $confirmarClave === "" || empty($roles)) {
            RespuestaJson::error("Existen campos vacíos.", 422);
        }

        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("La cédula debe contener exactamente 8 dígitos.", 422);
        }

        if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/u", $nombre)) {
            RespuestaJson::error("El nombre contiene caracteres no válidos.", 422);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            RespuestaJson::error("El correo electrónico no es válido.", 422);
        }

        if (strlen($clave) < 12) {
            RespuestaJson::error("La contraseña debe contener al menos 12 caracteres.", 422);
        }

        if ($clave !== $confirmarClave) {
            RespuestaJson::error("Las contraseñas no coinciden.", 422);
        }

        $rolesValidos = ["administrador", "tecnico", "docente"];

        foreach ($roles as $rol) {
            if (!in_array($rol, $rolesValidos, true)) {
                RespuestaJson::error("Uno de los roles seleccionados no es válido.", 422);
            }
        }

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);

        $conexion = Conexion::conectar();
        $dao = new UsuarioDAO($conexion);

        $resultado = $dao->registrarUsuario($cedula, $nombre, $correo, $claveHash, $roles, $_SESSION["cedula"]);
        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo registrar el usuario. La cédula o el correo pueden estar ya registrados.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Usuario registrado correctamente."], 201);
    }


    private function modificar(?string $cedula): void
    {
        Token::verificarCSRF();

        if ($cedula === null || !preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("La cédula no es válida.", 422);
        }

        $datos = json_decode(file_get_contents("php://input"), true);
        $nombre = trim($datos["nombre"] ?? "");
        $correo = trim($datos["correo"] ?? "");
        $clave = $datos["clave"] ?? "";
        $confirmarClave = $datos["confirmarClave"] ?? "";
        $roles = $datos["roles"] ?? [];

        if (!is_array($roles)) {
            RespuestaJson::error("Los roles seleccionados no son válidos.", 422);
        }

        $roles = array_map(
            function ($rol) {
                return strtolower(trim($rol));
            },
            $roles
        );

        $roles = array_unique($roles);

        if ($nombre === "" || $correo === "") {
            RespuestaJson::error("Existen campos vacíos.", 422);
        }

        if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,100}$/u", $nombre)) {
            RespuestaJson::error("El nombre contiene caracteres no válidos.", 422);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            RespuestaJson::error("El correo electrónico no es válido.", 422);
        }

        $rolesValidos = ["administrador", "tecnico", "docente"];

        foreach ($roles as $rol) {
            if (!in_array($rol, $rolesValidos, true)) {
                RespuestaJson::error("Uno de los roles seleccionados no es válido.", 422);
            }
        }

        $claveHash = null;

        if ($clave !== "" || $confirmarClave !== "") {
            if ($clave === "" || $confirmarClave === "") {
                RespuestaJson::error("Debe completar ambos campos de contraseña.", 422);
            }

            if (strlen($clave) < 12) {
                RespuestaJson::error("La contraseña debe contener al menos 12 caracteres.", 422);
            }

            if ($clave !== $confirmarClave) {
                RespuestaJson::error("Las contraseñas no coinciden.", 422);
            }

            $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        }

        $conexion = Conexion::conectar();
        $dao = new UsuarioDAO($conexion);
        $resultado = $dao->modificarUsuario($cedula, $nombre, $correo, $roles, $_SESSION["cedula"], $claveHash);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar el usuario. El correo puede estar registrado.", 400);
        }

        RespuestaJson::exito(["mensaje" => "Usuario modificado correctamente."]);
    }


    private function cambiarEstado(?string $cedula): void
    {
        Token::verificarCSRF();
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        if ($cedula === null || !preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("La cédula no es válida.", 422);
        }

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
        $dao = new UsuarioDAO($conexion);

        $resultado = $dao->cambiarEstadoUsuario($cedula, $activo, $_SESSION["cedula"]);

        unset($dao, $conexion);
        Conexion::desconectar();

        if (!$resultado) {
            RespuestaJson::error("No se pudo modificar el estado del usuario.", 400);
        }

        $mensaje = $activo ? "Usuario activado correctamente." : "Usuario desactivado correctamente.";
        RespuestaJson::exito(["mensaje" => $mensaje]);
    }

    private function leerFiltrosRuta(array $segmentos ): array {
        $filtros = ["rol" => "", "estado" => ""];


        if (empty($segmentos)) {
            return $filtros;
        }

        if (count($segmentos) % 2 !== 0) {
            RespuestaJson::error( "Los filtros de la ruta no son válidos.", 422);
        }


        for ($i = 0; $i < count($segmentos); $i += 2) {
            $nombre =rawurldecode($segmentos[$i]);
            $valor = strtolower(trim(rawurldecode( $segmentos[$i + 1] )));

            if (!array_key_exists($nombre, $filtros)) {
                RespuestaJson::error("El filtro solicitado no es válido.", 422);
            }

            $filtros[$nombre] = $valor;
        }

        $rolesValidos = ["", "administrador", "tecnico", "docente"];
        if (!in_array($filtros["rol"], $rolesValidos, true )) {
            RespuestaJson::error("El rol seleccionado no es válido.", 422);
        }


        $estadosValidos = ["", "activo", "inactivo" ];

        if (!in_array($filtros["estado"], $estadosValidos, true )) {
            RespuestaJson::error( "El estado seleccionado no es válido.", 422);
        }
        return $filtros;
    }
}