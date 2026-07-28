<?php

/**
 * @brief Gestiona la conexión con la base de datos mediante PDO.
 *
 * Establece y cierra la conexión con la base de datos MySQL del sistema.
 */
class ConectorPDO
{
    /**
     * @brief Servidor de la base de datos.
     */
    private string $servername;
    /**
     * @brief Usuario de la base de datos.
     */
    private string $username;
    /**
     * @brief Contraseña del usuario de la base de datos.
     */
    
    private string $password;

    /**
     * @brief Nombre de la base de datos.
     */
    private string $dbname;

    /**
     * @brief Conexión con la base de datos.
     */
    private ?PDO $conexion;

    /**
     * @brief Construye un objeto ConectorPDO.
     *
     * @param string $servername Servidor donde se encuentra la base de datos.
     * @param string $username Usuario de la base de datos.
     * @param string $password Contraseña del usuario de la base de datos.
     * @param string $dbname Nombre de la base de datos.
     */
    public function __construct(
        string $servername,
        string $username,
        string $password,
        string $dbname
    ) {
        $this->servername = $servername;
        $this->username   = $username;
        $this->password   = $password;
        $this->dbname     = $dbname;
        $this->conexion   = null;
    }

    /**
     * @brief Establece una conexión con la base de datos.
     *
     * Crea una instancia de PDO y configura el manejo de errores mediante excepciones.
     *
     * @return PDO|null Conexión establecida o null si ocurre un error.
     */
    public function establecerConexion(): ?PDO
    {
        try {
            $this->conexion = new PDO(
                "mysql:host=$this->servername;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch (PDOException $e) {
            echo "Error al conectar con la base de datos: "
                . $e->getMessage();
        }

        return $this->conexion;
    }

    /**
     * @brief Cierra la conexión con la base de datos.
     */
    public function desconectar(): void
    {
        $this->conexion = null;
    }
}