<?php

namespace Controllers;

require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/BaseController.php';

/**
 * CartController
 * Handles shopping cart operations including adding, updating, and removing items
 */
class CartController extends BaseController
{
    
    // ========================================
    // CART DISPLAY METHODS
    // ========================================

    /**
     * Display cart contents
     */
    public function index()
    {
        $this->requireLogin();
        
        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        
        // Get detailed cart items with product information
        $cartItems = $cart->getDetailedCartByUser($currentUser['id']);
        
        // Calculate totals
        $totalItems = $cart->getTotalItems($currentUser['id']);
        $totalCost = $cart->getTotalCost($currentUser['id']);
        
        // Update session cart count
        $_SESSION['cart_count'] = $totalItems;
        
        $viewData = [
            'cartItems' => $cartItems,
            'totalItems' => $totalItems,
            'totalCost' => $totalCost,
            'formattedTotal' => $cart->formatTotalCost($currentUser['id'])
        ];
        
        $this->loadView('cart/index', $viewData);
    }

    // ========================================
    // CART MANIPULATION METHODS
    // ========================================

    /**
     * Add product to cart
     */
    public function addToCart()
    {
        $this->requireLogin();
        
        if (!$this->isPost()) {
            $this->setErrorMessage('Invalid request method');
            $this->goBack();
            return;
        }

        $postData = $this->getPostData();
        $productId = intval($postData['product_id'] ?? 0);
        $quantity = intval($postData['quantity'] ?? 1);
        
        if (!$productId || $quantity <= 0) {
            $this->setErrorMessage('Datos de producto inválidos');
            $this->goBack();
            return;
        }

        // Verify product exists and has stock
        $product = new \Models\Product();
        if (!$product->getProductById($productId)) {
            $this->setErrorMessage('Producto no encontrado');
            $this->goBack();
            return;
        }

        if ($product->getStock() < $quantity) {
            $this->setErrorMessage('Stock insuficiente');
            $this->goBack();
            return;
        }

        // Add to cart
        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        $cart->setUserId($currentUser['id']);
        $cart->setProductId($productId);
        $cart->setQuantity($quantity);

        $added = $cart->addToCart();

        if ($added) {
            // Update session cart count
            $this->updateCartCount();
            $this->setSuccessMessage('Producto añadido al carrito');
        } else {
            $this->setErrorMessage('Error al añadir producto al carrito');
        }

        $this->goBack();
    }

    /**
     * Update item quantity in cart
     */
    public function updateQuantity()
    {
        $this->requireLogin();
        
        if (!$this->isPost()) {
            $this->setErrorMessage('Invalid request method');
            $this->redirect('cart', 'index');
            return;
        }

        $postData = $this->getPostData();
        $cartItemId = intval($postData['cart_item_id'] ?? 0);
        $quantity = intval($postData['quantity'] ?? 0);
        
        if (!$cartItemId) {
            $this->setErrorMessage('ID de elemento inválido');
            $this->redirect('cart', 'index');
            return;
        }

        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        
        // Get cart item and verify ownership
        $cartItem = $cart->getCartItemById($cartItemId);
        if (!$cartItem || $cartItem->getUserId() != $currentUser['id']) {
            $this->setErrorMessage('Elemento del carrito no encontrado');
            $this->redirect('cart', 'index');
            return;
        }

        // If quantity is 0 or negative, remove item
        if ($quantity <= 0) {
            $removed = $cartItem->removeFromCart();
            if ($removed) {
                $this->updateCartCount();
                $this->setSuccessMessage('Producto eliminado del carrito');
            } else {
                $this->setErrorMessage('Error al eliminar producto');
            }
            $this->redirect('cart', 'index');
            return;
        }

        // Verify stock availability
        $product = new \Models\Product();
        if ($product->getProductById($cartItem->getProductId())) {
            if ($product->getStock() < $quantity) {
                $this->setErrorMessage('Stock insuficiente. Disponible: ' . $product->getStock());
                $this->redirect('cart', 'index');
                return;
            }
        }

        // Update quantity
        $updated = $cartItem->updateQuantity($quantity);

        if ($updated) {
            $this->updateCartCount();
            $this->setSuccessMessage('Cantidad actualizada');
        } else {
            $this->setErrorMessage('Error al actualizar cantidad');
        }

        $this->redirect('cart', 'index');
    }

