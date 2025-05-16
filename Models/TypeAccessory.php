<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

// Class TypeAccessory
class TypeAccessory
{
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
     * Save the TypeAccessory to the database
     * @return bool true on success, false on failure
     */
    public function saveDB(){
        try{
            $query = $this->db->prepare('INSERT INTO tipo_accesorio (nombre) VALUES (:nombre)');
            $query->bindParam(':nombre', $this->name, PDO::PARAM_STR);
            $result = $query->execute();
            if($result){
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        }catch (\PDOException $e) {
            error_log("Error saving type accessory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the TypeAccessory in the database
     * @return bool true on success, false on failure
     */
    public function update(){
        try{
            $query = $this->db->prepare('UPDATE tipo_accesorio SET nombre = :nombre WHERE id = :id');
            $query->bindParam(':nombre', $this->name, PDO::PARAM_STR);
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();

        }catch (\PDOException $e) {
            error_log("Error updating type accessory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete the TypeAccessory from the database
     * @return bool true on success, false on failure
     */
    public function delete(){
        try{
            $query = $this->db->prepare('DELETE FROM tipo_accesorio WHERE id = :id');
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();

        }catch (\PDOException $e) {
            error_log("Error deleting type accessory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all TypeAccessory records from the database
     * @return array|false array of TypeAccessory objects on success, false on failure
     */
    public function getAll(){
        try{
            $query = $this->db->prepare('SELECT * FROM tipo_accesorio ORDER BY id DESC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (\PDOException $e) {
            error_log("Error fetching type accessories: " . $e->getMessage());
            return false;
        }
    }

}

?>