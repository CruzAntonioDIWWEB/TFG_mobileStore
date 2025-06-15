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
    public function __construct()
    {
        // Database connection
        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();
    }

    // Getters and Setters
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Save a new order item to the database
     * @return bool true on success, false on failure
     */
    public function save()
    {
        try {
            $sql = 'INSERT INTO order_items (order_id, product_id, quantity) 
                    VALUES (:order_id, :product_id, :quantity)';

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':order_id', $this->order_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);

            $result = $stmt->execute();
            if ($result) {
                $this->id = $this->db->lastInsertId();
                return true;
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error saving order item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing order item in the database
     * @return bool true on success, false on failure
     */
    public function updateDB()
    {
        try {
            $sql = 'UPDATE order_items SET order_id = :order_id, 
                    product_id = :product_id, quantity = :quantity 
                    WHERE id = :id';

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':order_id', $this->order_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating order item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an order item from the database
     * @return bool true on success, false on failure
     */
    public function delete()
    {
        try {
            $delete = $this->db->prepare('DELETE FROM order_items WHERE id = :id');
            $delete->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $delete->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting order item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get an order item by ID
     * @param int $id Order item ID
     * @return OrderItem|false OrderItem object or false on failure
     */
    public function getOrderItemById($id)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM order_items WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $item_data = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->id = $item_data['id'];
                $this->order_id = $item_data['order_id'];
                $this->product_id = $item_data['product_id'];
                $this->quantity = $item_data['quantity'];

                return $this;
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error getting order item by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all items for a specific order
     * @param int $order_id Order ID
     * @return array|false Array of order items or false on failure
     */
    public function getItemsByOrder($order_id)
    {
        try {
            $query = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
            $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error getting items by order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get detailed items for a specific order with product information
     * @param int $order_id Order ID
     * @return array|false Array of order items with product details or false on failure
     */
    public function getDetailedItemsByOrder($order_id)
    {
        try {
            $query = $this->db->prepare('
                SELECT oi.*, p.name, p.price, p.image 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ');
            $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error getting detailed items by order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get order item count for a specific order
     * @param int $order_id Order ID
     * @return int|false Count of items or false on failure
     */
    public function getItemCountByOrder($order_id)
    {
        try {
            $query = $this->db->prepare('
                SELECT SUM(quantity) as total 
                FROM order_items 
                WHERE order_id = :order_id
            ');
            $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (\PDOException $e) {
            error_log("Error getting item count by order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the product associated with this order item
     * @return Product|false Product object or false on failure
     */
    public function getProduct()
    {
        try {
            $product = new Product();
            return $product->getProductById($this->product_id);
        } catch (\Exception $e) {
            error_log("Error getting product for order item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate the subtotal for this order item (price * quantity)
     * @return float|false Subtotal or false on failure
     */
    public function calculateSubtotal()
    {
        try {
            $product = $this->getProduct();
            if ($product) {
                return $product->getPrice() * $this->quantity;
            }
            return false;
        } catch (\Exception $e) {
            error_log("Error calculating subtotal: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format subtotal with currency symbol
     * @return string|false Formatted subtotal or false on failure
     */
    public function formatSubtotal()
    {
        $subtotal = $this->calculateSubtotal();
        if ($subtotal !== false) {
            return number_format($subtotal, 2, ',', '.') . ' €';
        }
        return false;
    }
}
