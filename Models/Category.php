<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

/**
 * Class Category
 */
class Category
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
     * Save a new category to the database
     * @return bool true on success, false on failure
     */
    public function saveDB(){
        try{
            $query = $this->db->prepare('INSERT INTO categorias (nombre) VALUES (:nombre)');
            $query->bindParam(':nombre', $this->name, PDO::PARAM_STR);
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

    /**
     * Update an existing category in the database
     * @return bool true on success, false on failure
     */
    public function update(){
        try{
            $query = $this->db->prepare('UPDATE categorias SET nombre = :nombre WHERE id = :id');
            $query->bindParam(':nombre', $this->name, PDO::PARAM_STR);
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();

        }catch (\PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a category from the database
     * @return bool true on success, false on failure
     */
    public function delete(){
        try{
            $delete = $this->db->prepare('DELETE FROM categorias WHERE id = :id');
            $delete->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $delete->execute();

        }catch (\PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all categories from the database
     * @return array|false array of categories on success, false on failure
     */
    public function getAll(){
        try{
            $query = $this->db->prepare('SELECT * FROM categorias ORDER BY id ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (\PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a category by ID
     * @param int $id
     * @return array|false category data on success, false on failure
     */
    public function getById($id){
        try{
            $query = $this->db->prepare("SELECT * FROM categorias WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                $category_data = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $category_data['id'];
                $this->name = $category_data['nombre'];
                $this->created_at = $category_data['created_at'];
                $this->updated_at = $category_data['updated_at'];

                return $this;
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching category by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a category by name
     * @param string $name
     * @return array|false category data on success, false on failure
     */
    public function getByName($name){
        try{
            $query = $this->db->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
            $query->bindParam(':nombre', $name, PDO::PARAM_STR);
            $query->execute();

            if($query->rowCount() > 0){
                $category_data = $query->fetch(PDO::FETCH_ASSOC);
                $this->id = $category_data['id'];
                $this->name = $category_data['nombre'];
                $this->created_at = $category_data['created_at'];
                $this->updated_at = $category_data['updated_at'];

                return $this;
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error fetching category by name: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Counts how many products are in a category
     * @return int|false number of products on success, false on failure
     */
    public function countProducts(){
        try{
            $query = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE categoria_id = :id");
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        }catch (\PDOException $e) {
            error_log("Error counting products in category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtains the products of a category
     * @return array|false array of products on success, false on failure
     */
    public function getProducts(){
        try{
            $query = $this->db->prepare("SELECT * FROM productos WHERE categoria_id = :id ORDER BY id DESC");
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);

        }catch (\PDOException $e) {
            error_log("Error obtaining products in category: " . $e->getMessage());
            return false;
        }
    }

}

?>