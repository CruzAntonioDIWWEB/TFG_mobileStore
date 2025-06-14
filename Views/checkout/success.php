<?php
// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ' . BASE_URL . 'index.php?controller=user&action=login');
    exit;
}

$orderId = $orderId ?? null;
?>

<!-- Order Success Section -->
<section class="order-success-section">
    <div class="success-container">
        
        <!-- Success Content -->
        <div class="success-content">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="success-title">¡Pedido Realizado con Éxito!</h1>
            
            <div class="success-details">
                <?php if ($orderId): ?>
                    <p class="order-number">
                        <strong>Número de Pedido: #<?php echo htmlspecialchars($orderId); ?></strong>
                    </p>
                <?php endif; ?>
                
                <p class="success-message">
                    Tu pedido ha sido procesado correctamente y el pago se ha completado. 
                    En breve recibirás un email de confirmación con todos los detalles.
                </p>
                
                <div class="next-steps">
                    <h3>¿Qué sigue ahora?</h3>
                    <ul>
                        <li><i class="fas fa-envelope"></i> Recibirás un email de confirmación</li>
                        <li><i class="fas fa-box"></i> Prepararemos tu pedido para el envío</li>
                        <li><i class="fas fa-truck"></i> Te notificaremos cuando esté en camino</li>
                        <li><i class="fas fa-home"></i> Recibirás tu pedido en la dirección indicada</li>
                    </ul>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="success-actions">
                <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=orderHistory" class="action-btn primary">
                    <i class="fas fa-history"></i>
                    Ver Mis Pedidos
                </a>
                
                <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="action-btn secondary">
                    <i class="fas fa-home"></i>
                    Volver al Inicio
                </a>
                
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="action-btn secondary">
                    <i class="fas fa-shopping-cart"></i>
                    Seguir Comprando
                </a>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="contact-info">
            <h3>¿Necesitas Ayuda?</h3>
            <p>Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos:</p>
            <div class="contact-methods">
                <a href="<?php echo BASE_URL; ?>index.php?controller=contact&action=index" class="contact-link">
                    <i class="fas fa-envelope"></i>
                    Contactar Soporte
                </a>
            </div>
        </div>
    </div>
</section>

<script>
// Clear any remaining cart data
document.addEventListener('DOMContentLoaded', function() {
    // Make sure cart is cleared from localStorage
    localStorage.removeItem('mobilestore_cart');
    
    // Update cart count in header if it exists
    const cartCounts = document.querySelectorAll('.cart-count, .mobile-cart-count');
    cartCounts.forEach(element => {
        element.style.display = 'none';
    });
    
    // Update localStorage cart count
    if (window.cartStorage) {
        window.cartStorage.updateCount(0);
    }
});
</script>