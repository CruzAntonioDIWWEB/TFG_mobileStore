<?php

namespace Controllers;

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/OrderItem.php';
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
    public function index()
    {
        $this->requireLogin();

        // Load the checkout view
        $this->loadView('checkout/index');
    }

    /**
     * Process PayPal payment and create order
     */
    public function processPayment()
    {
        // Clean any output buffer to prevent HTML errors -_-
        ob_clean();

        $this->requireLogin();

        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
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
            error_log("Error in processPayment: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Create order from localStorage cart data - FIXED VERSION
     */
    private function createOrderFromCart($userId, $cartData, $shippingInfo, $paypalDetails)
    {
        try {
            // Log the cart data for debugging
            error_log("Creating order for user $userId with cart data: " . json_encode($cartData));

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
                throw new Exception('Error saving order to database');
            }

            $orderId = $order->getId();
            error_log("Order created with ID: $orderId");

            // Create order items and update stock
            foreach ($cartData['items'] as $item) {
                // Use multiple possible field names for product identification
                $productName = $item['product_name'] ?? $item['name'] ?? $item['title'] ?? null;
                $productId = $item['product_id'] ?? $item['id'] ?? null;

                error_log("Processing item: " . json_encode($item));

                // Try to find product by ID first, then by name
                $productData = null;
                if ($productId) {
                    $productData = $this->findProductById($productId);
                }

                if (!$productData && $productName) {
                    $productData = $this->findProductByExactName($productName);
                }

                if (!$productData) {
                    throw new Exception('Producto no encontrado: ' . ($productName ?: 'ID: ' . $productId));
                }

                error_log("Found product: " . json_encode($productData));

                // Verify stock
                if ($productData['stock'] < $item['quantity']) {
                    throw new Exception('Stock insuficiente para: ' . $productData['name']);
                }

                // Create order item
                $orderItem = new \Models\OrderItem();
                $orderItem->setOrderId($orderId);
                $orderItem->setProductId($productData['id']);
                $orderItem->setQuantity($item['quantity']);

                if (!$orderItem->save()) {
                    throw new Exception('Error saving order item for product: ' . $productData['name']);
                }

                error_log("Order item created for product ID: " . $productData['id']);

                // Update product stock
                $product = new \Models\Product();
                if ($product->getProductById($productData['id'])) {
                    $newStock = $productData['stock'] - $item['quantity'];
                    $product->setStock($newStock);
                    $product->updateDB();
                    error_log("Updated stock for product ID " . $productData['id'] . " to: $newStock");
                }
            }

            error_log("Order creation completed successfully with ID: $orderId");
            return $orderId;
        } catch (Exception $e) {
            error_log("Error creating order from cart: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Find product by ID
     */
    private function findProductById($productId)
    {
        try {
            require_once __DIR__ . '/../config/config.php';
            global $pdo;

            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error finding product by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find product by EXACT name
     */
    private function findProductByExactName($productName)
    {
        try {
            require_once __DIR__ . '/../config/config.php';
            global $pdo;

            // First try exact match
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name = :name");
            $stmt->bindParam(':name', $productName, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // If no exact match, try case-insensitive match
            if (!$result) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE LOWER(name) = LOWER(:name)");
                $stmt->bindParam(':name', $productName, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // If still no match, try partial match
            if (!$result) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :name LIMIT 1");
                $stmt->bindValue(':name', '%' . $productName . '%', PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error finding product by name: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Order success page
     */
    public function success()
    {
        $this->requireLogin();

        $orderId = $this->getGetData('orderId');

        $viewData = [
            'orderId' => $orderId
        ];

        $this->loadView('checkout/success', $viewData);
    }
}
