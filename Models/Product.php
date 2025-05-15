<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

class Product
{
    
    // Attributes 
    private $id;
    private $category_id;
    private $type_accessory;
    private $nombre;
    private $description;
    private $price;
    private $stock;
    private $image;
    private $created_at;
    private $updated_at;
    private $db;

    // Constructor
    public function __construct(){
        // Database connection
        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getCategoryId() {
        return $this->category_id;
    }
    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }

    public function getTypeAccessory() {
        return $this->type_accessory;
    }
    public function setTypeAccessory($type_accessory) {
        $this->type_accessory = $type_accessory;
    }

    public function getNombre() {
        return $this->nombre;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }

    public function getPrice() {
        return $this->price;
    }
    public function setPrice($price) {
        $this->price = $price;
    }

    public function getStock() {
        return $this->stock;
    }
    public function setStock($stock) {
        $this->stock = $stock;
    }

    public function getImage() {
        return $this->image;
    }
    public function setImage($image) {
        $this->image = $image;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }
    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }

    public function getDb() {
        return $this->db;
    }
    public function setDb($db) {
        $this->db = $db;
    }

    /**
     * Save a product to the database
     * @return bool true on success, false on failure
     */

    public function save (){
        try {
            $sql = 'INSERT INTO productos (categoria_id, tipo_accesorio_id, nombre, descripcion, precio, stock, imagen) 
                    VALUES (:categoria_id, :tipo_accesorio_id, :nombre, :descripcion, :precio, :stock, :imagen)';

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':categoria_id', $this->category_id, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_accesorio_id', $this->type_accessory, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $this->description, PDO::PARAM_STR);
            $stmt->bindParam(':precio', $this->price, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $this->stock, PDO::PARAM_INT);
            $stmt->bindParam(':imagen', $this->image, PDO::PARAM_STR);

            return $stmt->execute();

            if ($result) {
                $this->id = $this->db->lastIndertId();
                return true;
            }

            return false;

        } catch (\PDOException $e) {
            error_log("Error saving product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * * Get all products
     * @return array of products
     */
    public function getAllProducts() {
        try {
            $sql = "SELECT * FROM productos ORDER BY id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);  
        } catch (\PDOException $e) {
            error_log("Error getting all the products: " . $e->getMessage());
            return []; 
        }
    }

    /**
     * Update a product in the database
     * @return bool true on success, false on failure
     */
    public function update(){
        try {
            $sql = 'UPDATE productos SET categoria_id = :categoria_id, tipo_accesorio_id = :tipo_accesorio_id, 
                    nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock';
            
            $params = [
                ':id' => $this->id,
                ':categoria_id' => $this->category_id,
                ':tipo_accesorio_id' => $this->type_accessory,
                ':nombre' => $this->nombre,
                ':descripcion' => $this->description,
                ':precio' => $this->price,
                ':stock' => $this->stock,
            ];

            // If an image is provided, add it to the SQL statement and parameters
            if (!empty($this->image)) {
                $sql .= ', imagen = :imagen';
                $params[':imagen'] = $this->image;
            }

            $sql .= ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);

            foreach ($params as $param => $value) {
                if($param == ':id' || $param == ':categoria_id' || $param == ':tipo_accesorio_id' || $param == ':stock') {
                    $stmt->bindValue($param, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $value, PDO::PARAM_STR);
                }
            }

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a product from the database
     * @return bool true on success, false on failure
     */
    public function delete(){
        try{
            $delete = $this->db->prepare('DELETE FROM productos WHERE id = :id');
            $delete->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $delete->execute();
        }catch (\PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a product by ID
     * @return array of product data
     */
    public function getProductById($id){
        try{
            $query = $this->db->prepare("SELECT * FROM productos WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                $product_data = $query->fetch(PDO::FETCH_ASSOC);

                $this->id = $product_data['id'];
                $this->category_id = $product_data['categoria_id'];
                $this->type_accessory = $product_data['tipo_accesorio_id'];
                $this->nombre = $product_data['nombre'];
                $this->description = $product_data['descripcion'];
                $this->price = $product_data['precio'];
                $this->stock = $product_data['stock'];
                $this->image = $product_data['imagen'];
                $this->created_at = $product_data['created_at'];
                $this->updated_at = $product_data['updated_at'];

                return $this;
            }

            return false;
        }catch (\PDOException $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Function to get all the prudcts in the database
     * @return array|false array of products if successful, otherwise false
     */
    public function getAll(){
        try {
            $query = $this->db->query("SELECT * FROM usuarios ORDER BY id DESC");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return false;
        }
    }

}

?>