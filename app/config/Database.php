<?php

namespace App\config;

use PDO;
use PDOException;

/**
 * Clase Database
 *
 * Implementa el patrón Singleton para gestionar la conexión a la base de datos PostgreSQL.
 * Utiliza variables de entorno para la configuración de conexión.
 *
 * @package App\Config
 * @author Analía Solís <analia.solis@utp.ac.pa> - ACJ Development Team
 * @version 1.0
 */
class Database
{
    /**
     * @var string Host de la base de datos
     */
    private $host;

    /**
     * @var string Nombre de la base de datos
     */
    private $db_name;

    /**
     * @var string Usuario de la base de datos
     */
    private $username;

    /**
     * @var string Contraseña de la base de datos
     */
    private $password;

    /**
     * @var string Puerto de la base de datos
     */
    private $port;

    /**
     * @var PDO Objeto de conexión PDO
     */
    private $conn;

    /**
     * @var Database|null Instancia única de la clase (Singleton)
     */
    private static $instance = null;

    /**
     * Constructor privado para implementar el patrón Singleton.
     * Establece la conexión a la base de datos usando variables de entorno.
     *
     * @throws PDOException Si falla la conexión a la base de datos
     */
    private function __construct()
    {
        // Leemos las credenciales desde las variables de entorno
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_DATABASE'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->port = $_ENV['DB_PORT'];
        // Construimos el DSN (Data Source Name) para la conexión
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // En un entorno de producción, sería mejor registrar este error que imprimirlo
            die('Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la instancia única de la clase Database (patrón Singleton).
     *
     * @return Database La instancia única de la clase
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO a la base de datos.
     *
     * @return PDO El objeto de conexión PDO
     */
    public function getConnection()
    {
        return $this->conn;
    }
}