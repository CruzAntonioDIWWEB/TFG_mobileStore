<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

// Class AccessoryType
class AccessoryType
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
     * Save the AccessoryType to the database
     * @return bool true on success, false on failure
     */
    public function saveDB(){
        try{
            $query = $this->db->prepare('INSERT INTO accessory_types (name) VALUES (:name)');
            $query->bindParam(':name', $this->name, PDO::PARAM_STR);
            $result = $query->execute();
            if($result){
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        }catch (\PDOException $e) {
            error_log("Error saving accessory type: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the AccessoryType in the database
     * @return bool true on success, false on failure
     */
    public function updateDB(){
        try{
            $query = $this->db->prepare('UPDATE accessory_types SET name = :name WHERE id = :id');
            $query->bindParam(':name', $this->name, PDO::PARAM_STR);
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();

        }catch (\PDOException $e) {
            error_log("Error updating accessory type: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete the AccessoryType from the database
     * @return bool true on success, false on failure
     */
    public function delete(){
        try{
            $query = $this->db->prepare('DELETE FROM accessory_types WHERE id = :id');
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();

        }catch (\PDOException $e) {
            error_log("Error deleting accessory type: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all AccessoryType records from the database
     * @return array|false array of AccessoryType objects on success, false on failure
     */
    public function getAll(){
        try{
            $query = $this->db->prepare('SELECT * FROM accessory_types ORDER BY id DESC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (\PDOException $e) {
            error_log("Error fetching accessory types: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get an AccessoryType by ID
     * @return array|false AccessoryType object on success, false on failure
     */
    public function getById($id){
        try{
            $query = $this->db->prepare('SELECT * FROM accessory_types WHERE id = :id');
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            
            if($query->rowCount() > 0){
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $result['id'];
                $this->name = $result['name'];
                $this->created_at = $result['created_at'];
                $this->updated_at = $result['updated_at'];
                return $this;
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching accessory type by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get an AccessoryType by name
     * @return mixed AccessoryType object on success, false on failure
     */
    public function getByName($name){
        try{
            $query = $this->db->prepare('SELECT * FROM accessory_types WHERE name = :name');
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->execute();
            
            if($query->rowCount() > 0){
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $result['id'];
                $this->name = $result['name'];
                $this->created_at = $result['created_at'];
                $this->updated_at = $result['updated_at'];
                return $this;
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching accessory type by name: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count how many products are associated with this AccessoryType
     * @return int|false number of products on success, false on failure
     */
    public function countProducts(){
        try{
            $query = $this->db->prepare('SELECT COUNT(*) as total FROM products WHERE accessory_type_id = :id');
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];

        }catch (\PDOException $e) {
            error_log("Error counting products for accessory type: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all products associated with this AccessoryType
     * @return array|false array of Product objects on success, false on failure
     */
    public function getProducts(){
        try{
            $query = $this->db->prepare('SELECT * FROM products WHERE accessory_type_id = :id ORDER BY id DESC');
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();
            
            if($query->rowCount() > 0){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching products for accessory type: " . $e->getMessage());
            return false;
        }
    }


}

?>