    /**
     * Remove specific item from cart
     */
    public function removeItem()
    {
        $this->requireLogin();
        
        if (!$this->isPost()) {
            $this->setErrorMessage('Invalid request method');
            $this->redirect('cart', 'index');
            return;
        }

        $postData = $this->getPostData();
        $cartItemId = intval($postData['cart_item_id'] ?? 0);
        
        if (!$cartItemId) {
            $this->setErrorMessage('ID de elemento inválido');
            $this->redirect('cart', 'index');
            return;
        }

        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        
        // Get cart item and verify ownership
        $cartItem = $cart->getCartItemById($cartItemId);
        if (!$cartItem || $cartItem->getUserId() != $currentUser['id']) {
            $this->setErrorMessage('Elemento del carrito no encontrado');
            $this->redirect('cart', 'index');
            return;
        }

        // Remove item
        $removed = $cartItem->removeFromCart();

        if ($removed) {
            $this->updateCartCount();
            $this->setSuccessMessage('Producto eliminado del carrito');
        } else {
            $this->setErrorMessage('Error al eliminar producto');
        }

        $this->redirect('cart', 'index');
    }

    /**
     * Clear entire cart
     */
    public function clearCart()
    {
        $this->requireLogin();
        
        if (!$this->isPost()) {
            $this->setErrorMessage('Invalid request method');
            $this->redirect('cart', 'index');
            return;
        }

        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        
        $cleared = $cart->clearCart($currentUser['id']);

        if ($cleared) {
            // Update session cart count
            $_SESSION['cart_count'] = 0;
            $this->setSuccessMessage('Carrito vaciado');
        } else {
            $this->setErrorMessage('Error al vaciar el carrito');
        }

        $this->redirect('cart', 'index');
    }

    // ========================================
    // AJAX METHODS (for dynamic updates)
    // ========================================

    /**
     * Add to cart via AJAX (for product catalog buttons)
     */
    public function ajaxAddToCart()
    {
        $this->requireLogin();
        
        // Set content type for JSON response
        header('Content-Type: application/json');
        
        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $postData = $this->getPostData();
        $productId = intval($postData['product_id'] ?? 0);
        $quantity = intval($postData['quantity'] ?? 1);
        
        if (!$productId || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos de producto inválidos']);
            return;
        }

        // Verify product exists and has stock
        $product = new \Models\Product();
        if (!$product->getProductById($productId)) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        if ($product->getStock() < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
            return;
        }

        // Add to cart
        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        $cart->setUserId($currentUser['id']);
        $cart->setProductId($productId);
        $cart->setQuantity($quantity);

        $added = $cart->addToCart();

        if ($added) {
            // Get updated cart count
            $totalItems = $cart->getTotalItems($currentUser['id']);
            $_SESSION['cart_count'] = $totalItems;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Producto añadido al carrito',
                'cartCount' => $totalItems
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al añadir producto al carrito']);
        }
    }

    /**
     * Get cart count via AJAX
     */
    public function getCartCount()
    {
        header('Content-Type: application/json');
        
        if (!$this->checkUserSession()) {
            echo json_encode(['success' => false, 'count' => 0]);
            return;
        }

        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        $totalItems = $cart->getTotalItems($currentUser['id']);
        
        echo json_encode(['success' => true, 'count' => $totalItems]);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Update cart count in session
     */
    private function updateCartCount()
    {
        if ($this->checkUserSession()) {
            $currentUser = $this->getCurrentUser();
            $cart = new \Models\Cart();
            $totalItems = $cart->getTotalItems($currentUser['id']);
            $_SESSION['cart_count'] = $totalItems;
        }
    }

    /**
     * Verify cart stock before checkout (helper for future checkout process)
     */
    public function verifyCartStock()
    {
        $this->requireLogin();
        
        $currentUser = $this->getCurrentUser();
        $cart = new \Models\Cart();
        
        $stockProblems = $cart->verifyStock($currentUser['id']);
        
        if ($stockProblems === true) {
            // All items have sufficient stock
            return true;
        } elseif (is_array($stockProblems)) {
            // There are stock problems
            $problemMessages = [];
            foreach ($stockProblems as $problem) {
                $problemMessages[] = "'{$problem['product_name']}': solicitados {$problem['requested_quantity']}, disponibles {$problem['available_stock']}";
            }
            
            $this->setErrorMessage('Problemas de stock: ' . implode(', ', $problemMessages));
            return false;
        }
        
        // Error occurred
        $this->setErrorMessage('Error al verificar el stock');
        return false;
    }
}

?>