<?php
// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ' . BASE_URL . 'index.php?controller=user&action=login');
    exit;
}
?>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="checkout-container">
        <div class="checkout-header">
            <div class="header-content">
                <div class="title-section">
                    <h1 class="checkout-title">
                        <i class="fas fa-credit-card"></i>
                        Finalizar Compra
                    </h1>
                    <p class="checkout-subtitle">Completa tu pedido de forma segura</p>
                </div>
                
                <div class="header-actions">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Carrito
                    </a>
                </div>
            </div>
        </div>

        <!-- Checkout Content -->
        <div class="checkout-content">           
            <!-- Cart Summary -->
            <div class="cart-summary-section">
                <h2 class="section-title">
                    <i class="fas fa-shopping-cart"></i>
                    Resumen del Pedido
                </h2>
                
                <div class="cart-summary-content" id="cartSummary">
                    <div class="loading-cart">
                        <i class="fas fa-spinner fa-spin"></i>
                        Cargando carrito...
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="shipping-section">
                <h2 class="section-title">
                    <i class="fas fa-truck"></i>
                    Información de Envío
                </h2>
                
                <form id="shippingForm" class="shipping-form">
                    <div class="form-group">
                        <label for="province" class="form-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Provincia *
                        </label>
                        <input type="text" id="province" name="province" class="form-input" placeholder="Ej: Madrid" required>
                    </div>

                    <div class="form-group">
                        <label for="locality" class="form-label">
                            <i class="fas fa-city"></i>
                            Localidad *
                        </label>
                        <input type="text" id="locality" name="locality" class="form-input" placeholder="Ej: Madrid" required>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">
                            <i class="fas fa-home"></i>
                            Dirección Completa *
                        </label>
                        <input type="text" id="address" name="address" class="form-input" placeholder="Ej: Calle Gran Vía, 45, 3º B" required>
                        <small class="form-hint">Incluye calle, número, piso y puerta</small>
                    </div>
                </form>
            </div>

            <!-- Payment Section -->
            <div class="payment-section">
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Método de Pago
                </h2>
                
                <div class="payment-content">
                    <div class="payment-info">
                        <p><i class="fab fa-paypal"></i> Pago seguro con PayPal</p>
                        <small>Serás redirigido a PayPal para completar el pago de forma segura</small>
                    </div>
                    
                    <!-- PayPal Button Container -->
                    <div id="paypal-button-container"></div>
                    
                    <!-- Loading State -->
                    <div class="payment-loading" id="paymentLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Procesando pago...
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=AWov7bPkLgUtjI8aY39shrD6o-3sX7YLyaVI1Z9Oyx5KsgOcR46UPVqqZMyPNCvomolmkYar6nWVzeZP&currency=EUR"></script>

<!-- Checkout JavaScript modules -->
<script src="<?php echo ASSETS_URL; ?>js/checkout/checkoutFunctionality.js"></script>
<script src="<?php echo ASSETS_URL; ?>js/checkout/paypalIntegration.js"></script>
<script src="<?php echo ASSETS_URL; ?>js/checkout/emailConfirmation.js"></script>

<script>
// Make BASE_URL available for JavaScript
window.BASE_URL = '<?php echo BASE_URL; ?>';
window.ASSETS_URL = '<?php echo ASSETS_URL; ?>';
</script>