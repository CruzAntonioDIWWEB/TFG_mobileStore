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
        
        <!-- Header -->
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

            <!-- Shipping Form -->
            <div class="shipping-section">
                <h2 class="section-title">
                    <i class="fas fa-truck"></i>
                    Información de Envío
                </h2>
                
                <form id="shippingForm" class="shipping-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="province">Provincia *</label>
                            <input type="text" id="province" name="province" required>
                        </div>
                        <div class="form-group">
                            <label for="locality">Localidad *</label>
                            <input type="text" id="locality" name="locality" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Dirección Completa *</label>
                        <input type="text" id="address" name="address" placeholder="Calle, número, piso, puerta..." required>
                    </div>
                </form>
            </div>

            <!-- Payment Section -->
            <div class="payment-section">
                <h2 class="section-title">
                    <i class="fas fa-lock"></i>
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

<script>
// Checkout functionality
document.addEventListener('DOMContentLoaded', function() {
    loadCartSummary();
    initializePayPal();
});

/**
 * Load cart summary from localStorage
 */
function loadCartSummary() {
    const cartData = getCartFromLocalStorage();
    const summaryContainer = document.getElementById('cartSummary');
    
    if (!cartData || !cartData.items || cartData.items.length === 0) {
        summaryContainer.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Tu carrito está vacío</p>
                <a href="${BASE_URL}index.php?controller=product&action=phones" class="shop-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Ir a Comprar
                </a>
            </div>
        `;
        return;
    }

    let itemsHtml = '';
    cartData.items.forEach(item => {
        const itemTotal = (item.price * item.quantity).toFixed(2);
        itemsHtml += `
            <div class="summary-item">
                <div class="item-info">
                    <span class="item-name">${item.product_name || item.name}</span>
                    <span class="item-quantity">x${item.quantity}</span>
                </div>
                <span class="item-total">€${itemTotal}</span>
            </div>
        `;
    });

    summaryContainer.innerHTML = `
        <div class="summary-items">
            ${itemsHtml}
        </div>
        <div class="summary-divider"></div>
        <div class="summary-total">
            <div class="total-row">
                <span class="total-label">Total (${cartData.totalItems} productos)</span>
                <span class="total-amount">€${cartData.totalCost.toFixed(2)}</span>
            </div>
        </div>
    `;
}

/**
 * Get cart data from localStorage
 */
function getCartFromLocalStorage() {
    try {
        const cartData = localStorage.getItem('mobilestore_cart');
        return cartData ? JSON.parse(cartData) : null;
    } catch (error) {
        console.error('Error getting cart data:', error);
        return null;
    }
}

/**
 * Get shipping form data
 */
function getShippingFormData() {
    return {
        province: document.getElementById('province').value.trim(),
        locality: document.getElementById('locality').value.trim(),
        address: document.getElementById('address').value.trim()
    };
}

/**
 * Validate shipping form
 */
function validateShippingForm() {
    const shipping = getShippingFormData();
    
    if (!shipping.province || !shipping.locality || !shipping.address) {
        alert('Por favor, completa todos los campos de envío');
        return false;
    }
    
    if (shipping.address.length < 10) {
        alert('Por favor, proporciona una dirección más específica');
        return false;
    }
    
    return true;
}

/**
 * Initialize PayPal button
 */
function initializePayPal() {

    paypal.Buttons({
        style: {
            color: 'blue',
            shape: 'pill',
            label: 'pay',
            height: 45
        },
        
        createOrder: function(data, actions) {
            // Validate shipping form before creating order
            const carritoProductos = JSON.parse(localStorage.getItem('mobilestore_cart')) || [];
            

            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: carritoProductos.totalCost.toFixed(2),
                        currency_code: 'EUR',
                        breakdown: {
                            item_total: {
                                value: carritoProductos.totalCost.toFixed(2),
                                currency_code: 'EUR'
                            },
                        },
                    },
                    items: carritoProductos.items.map(producto => ({
                            name: producto.product_name,
                            unit_amount: {
                                value: producto.price.toFixed(2),
                                currency_code: 'EUR'
                            },
                            quantity: producto.quantity.toString()
                        }))
                }]
            });
        },
        
onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
        console.log(details);
        
        // Clear cart from localStorage
        localStorage.removeItem('mobilestore_cart');
        
        // Update cart count in header if cartStorage exists
        if (window.cartStorage) {
            window.cartStorage.updateCount(0);
        }
        
        // Update cart count badges
        const cartCounts = document.querySelectorAll('.cart-count, .mobile-cart-count');
        cartCounts.forEach(element => {
            element.style.display = 'none';
        });
        
        // Replace checkout section with styled success message
        document.querySelector('.checkout-section').innerHTML = `
            <section class="order-success-section">
                <div class="success-container">
                    <div class="success-content">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        
                        <h1 class="success-title">¡Pago realizado con éxito!</h1>
                        
                        <div class="success-message">
                            <p>Tu pedido ha sido procesado correctamente.</p>
                            <p>En breve recibirás un email de confirmación con todos los detalles.</p>
                            
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
                        
                        <div class="success-actions">
                            <a href="${BASE_URL}index.php?controller=home&action=index" class="action-btn primary">
                                <i class="fas fa-home"></i>
                                Volver al Inicio
                            </a>
                            
                            <a href="${BASE_URL}index.php?controller=product&action=phones" class="action-btn secondary">
                                <i class="fas fa-shopping-cart"></i>
                                Seguir Comprando
                            </a>
                        </div>
                    </div>
                
                </div>
            </section>
        `;

        // Scroll to the top of the page
        const successSection = document.querySelector('.order-success-section');
        if (successSection) {
            successSection.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
        

    });
},
        
        onCancel: function(data) {
            console.log('Payment cancelled:', data);
            alert('Pago cancelado. Puedes intentarlo de nuevo cuando gustes.');
        },
        
        onError: function(err) {
            console.error('PayPal error:', err);
            alert('Error en el procesamiento del pago. Por favor, inténtalo de nuevo.');
        }
        
    }).render('#paypal-button-container');
}


const BASE_URL = '<?php echo BASE_URL; ?>';
</script>