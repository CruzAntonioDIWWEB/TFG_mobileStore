<?php

namespace config;

use PDO;
use PDOException;

class DatabaseConfig{
    private $connection;
    private $host;
    private $dbname;
    private $user;
    private $password;
    
    public function __construct(){
        //Cargo las variables de entorno desde el fichero .env
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        
        //Asigno los valores de configuración
        $this->host = $_ENV['DBHOST'];
        $this->dbname = $_ENV['DBNAME'];
        $this->user = $_ENV['DBUSER'];
        $this->password = $_ENV['DBPASSWORD'];
        
        //Se crea la conexión
        $this->connect();
    }
    
    /**
     * Se establece la conexión a la base de datos
     */
    private function connect(){
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $this->connection = new PDO($dsn, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            //echo "Conexión establecida";
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Se devuelve la conexión a la base de datos
     */
    public function getConnection()
    {
        return $this->connection;
    }
}