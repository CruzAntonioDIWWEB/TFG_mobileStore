<?php

namespace config;

use PDO;
use PDOException;

class DatabaseConfig
{
    private $connection;
    private $host;
    private $dbname;
    private $user;
    private $password;

    public function __construct()
    {
        // Load the environment variables from .env file
        require_once __DIR__ . '/../vendor/autoload.php';

        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // Set database connection parameters from environment variables
        $this->host = $_ENV['DBHOST'];
        $this->dbname = $_ENV['DBNAME'];
        $this->user = $_ENV['DBUSER'];
        $this->password = $_ENV['DBPASSWORD'];

        // Establish the database connection
        $this->connect();
    }

    /**
     * Connect to the database using PDO
     */
    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $this->connection = new PDO($dsn, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Get the PDO connection instance
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
