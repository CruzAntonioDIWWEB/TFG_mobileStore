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
    public function __construct()
    {

        $dbConfig = new DatabaseConfig();
        $this->db = $dbConfig->getConnection();

        // Set current date and time
        $dataTime = new DateTime();
        $this->date_added = $dataTime->format('Y-m-d H:i:s');
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getDateAdded()
    {
        return $this->date_added;
    }

    public function getDb()
    {
        return $this->db;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setDateAdded($date_added)
    {
        $this->date_added = $date_added;
        return $this;
    }

    /**
     * Add product to cart or update quantity if already exists
     * @return bool true on success, false on failure
     */
    public function addToCart()
    {
        try {
            // Check if product already exists in cart
            $check = $this->db->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $check->bindParam(':user_id', $this->user_id);
            $check->bindParam(':product_id', $this->product_id);
            $check->execute();

            if ($check->rowCount() > 0) {
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

                if ($result) {
                    $this->id = $this->db->lastInsertId();
                }

                return $result;
            }
        } catch (\PDOException $e) {
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
    public function removeFromCart()
    {
        try {
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
    public function removeProductFromCart($user_id, $product_id)
    {
        try {
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
    public function clearCart($user_id)
    {
        try {
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
    public function getCartItemById($id)
    {
        try {
            $query = $this->db->prepare('SELECT * FROM cart WHERE id = :id');
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
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

    /**
     * Get cart item by user ID and product ID
     * @param int $user_id User ID
     * @param int $product_id Product ID
     * @return Cart|false Cart object or false on failure
     */
    public function getCartItemByUserAndProduct($user_id, $product_id)
    {
        try {
            $query = $this->db->prepare('SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
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
            error_log("Error getting cart item by user and product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all cart items for a user
     * @param int $user_id User ID
     * @return array|false Array of Cart objects or false on failure
     */
    public function getCartByUser($user_id)
    {
        try {
            $query = $this->db->prepare('SELECT * FROM cart WHERE user_id = :user_id');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error getting cart by user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get detailed cart items with product information for a user
     * @param int $user_id User ID
     * @return array|false Array of cart items with product details or false on failure
     */
    public function getDetailedCartByUser($user_id)
    {
        try {
            $query = $this->db->prepare('SELECT c.*, p.name, p.price, p.image, p.stock 
                                         FROM cart c JOIN products p ON c.product_id = p.id
                                         WHERE c.user_id = :user_id ORDER BY c.date_added DESC');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Error getting detailed cart by user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate total items in cart for a user
     * @param int $user_id User ID
     * @return int Total items in cart
     */
    public function getTotalItems($user_id)
    {
        try {
            $query = $this->db->prepare('SELECT SUM(quantity) as total_items FROM cart WHERE user_id = :user_id');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total_items'] ?: 0; // Return 0 if no items found

        } catch (\PDOException $e) {
            error_log("Error calculating total items in cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate total price of items in cart for a user
     * @param int $user_id User ID
     * @return float Total price of items in cart
     */
    public function getTotalCost($user_id)
    {
        try {
            $query = $this->db->prepare('SELECT SUM(c.quantity * p.price) as total 
                                         FROM cart c JOIN products p ON c.product_id = p.id
                                         WHERE c.user_id = :user_id');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?: 0;
        } catch (\PDOException $e) {
            error_log("Error calculating cart total cost: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if product is already in cart
     * @param int $user_id User ID
     * @param int $product_id Product ID
     * @return bool true if product is in cart, false otherwise
     */
    public function isProductInCart($user_id, $product_id)
    {
        try {
            $query = $this->db->prepare('SELECT COUNT(*) as count FROM cart WHERE user_id = :user_id AND product_id = :product_id');
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (\PDOException $e) {
            error_log("Error checking if product is in cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean old cart items (older than specified days)
     * @param int $days Number of days
     * @return bool true on success, false on failure
     */
    public function cleanOldCartItems($days = 30)
    {
        try {
            $date = new DateTime();
            $date->modify("-$days days");
            $formattedDate = $date->format('Y-m-d H:i:s');

            $delete = $this->db->prepare('DELETE FROM cart WHERE date_added < :date');
            $delete->bindParam(':date', $formattedDate, PDO::PARAM_STR);
            return $delete->execute();
        } catch (\PDOException $e) {
            error_log("Error cleaning old cart items: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the product associated with this cart item
     * @return Product|false Product object or false on failure
     */
    public function getProduct()
    {
        try {
            $product = new Product();
            return $product->getProductById($this->product_id);
        } catch (\PDOException $e) {
            error_log("Error getting product for cart item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate the subtotal for this cart item (price * quantity)
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
     * Format total cost with currency symbol
     * @param int $user_id User ID
     * @return string|false Formatted total cost or false on failure
     */
    public function formatTotalCost($user_id)
    {
        $total = $this->getTotalCost($user_id);
        if ($total !== false) {
            return number_format($total, 2, ',', '.') . ' €';
        }
        return false;
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

    /**
     * Verify if all products in cart have enough stock
     * @param int $user_id User ID
     * @return bool|array true if all have stock, array of problematic products otherwise
     */
    public function verifyStock($user_id)
    {
        try {
            $cartItems = $this->getDetailedCartByUser($user_id);


            if (!$cartItems) {
                return true; // No items in cart
            }

            $problematicProducts = [];

            foreach ($cartItems as $item) {
                if ($item['quantity'] < $item['stock']) {
                    $problematicProducts[] = [
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'available_stock' => $item['stock'],
                        'requested_quantity' => $item['quantity']
                    ];
                }
            }

            return empty($problematicProducts) ? true : $problematicProducts;
        } catch (\PDOException $e) {
            error_log("Error verifying stock: " . $e->getMessage());
            return false;
        }
    }
}
