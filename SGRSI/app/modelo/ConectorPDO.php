<?php

/**
 * @brief Gestiona la conexión con la base de datos mediante PDO.
 *
 * Esta clase se encarga de establecer y cerrar la conexión
 * con la base de datos MySQL utilizada por el sistema.
 */
class ConectorPDO
{
    /**
     * @brief Nombre o dirección del servidor de la base de datos.
     */
    private string $servername;

    /**
     * @brief Usuario utilizado para conectarse a la base de datos.
     */
    private string $username;

    /**
     * @brief Contraseña utilizada para conectarse a la base de datos.
     */
    private string $password;

    /**
     * @brief Nombre de la base de datos.
     */
    private string $dbname;

    /**
     * @brief Conexión PDO actualmente establecida.
     *
     * Puede ser null cuando todavía no se ha establecido
     * una conexión o cuando esta fue cerrada.
     */
    private ?PDO $conexion;

    /**
     * @brief Construye un objeto ConectorPDO.
     *
     * @param string $servername Servidor donde se encuentra MySQL.
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
     * Crea una instancia de PDO utilizando los datos proporcionados
     * en el constructor y configura PDO para trabajar con excepciones
     * ante errores de SQL.
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
     *
     * Libera la referencia al objeto PDO, provocando que la conexión
     * sea cerrada cuando ya no existan otras referencias a ella.
     */
    public function desconectar(): void
    {
        $this->conexion = null;
    }
}