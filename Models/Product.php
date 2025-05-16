<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

class Product
{
    
    //Attributes 
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

    //Constructor
    public function __construct(){
        //Database connection
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

     public function saveDB(){
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
    
            $result = $stmt->execute();
            if ($result) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
    
            return false;
        } catch (\PDOException $e) {
            error_log("Error saving product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a product to the database
     * @return bool true on success, false on failure
     */
    public function update(){
        try{
            $sql = 'UPDATE productos SET categoria_id = :categoria_id, tipo_accesorio_id = :tipo_accesorio_id,
                    nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock';
            
            $params = [
                ':id' => $this->id,
                ':categoria_id' => $this->category_id,
                ':tipo_accesorio_id' => $this->type_accessory,
                ':nombre' => $this->nombre,
                ':descripcion' => $this->description,
                ':precio' => $this->price,
                ':stock' => $this->stock
            ];

            // Check if an image was uploaded
            if (!empty($this->image)) {
                $sql .= ', imagen = :imagen';
                $params[':imagen'] = $this->image;
            }

            $sql .= ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);

            foreach ($params as $param => $value){
                if ($param == ':id' || $param == ':categoria_id' || $param == ':tipo_accesorio_id' || $param == ':stock'){
                    $stmt->bindParam($param, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($param, $value, PDO::PARAM_STR);
                }
            }

            return $stmt->execute();
        }catch (\PDOException $e) {
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
     * @return array of product
     */
    public function getProductById($id){
        try{
            $stmt = $this->db->prepare('SELECT * FROM productos WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            if($stmt->rowCount() > 0){
                $product_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
     * Get all products 
     * @return array of products
     */
    public function getAll(){
        try{
            $query = $this->db->prepare('SELECT * FROM productos ORDER BY id DESC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (\PDOException $e) {
            error_log("Error getting all products: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets prodcuts by category
     * @param int $category_id
     * @return array|false of products or false on failure
     */
    public function getByCategory($category_id){
        try{
            $query = $this->db->prepare('SELECT * FROM productos WHERE categoria_id = :categoria_id ORDER BY id DESC');
            $query->bindParam(':categoria_id', $category_id, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error getting products by category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get accessories by type
     * @param int $type_accessory
     * @return array|false of products or false on failure
     */
    public function getByTypeAccessory($type_accessory){
        try{
            $query = $this->db->prepare('SELECT * FROM productos WHERE tipo_accesorio_id = :tipo_accesorio_id ORDER BY id DESC');
            $query->bindParam(':tipo_accesorio_id', $type_accessory, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;

        }catch (\PDOException $e) {
            error_log("Error getting products by type accessory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all products by stock availability
     * @return array|false of products or false on failure
     */
    public function getAvailableProduct(){
        try {
            $query = $this->db->query("SELECT * FROM productos WHERE stock > 0 ORDER BY id DESC");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtaining all the available products: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find product by name or description
     * @param string $keyword
     * @return array|false of products or false on failure
     */
    public function search($keyword){
        try {
            $query = $this->db->prepare("SELECT * FROM productos WHERE nombre LIKE :keyword OR descripcion LIKE :keyword ORDER BY id DESC");
            $keyword = "%$keyword%";
            $query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
            $query->execute();

            if($query->rowCount() > 0){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;

        } catch (\PDOException $e) {
            error_log("Error searching products: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reduce the stock of a product
     * @param int $quantity
     * @return bool true on success, false on failure
     */
    public function reduceStock($quantity){
        if($this->stock >= $quantity) {
            try{
                $this->stock -= $quantity;
                $query = $this->db->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
                $query->bindParam(':stock', $this->stock, PDO::PARAM_INT);
                $query->bindParam(':id', $this->id, PDO::PARAM_INT);
                return $query->execute();
            } catch (\PDOException $e) {
                error_log("Error reducing stock: " . $e->getMessage());
                return false;
            }
        }

        return false; // Not enough stock
    }

    /**
     * Increase the stock of a product
     * @param int $quantity
     * @return bool true on success, false on failure
     */
    public function increaseStock($quantity){
        try{
            $this->stock += $quantity;
            $query = $this->db->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
            $query->bindParam(':stock', $this->stock, PDO::PARAM_INT);
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $query->execute();
        } catch (\PDOException $e) {
            error_log("Error increasing stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the category of a product
     * @return string|false category name or false on failure
     */
    public function getCategory(){
        try {
            $query = $this->db->prepare("SELECT nombre FROM categorias WHERE id = :categoria_id");
            $query->bindParam(':categoria_id', $this->category_id, PDO::PARAM_INT);
            $query->execute();
            
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['nombre'];
            }
            
            return false;
        } catch (\PDOException $e) {
            error_log("Error al obtener nombre de categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the type of accessory of a product
     * @return string|false type accessory name or false on failure
     */
    public function getTypeAccessoryName(){
        if(!$this->type_accessory){
            return false;
        }

        try{
            $query = $this->db->prepare("SELECT nombre FROM tipo_accesorio WHERE id = :tipo_accesorio_id");
            $query->bindParam(':tipo_accesorio_id', $this->type_accessory, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['nombre'];
            }

            return false;
        }catch (\PDOException $e) {
            error_log("Error obtaining the accessory name: " . $e->getMessage());
            return false;
        }
    }

    //NOT DEFINITIVE BUT THIS 2 METHODS ARE BASED ON THE CATEGORIES ID WHICH I DO NOT KNOW YET IF THEY ARE GONNA BE LIKE THAT

    /**
     * Verify if a product is a phone
     * @return bool true if it is a phone, false otherwise
     */
    public function isPhone(){
        // ID 1 is the category ID for phones
        return $this->category_id == 1;
    }

    /**
     * Verify if a product is an accessory
     * @return bool true if it is an accessory, false otherwise
     */
    public function isAccessory(){
        // ID 2 is the category ID for accessories
        return $this->category_id == 2;
    }

    /**
     * Format of the price
     * @return string formatted price
     */
    public function formatPrice(){
        return number_format($this->price, 2, ',', '.'). ' €';
    }

}

?>