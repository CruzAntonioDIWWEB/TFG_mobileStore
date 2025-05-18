<?php

namespace Models;

use config\DatabaseConfig;
use PDO;
use DateTime;

class Order
{
    // Properties
    private $id;
    private $user_id;
    private $province;
    private $locality;
    private $address;
    private $cost;
    private $status;
    private $date;
    private $time;
    private $created_at;
    private $updated_at;
    private $db;

    // Status constants
    const STATUS_PENDING = 'pendiente';
    const STATUS_SHIPPED = 'enviado';
    const STATUS_DELIVERED = 'entregado';
    const STATUS_CANCELLED = 'cancelado';
    const STATUS_PAID = 'pagado';

    // Constructor
    public function __construct(){
        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();

        // Set default status
        $this->status = self::STATUS_PENDING;

        // Set default date and time
        $dateTime = new DateTime();
        $this->date = $dateTime->format('Y-m-d');
        $this->time = $dateTime->format('H:i:s');
    }

    // Getters and Setters

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getProvince() {
        return $this->province;
    }

    public function setProvince($province) {
        $this->province = $province;
    }

    public function getLocality() {
        return $this->locality;
    }

    public function setLocality($locality) {
        $this->locality = $locality;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getCost() {
        return $this->cost;
    }

    public function setCost($cost) {
        $this->cost = $cost;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
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

    /**
     * Save a new order to the database
     * @return bool true on success, false on failure
     */
    public function saveDB(){
        try{
            $sql = 'INSERT INTO orders (user_id, province, locality, address, cost, status, date, time) 
                    VALUES (:user_id, :province, :locality, :address, :cost, :status, :date, :time)';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':province', $this->province, PDO::PARAM_STR);
            $stmt->bindParam(':locality', $this->locality, PDO::PARAM_STR);
            $stmt->bindParam(':address', $this->address, PDO::PARAM_STR);
            $stmt->bindParam(':cost', $this->cost, PDO::PARAM_STR);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
            $stmt->bindParam(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindParam(':time', $this->time, PDO::PARAM_STR);

            $result = $stmt->execute();

            if($result){
                $this->id = $this->db->lastInsertId();
                return true;
            } else {
                return false;
            }

        }catch (\PDOException $e) {
            echo "Error saving the order: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update the order in the database
     * @return bool true on success, false on failure
     */
    public function update(){
        try{
            $sql = 'UPDATE orders SET user_id = :user_id, province = :province, locality = :locality,
            address = :address, cost = :cost, status = :status, date = :date, time = :time WHERE id = :id';

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':province', $this->province, PDO::PARAM_STR);
            $stmt->bindParam(':locality', $this->locality, PDO::PARAM_STR);
            $stmt->bindParam(':address', $this->address, PDO::PARAM_STR);
            $stmt->bindParam(':cost', $this->cost, PDO::PARAM_STR);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
            $stmt->bindParam(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindParam(':time', $this->time, PDO::PARAM_STR);

            return $stmt->execute();

        } catch (\PDOException $e) {
            echo "Error updating the order: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete the order from the database
     * @return bool true on success, false on failure
     */
    public function delete(){
        try{
            $delete = 'DELETE FROM orders WHERE id = :id';
            $stmt = $this->db->prepare($delete);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            echo "Error deleting the order: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get an order by ID
     * @param int $id
     * @return Order|false the order object on success, false on failure
     */
    public function getOrderById($id){
        try{
            $query = 'SELECT * FROM orders WHERE id = :id';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $orderData = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->id = $orderData['id'];
                $this->user_id = $orderData['user_id'];
                $this->province = $orderData['province'];
                $this->locality = $orderData['locality'];
                $this->address = $orderData['address'];
                $this->cost = $orderData['cost'];
                $this->status = $orderData['status'];
                $this->date = $orderData['date'];
                $this->time = $orderData['time'];
                $this->created_at = $orderData['created_at'];
                $this->updated_at = $orderData['updated_at'];

                return $this;
            }

            return false;
        }catch (\PDOException $e) {
            echo "Error fetching the order: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get all orders from the database
     * @return array|false array of Order objects on success, false on failure
     */
    public function getAllOrders(){
        try{
            $query = 'SELECT * FROM orders ORDER BY date DESC, time DESC';
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        }catch (\PDOException $e) {
            echo "Error fetching orders: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get all orders by user ID
     * @param int $user_id
     * @return array|false array of Order objects on success, false on failure
     */
    public function getOrderByUser($user_id){
        try{
            $query = $this->db->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY date DESC, time DESC');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            if($query->rowCount() > 0){
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }

            return false;

        }catch (\PDOException $e) {
            echo "Error fetching orders by user: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get all orders by status
     * @param string $status
     * @return array|false array of Order objects on success, false on failure
     */
    public function getOrderByStatus($status){
        try{
            $query = $this->db->prepare('SELECT * FROM orders WHERE status = :status ORDER BY date DESC, time DESC');            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->execute();

            if($query->rowCount() > 0){
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }

            return false;

        }catch (\PDOException $e) {
            echo "Error fetching orders by status: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get order items (products)
     * @return array|false array of OrderItem objects on success, false on failure
     */
    public function getOrderItems(){
        try{
            $query = $this->db->prepare('
                SELECT oi.*, p.name, p.price, p.image FROM order_items oi
                JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id');
            
            $query->bindParam(':order_id', $this->id, PDO::PARAM_INT);
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }

            return false;
        }catch (\PDOException $e) {
            echo "Error fetching order items: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get user information for the order
     * @return array|false array of User objects on success, false on failure
     */
    public function getUser(){
        try{
            $query = 'SELECT * FROM users WHERE id = :user_id';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                return $userData;
            }

            return false;
        }catch (\PDOException $e) {
            echo "Error fetching user information: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update the order status
     * @param string $status
     * @return bool true on success, false on failure
     */
    public function updateStatus($status){
        try{
            $this->status = $status;
            $query = 'UPDATE orders SET status = :status WHERE id = :id';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();

        }catch (\PDOException $e) {
            echo "Error updating order status: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get the total number of orders
     * @return int|false total number of orders on success, false on failure
     */
    public function getTotalOrders(){
        try {
            $query = $this->db->query('SELECT COUNT(*) as total FROM orders');
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (\PDOException $e) {
            error_log("Error getting orders count: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sum of all orders (total sales)
     * @return float|false Sum of all orders or false on failure
     */
    public function getTotalSales()
    {
        try {
            $query = $this->db->query('SELECT SUM(cost) as total FROM orders WHERE status != "' . self::STATUS_CANCELLED . '"');
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (\PDOException $e) {
            error_log("Error getting total sales: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create order from cart items
     * @param int $user_id User ID
     * @param string $province Province
     * @param string $locality Locality
     * @param string $address Address
     * @return int|false Order ID on success, false on failure
     */
    public function createFromCart($user_id, $province, $locality, $address)
    {
        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Set order details
            $this->user_id = $user_id;
            $this->province = $province;
            $this->locality = $locality;
            $this->address = $address;

            // Get cart Items
            $cart_query = $this->db->prepare('SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id');
            $cart_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $cart_query->execute();

            if ($cart_query->rowCount() == 0) {
                return false; // No items in cart
            }

            $cart_items = $cart_query->fetchAll(PDO::FETCH_ASSOC);

            // Calculate total cost
            $total_cost = 0;
            foreach ($cart_items as $item) {
                $total_cost += $item['price'] * $item['quantity'];
            }

            $this->cost = $total_cost;

            // Save order
            if (!$this->saveDB()) {
                $this->db->rollBack();
                return false; // Failed to save order
            }

            // Save order items
            foreach ($cart_items as $item) {
                $order_item_query = $this->db->prepare('INSERT INTO order_items (order_id, product_id, quantity) VALUES (:order_id, :product_id, :quantity)');
                $order_item_query->bindParam(':order_id', $this->id, PDO::PARAM_INT);
                $order_item_query->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                $order_item_query->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                
                if (!$order_item_query->execute()) {
                    $this->db->rollBack();
                    return false; // Failed to save order item
                }

                // Update product stock directly from the database
                $update_stock_query = $this->db->prepare('UPDATE products SET stock = stock - :quantity WHERE id = :product_id AND stock >= :quantity');
                $update_stock_query->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $update_stock_query->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
                
                if (!$update_stock_query->execute() || $update_stock_query->rowCount() == 0) {
                    // If update failed or no rows affected (not enough stock)
                    $this->db->rollBack();
                    return false; // Not enough stock
                }
            }

            // Clear cart
            $clear_cart_query = $this->db->prepare('DELETE FROM cart WHERE user_id = :user_id');
            $clear_cart_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if (!$clear_cart_query->execute()) {
                $this->db->rollBack();
                return false; // Failed to clear cart
            }

            // Commit transaction
            $this->db->commit();
            return $this->id;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error creating order from cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format the cost to a currency format
     * @return string formatted cost
     */
    public function formatCost(){
        return number_format($this->cost, 2, ',', '.') . ' €';
    }

    /**
     * Get the status in a human-readable format
     * @return string formatted status
     */
    public function getStatusText(){
        switch ($this->status){
            case self::STATUS_PENDING:
                return 'Pendiente';
            case self::STATUS_SHIPPED:
                return 'Enviado';
            case self::STATUS_DELIVERED:
                return 'Entregado';
            case self::STATUS_CANCELLED:
                return 'Cancelado';
            case self::STATUS_PAID:
                return 'Pagado';
            default:
                return 'Desconocido';
        }
    }

    /**
     * Get formatted date
     * @return string formatted date
     */
    public function getFormattedDate(){
        return date('d/m/Y', strtotime($this->date));
    }

}


?>