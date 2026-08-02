<?php
require_once __DIR__ . "/ConectorPDO.php";
require_once __DIR__ . "/Usuario.php";

class AccesoDatosUsuario {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? ConectorPDO::getConexion();
    }

    public function buscarUsuarioPorCedula(string $cedula): ?Usuario {
        // 1. Buscar datos principales del usuario por cédula
        $sqlUsuario = "SELECT ci, nombre, correo, clave, activo FROM USUARIO WHERE ci = :cedula";
        $stmt = $this->db->prepare($sqlUsuario);
        $stmt->execute([':cedula' => $cedula]);
        $datosUser = $stmt->fetch();

        if (!$datosUser) {
            return null; // Devuelve null si no existe
        }

        // 2. Recuperar todos los roles asociados al usuario
        $sqlRoles = "SELECT rol FROM ROL WHERE ci = :cedula";
        $stmtRoles = $this->db->prepare($sqlRoles);
        $stmtRoles->execute([':cedula' => $cedula]);
        $rolesRows = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

        $isAdmin   = in_array('administrador', $rolesRows);
        $isTecnico = in_array('tecnico', $rolesRows);
        $isDocente = in_array('docente', $rolesRows);

        return new Usuario(
            $datosUser['ci'],
            $datosUser['nombre'],
            $datosUser['correo'],
            $datosUser['clave'],
            $isAdmin,
            $isTecnico,
            $isDocente,
            (bool)$datosUser['activo']
        );
    }
}