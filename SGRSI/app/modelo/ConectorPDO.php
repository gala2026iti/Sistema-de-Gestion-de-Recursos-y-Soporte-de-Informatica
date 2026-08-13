<?php
// app/modelo/ConectorPDO.php

class ConectorPDO
{
    private string $servername;
    private string $username;
    private string $password;
    private string $dbname;
    private ?PDO $conexion;

    /**
     * El constructor recibe los datos del servidor y la base de datos
     */
    public function __construct(string $servername, string $username, string $password, string $dbname) {
        $this->servername = $servername;
        $this->username   = $username;
        $this->password   = $password;
        $this->dbname     = $dbname;
        $this->conexion   = null;
    }

    /**
     * Crea la instancia PDO y establece la conexión con MySQL
     */
    public function establecerConexion(): ?PDO {
        try {
            $this->conexion = new PDO(
                "mysql:host=$this->servername;dbname=$this->dbname;charset=utf8mb4", 
                $this->username, 
                $this->password
            );
            
            // Configurar el modo de error de PDO a Excepción para capturar fallos SQL
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error al conectar con la base de datos: " . $e->getMessage();
        }
        
        return $this->conexion;
    }

    /**
     * Cierra la conexión liberando la variable PDO
     */
    public function desconectar(): void {
        $this->conexion = null;
    }
}