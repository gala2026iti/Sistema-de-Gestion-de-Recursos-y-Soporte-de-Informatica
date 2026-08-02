<?php
class ConectorPDO {
    private static ?PDO $conexion = null;

    public static function getConexion(): PDO {
        if (self::$conexion === null) {
            $host = '127.0.0.1';
            $port = '3306';
            $db   = 'sgrsi';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$conexion = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                throw new \PDOException("Error de conexión PDO: " . $e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$conexion;
    }
}