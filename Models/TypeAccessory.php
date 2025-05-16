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

    /**
     * Get a TypeAccessory by ID
     * @return array|false TypeAccessory object on success, false on failure
     */
    public function getById($id){
        try{
            $query = $this->db->prepare('SELECT * FROM tipo_accesorio WHERE id = :id');
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            
            if($query->rowCount() > 0){
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $result['id'];
                $this->name = $result['nombre'];
                $this->created_at = $result['created_at'];
                $this->updated_at = $result['updated_at'];
                return $this;
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching type accessory by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a TypeAccessory by name
     * @return mixed TypeAccessory object on success, false on failure
     */
    public function getByName($name){
        try{
            $query = $this->db->prepare('SELECT * FROM tipo_accesorio WHERE nombre = :nombre');
            $query->bindParam(':nombre', $name, PDO::PARAM_STR);
            $query->execute();
            
            if($query->rowCount() > 0){
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $result['id'];
                $this->name = $result['nombre'];
                $this->created_at = $result['created_at'];
                $this->updated_at = $result['updated_at'];
                return $this;
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching type accessory by name: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count how many products are associated with this TypeAccessory
     * @return int|false number of products on success, false on failure
     */
    public function countProducts(){
        try{
            $query = $this->db->prepare('SELECT COUNT(*) as total FROM productos WHERE tipo_accesorio_id = :id');
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];

        }catch (\PDOException $e) {
            error_log("Error counting products for type accessory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all products associated with this TypeAccessory
     * @return array|false array of Product objects on success, false on failure
     */
    public function getProducts(){
        try{
            $query = $this->db->prepare('SELECT * FROM productos WHERE tipo_accesorio_id = :id ORDER BY id DESC');
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();
            
            if($query->rowCount() > 0){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching products for type accessory: " . $e->getMessage());
            return false;
        }
    }


}

?>