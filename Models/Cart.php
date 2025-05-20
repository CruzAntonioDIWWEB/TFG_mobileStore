<?php

namespace Models;

use config\DatabaseConfig;
use PDO;
use DateTime;

class Cart
{
    // Attributes
    private $id;
    private $user_id;
    private $product_id;
    private $quantity;
    private $date_added;
    private $db;

    // Constructor
    public function __construct(){

        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();

        // Set current date and time
        $dataTime = new DateTime();
        $this->date_added = $dataTime->format('Y-m-d H:i:s');
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getProductId() {
        return $this->product_id;
    }
    
    public function getQuantity() {
        return $this->quantity;
    }
    
    public function getDateAdded() {
        return $this->date_added;
    }
    
    public function getDb() {
        return $this->db;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function setProductId($product_id) {
        $this->product_id = $product_id;
        return $this;
    }
    
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }
    
    public function setDateAdded($date_added) {
        $this->date_added = $date_added;
        return $this;
    }

    /**
     * Add product to cart or update quantity if already exists
     * @return bool true on success, false on failure
     */
    public function addToCart(){
        try{
            // Check if product already exists in cart
            $check = $this->db->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $check->bindParam(':user_id', $this->user_id);
            $check->bindParam(':product_id', $this->product_id);
            $check->execute();

            if($check->rowCount() > 0){
                // Product exists, update quantity
                $existingProduct = $check->fetch(PDO::FETCH_ASSOC);
                $newQuantity = $existingProduct['quantity'] + $this->quantity;

                $update = $this->db->prepare("UPDATE cart SET quantity = :quantity, date_added = :date_added WHERE id = :id AND product_id = :product_id");
                $update->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
                $update->bindParam(':date_added', $this->date_added, PDO::PARAM_STR);
                $update->bindParam(':id', $existingProduct['id'], PDO::PARAM_INT);

                return $update->execute();
            } else {
                // Product doesn't exist, insert new 
                $insert = $this->db->prepare("INSERT INTO cart (user_id, product_id, quantity, date_added) VALUES (:user_id, :product_id, :quantity, :date_added)");
                $insert->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
                $insert->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
                $insert->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
                $insert->bindParam(':date_added', $this->date_added, PDO::PARAM_STR);
                $result = $insert->execute();

                if($result){
                    $this->id = $this->db->lastInsertId();
                }

                return $result;
            }
        }catch (\PDOException $e) {
            error_log("Error adding to cart: " . $e->getMessage());
            return false;
        }
    }


}

?>