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

    /**
     * Update cart item quantity
     * @param int $quantity New quantity
     * @return bool true on success, false on failure
     */
    public function updateQuantity($quantity)
    {
        try {
            if ($quantity <= 0) {
                // If quantity is zero or negative, remove the item
                return $this->removeFromCart();
            }
            
            $this->quantity = $quantity;
            $update = $this->db->prepare('
                UPDATE cart 
                SET quantity = :quantity, date_added = :date_added 
                WHERE id = :id
            ');
            $update->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
            $update->bindParam(':date_added', $this->date_added, PDO::PARAM_STR);
            $update->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $update->execute();
        } catch (\PDOException $e) {
            error_log("Error updating cart quantity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove item from cart
     * @return bool true on success, false on failure
     */
    public function removeFromCart(){
        try{
            $delete = $this->db->prepare('DELETE FROM cart WHERE id = :id');
            $delete->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $delete->execute();
        } catch (\PDOException $e) {
            error_log("Error removing from cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove product from cart by product ID
     * @param int $user_id User ID
     * @param int $product_id Product ID
     * @return bool true on success, false on failure
     */
    public function removeProductFromCart($user_id, $product_id){
        try{
            $delete = $this->db->prepare('DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id');
            $delete->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $delete->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $delete->execute();
        } catch (\PDOException $e) {
            error_log("Error removing product from cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clears all items form user's cart
     * @param int $user_id User ID
     * @return bool true on success, false on failure
     */
    public function clearCart($user_id){
        try{
            $delete = $this->db->prepare('DELETE FROM cart WHERE user_id = :user_id');
            $delete->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            return $delete->execute();
        } catch (\PDOException $e) {
            error_log("Error clearing cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart item by ID
     * @param int $id Cart item ID
     * @return Cart|false Cart object or false on failure
     */
    public function getCartItemById($id){
        try{
            $query = $this->db->prepare('SELECT * FROM cart WHERE id = :id');
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            
            if($query->rowCount() > 0){
                $cartItem = $query->fetch(PDO::FETCH_ASSOC);
                $this->setId($cartItem['id']);
                $this->setUserId($cartItem['user_id']);
                $this->setProductId($cartItem['product_id']);
                $this->setQuantity($cartItem['quantity']);
                $this->setDateAdded($cartItem['date_added']);
                return $this;
            }
            
            return false;
        } catch (\PDOException $e) {
            error_log("Error fetching cart item: " . $e->getMessage());
            return false;
        }
    }

}

?>