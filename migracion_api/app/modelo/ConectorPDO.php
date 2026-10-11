<?php
class ConectorPDO
{
    private string $servername;
    private int $port;
    private string $username;
    private string $password;
    private string $dbname;

    private ?PDO $conexion = null;

    private static ?self $instancia = null;

    private function __construct(
        string $servername,
        int $port,
        string $username,
        string $password,
        string $dbname
    ) {
        $this->servername = $servername;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): void
    {
        throw new Exception(
            "No se puede deserializar un Singleton."
        );
    }

    public function __serialize(): array
    {
        throw new LogicException(
            "El objeto no puede serializarse."
        );
    }

    public function __unserialize(array $data): void
    {
        throw new LogicException(
            "El objeto no puede deserializarse."
        );
    }

    public static function obtenerInstancia(
        string $servername,
        int $port,
        string $username,
        string $password,
        string $dbname
    ): ConectorPDO {
        if (self::$instancia === null) {
            self::$instancia = new self(
                $servername,
                $port,
                $username,
                $password,
                $dbname
            );
        }

        return self::$instancia;
    }

    public function establecerConexion(): PDO
    {
        if ($this->conexion !== null) {
            return $this->conexion;
        }

        $dsn =
            "mysql:" .
            "host={$this->servername};" .
            "port={$this->port};" .
            "dbname={$this->dbname};" .
            "charset=utf8mb4";

        $this->conexion = new PDO(
            $dsn,
            $this->username,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        return $this->conexion;
    }

    public function desconectar(): void
    {
        $this->conexion = null;
    }
}