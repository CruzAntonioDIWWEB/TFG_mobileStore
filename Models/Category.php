<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

/**
 * Class Category
 */
class Category{
    // Properties
    private $id;
    private $name;
    private $created_at;
    private $updated_at;
    private $db;

    // Constructor
    public function __construct(){
        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();
    }

        // Getters
        public function getId() {
            return $this->id;
        }
        
        public function getName() {
            return $this->name;
        }
        
        public function getCreatedAt() {
            return $this->created_at;
        }
        
        public function getUpdatedAt() {
            return $this->updated_at;
        }
        
        // Setters
        public function setId($id) {
            $this->id = $id;
            return $this;
        }
        
        public function setName($name) {
            $this->name = $name;
            return $this;
        }
        
        public function setCreatedAt($created_at) {
            $this->created_at = $created_at;
            return $this;
        }
        
        public function setUpdatedAt($updated_at) {
            $this->updated_at = $updated_at;
            return $this;
        }

        /**
         * Save a new category to the database
         * @return bool true on success, false on failure
         */
        public function save(){
            try{
            $query = $this->db->prepare('INSERT INTO categorias (nombre) VALUES (:nombre)');
            $query->bindParam(':nombre', $this->name);
            $result = $query->execute();

                if($result){
                    $this->id = $this->db->lastInsertId();
                    return true;
                }
                return false;  
            }catch (\PDOException $e) {
                error_log("Error saving category: " . $e->getMessage());
                return false;
            }
        }

        



}

?>