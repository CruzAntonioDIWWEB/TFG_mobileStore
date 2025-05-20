<?php

namespace Models;

use config\DatabaseConfig;
use PDO;

class OrderItem
{

    // Attributes
    private $id;
    private $order_id;
    private $product_id;
    private $quantity;
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
    
    public function getOrderId() {
        return $this->order_id;
    }
    
    public function getProductId() {
        return $this->product_id;
    }
    
    public function getQuantity() {
        return $this->quantity;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setOrderId($order_id) {
        $this->order_id = $order_id;
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

    /**
     * Save a new order item to the database
     * @return bool true on success, false on failure
     */
    public function save(){
        try{
            $sql = 'INSERT INTO order_items (order_id, product_id, quantity) 
                    VALUES (:order_id, :product_id, :quantity)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':order_id', $this->order_id);
            $stmt->bindParam(':product_id', $this->product_id);
            $stmt->bindParam(':quantity', $this->quantity);

            $result = $stmt->execute();

            if($result){
                $this->id = $this->db->lastInsertId();
                return true;
            } else {
                return false;
            }
        }catch (\PDOException $e) {
            error_log("Error saving order item: " . $e->getMessage());
            return false;
        }
    }


}

?>