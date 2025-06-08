<?php
// Get messages for display
$messages = $messages ?? [];
$cartItems = $cartItems ?? [];
$totalItems = $totalItems ?? 0;
$totalCost = $totalCost ?? 0;
$formattedTotal = $formattedTotal ?? '0,00 €';
?>

<!-- Messages Display -->
<?php if (!empty($messages['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($messages['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($messages['error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?php echo htmlspecialchars($messages['error']); ?>
    </div>
<?php endif; ?>

<!-- Shopping Cart Section -->
<section class="cart-section">
    <div class="cart-container">
        
        <!-- Cart Header -->
        <div class="cart-header">
            <div class="cart-title-section">
                <h1 class="cart-title">
                    <i class="fas fa-shopping-cart"></i>
                    Mi carrito de compras
                </h1>
                <p class="cart-subtitle">
                    <?php if ($totalItems > 0): ?>
                        Tienes <?php echo $totalItems; ?> <?php echo $totalItems === 1 ? 'producto' : 'productos'; ?> en tu carrito
                    <?php else: ?>
                        Tu carrito está vacío
                    <?php endif; ?>
                </p>
            </div>
            
            <?php if (!empty($cartItems)): ?>
                <div class="cart-actions-header">
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=clearCart" class="clear-cart-form">
                        <button type="button" class="cart-clear-btn" onclick="confirmClearCart()">
                            <i class="fas fa-trash-alt"></i>
                            Vaciar carrito
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($cartItems)): ?>
            <!-- Cart Content -->
            <div class="cart-content">
                
                <!-- Cart Items List -->
                <div class="cart-items-section">
                    <div class="cart-items-list">
                        <?php foreach ($cartItems as $item): ?>
                            <article class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                                <div class="item-image">
                                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $item['product_id']; ?>">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($item['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 class="product-image">
                                        <?php else: ?>
                                            <img src="/dashboard/TFG/assets/img/placeholder-product.jpg" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 class="product-image">
                                        <?php endif; ?>
                                    </a>
                                    
                                    <!-- Stock indicator -->
                                    <?php if ($item['stock'] <= 0): ?>
                                        <div class="stock-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Sin stock
                                        </div>
                                    <?php elseif ($item['stock'] < $item['quantity']): ?>
                                        <div class="stock-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Stock limitado
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-details">
                                    <div class="item-info">
                                        <h3 class="item-name">
                                            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $item['product_id']; ?>">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </a>
                                        </h3>
                                        
                                        <div class="item-meta">
                                            <span class="item-price">
                                                <?php echo number_format($item['price'], 2, ',', '.'); ?> €
                                            </span>
                                            
                                            <?php if ($item['stock'] > 0): ?>
                                                <span class="item-availability available">
                                                    <i class="fas fa-check"></i>
                                                    Disponible
                                                </span>
                                            <?php else: ?>
                                                <span class="item-availability unavailable">
                                                    <i class="fas fa-times"></i>
                                                    No disponible
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="item-controls">
                                        <!-- Quantity Controls -->
                                        <div class="quantity-section">
                                            <label class="quantity-label">Cantidad:</label>
                                            <form class="quantity-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=updateQuantity">
                                                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                                
                                                <div class="quantity-controls">
                                                    <button type="button" class="quantity-btn quantity-decrease" 
                                                            onclick="updateQuantity(<?php echo $item['id']; ?>, -1, <?php echo $item['stock']; ?>)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    
                                                    <input type="number" 
                                                           name="quantity" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="0" 
                                                           max="<?php echo $item['stock']; ?>" 
                                                           class="quantity-input"
                                                           id="quantity-<?php echo $item['id']; ?>"
                                                           onchange="submitQuantityForm(<?php echo $item['id']; ?>)">
                                                    
                                                    <button type="button" class="quantity-btn quantity-increase" 
                                                            onclick="updateQuantity(<?php echo $item['id']; ?>, 1, <?php echo $item['stock']; ?>)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        
                                        <!-- Remove Item -->
                                        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=removeItem" class="remove-form">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                            <button type="button" class="remove-btn" onclick="confirmRemoveItem(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="item-subtotal">
                                    <span class="subtotal-label">Subtotal:</span>
                                    <span class="subtotal-amount">
                                        <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?> €
                                    </span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="cart-summary-section">
                    <div class="cart-summary">
                        <div class="summary-header">
                            <h2 class="summary-title">
                                <i class="fas fa-calculator"></i>
                                Resumen del pedido
                            </h2>
                        </div>
                        
                        <div class="summary-content">
                            <div class="summary-row">
                                <span class="summary-label">Productos (<?php echo $totalItems; ?>):</span>
                                <span class="summary-value"><?php echo $formattedTotal; ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span class="summary-label">Envío:</span>
                                <span class="summary-value free">Gratis</span>
                            </div>
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-row total-row">
                                <span class="summary-label">Total:</span>
                                <span class="summary-value total"><?php echo $formattedTotal; ?></span>
                            </div>
                        </div>
                        
                        <div class="summary-actions">
                            <button class="checkout-btn" onclick="proceedToCheckout()">
                                <i class="fas fa-credit-card"></i>
                                Proceder al pago
                            </button>
                            
                            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="continue-shopping-btn">
                                <i class="fas fa-arrow-left"></i>
                                Seguir comprando
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
            
        <?php else: ?>
            <!-- Empty Cart -->
            <div class="empty-cart">
                <div class="empty-cart-content">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    
                    <h2 class="empty-cart-title">Tu carrito está vacío</h2>
                    <p class="empty-cart-text">
                        Parece que aún no has añadido ningún producto a tu carrito.
                        Explora nuestro catálogo y encuentra los mejores móviles y accesorios.
                    </p>
                    
                    <div class="empty-cart-actions">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="shop-phones-btn">
                            <i class="fas fa-mobile-alt"></i>
                            Ver móviles
                        </a>
                        
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=accessories" class="shop-accessories-btn">
                            <i class="fas fa-headphones"></i>
                            Ver accesorios
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Confirmation Modals -->
<div id="remove-item-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar eliminación</h3>
            <button type="button" class="modal-close" onclick="closeModal('remove-item-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <p id="remove-item-text">¿Estás seguro de que quieres eliminar este producto del carrito?</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-btn btn-cancel" onclick="closeModal('remove-item-modal')">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <button type="button" class="modal-btn btn-confirm" id="confirm-remove-btn">
                <i class="fas fa-check"></i>
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

<div id="clear-cart-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Vaciar carrito</h3>
            <button type="button" class="modal-close" onclick="closeModal('clear-cart-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p>¿Estás seguro de que quieres vaciar todo el carrito?</p>
            <p class="modal-subtitle">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-btn btn-cancel" onclick="closeModal('clear-cart-modal')">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=clearCart" style="display: inline;">
                <button type="submit" class="modal-btn btn-confirm">
                    <i class="fas fa-check"></i>
                    Sí, vaciar carrito
                </button>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo ASSETS_URL; ?>js/cartFunctionality.js"></script>
<script>
// Sync cart data to localStorage when cart page loads
document.addEventListener('DOMContentLoaded', function() {
    if (window.cartStorage) {
        // Auto-sync cart data from the current page
        window.cartStorage.autoSync();
        
        // Listen for form submissions to update localStorage
        
        // When quantity is updated
        const quantityForms = document.querySelectorAll('form[action*="updateQuantity"]');
        quantityForms.forEach(form => {
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    window.cartStorage.autoSync();
                }, 500); // Small delay to let the page reload
            });
        });
        
        // When item is removed
        const removeForms = document.querySelectorAll('form[action*="removeItem"]');
        removeForms.forEach(form => {
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    window.cartStorage.autoSync();
                }, 500);
            });
        });
        
        // When cart is cleared
        const clearForm = document.querySelector('form[action*="clearCart"]');
        if (clearForm) {
            clearForm.addEventListener('submit', function() {
                window.cartStorage.clear();
            });
        }
    }
});
</script>