<?php

namespace Controllers;

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/BaseController.php';

use Exception;
use PDO;

/**
 * CheckoutController
 * Handles checkout process and PayPal payment integration
 */
class CheckoutController extends BaseController
{
    
    /**
     * Display checkout page
     */
    public function index(){
        // Require user to be logged in
        $this->requireLogin();
        
        // Load the checkout view
        $this->loadView('checkout/index');
    }

    /**
     * Process PayPal payment and create order
     */
    public function processPayment(){
        // Require user to be logged in
        $this->requireLogin();
        
        header('Content-Type: application/json');
        
        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            // Get JSON data from request
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }

            // Get current user
            $currentUser = $this->getCurrentUser();
            
            // Validate required data
            if (!isset($data['paypalDetails']) || !isset($data['cartData']) || !isset($data['shippingInfo'])) {
                throw new Exception('Missing required data');
            }

            $paypalDetails = $data['paypalDetails'];
            $cartData = $data['cartData'];
            $shippingInfo = $data['shippingInfo'];

            // Validate shipping info
            if (empty($shippingInfo['province']) || empty($shippingInfo['locality']) || empty($shippingInfo['address'])) {
                throw new Exception('Información de envío incompleta');
            }

            // Validate cart data
            if (empty($cartData['items']) || $cartData['totalCost'] <= 0) {
                throw new Exception('Carrito vacío o inválido');
            }

            // Create order from cart data
            $orderId = $this->createOrderFromCart($currentUser['id'], $cartData, $shippingInfo, $paypalDetails);
            
            if ($orderId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Pedido creado exitosamente',
                    'orderId' => $orderId
                ]);
            } else {
                throw new Exception('Error al crear el pedido');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create order from localStorage cart data
     */
    private function createOrderFromCart($userId, $cartData, $shippingInfo, $paypalDetails) {
        try {
            // Create new order
            $order = new \Models\Order();
            $order->setUserId($userId);
            $order->setProvince($shippingInfo['province']);
            $order->setLocality($shippingInfo['locality']);
            $order->setAddress($shippingInfo['address']);
            $order->setCost($cartData['totalCost']);
            $order->setStatus('paid'); // PayPal payment already completed
            $order->setDate(date('Y-m-d'));
            $order->setTime(date('H:i:s'));

            // Save order
            if (!$order->saveDB()) {
                throw new Exception('Error saving order');
            }

            // Create order items and update stock
            foreach ($cartData['items'] as $item) {
                // Find product by name (since localStorage might not have product_id)
                $product = new \Models\Product();
                $productData = $this->findProductByName($item['product_name'] ?? $item['name']);
                
                if (!$productData) {
                    throw new Exception('Producto no encontrado: ' . ($item['product_name'] ?? $item['name']));
                }

                // Verify stock
                if ($productData['stock'] < $item['quantity']) {
                    throw new Exception('Stock insuficiente para: ' . $productData['name']);
                }

                // Create order item
                $orderItem = new \Models\OrderItem();
                $orderItem->setOrderId($order->getId());
                $orderItem->setProductId($productData['id']);
                $orderItem->setQuantity($item['quantity']);
                
                if (!$orderItem->save()) {
                    throw new Exception('Error saving order item');
                }

                // Update product stock
                $product->getProductById($productData['id']);
                $newStock = $productData['stock'] - $item['quantity'];
                $product->setStock($newStock);
                $product->updateDB();
            }

            return $order->getId();

        } catch (Exception $e) {
            error_log("Error creating order from cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find product by name (helper method)
     */
    private function findProductByName($productName) {
        try {
            require_once __DIR__ . '/../config/config.php';
            global $pdo;
            
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :name LIMIT 1");
            $stmt->bindValue(':name', '%' . $productName . '%', PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error finding product by name: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Order success page
     */
    public function success(){
        $this->requireLogin();
        
        $orderId = $this->getGetData('orderId');
        
        $viewData = [
            'orderId' => $orderId
        ];
        
        $this->loadView('checkout/success', $viewData);
    }
}

?>