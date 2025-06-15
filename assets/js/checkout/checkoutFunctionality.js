/**
 * Main checkout functionality
 */
document.addEventListener('DOMContentLoaded', function () {
    loadCartSummary();

    // Initial check for PayPal button visibility
    updatePayPalButtonVisibility();

    // Add event listeners to shipping form inputs
    const shippingInputs = document.querySelectorAll('#shippingForm input');
    shippingInputs.forEach(input => {
        input.addEventListener('input', updatePayPalButtonVisibility);
        input.addEventListener('blur', updatePayPalButtonVisibility);
    });
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
                <a href="${window.BASE_URL}index.php?controller=product&action=phones" class="shop-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Ir de Compras
                </a>
            </div>
        `;
        return;
    }

    let summaryHTML = '<div class="cart-items">';

    cartData.items.forEach(item => {
        const imagePath = window.ASSETS_URL ? `${window.ASSETS_URL}img/products/` : '/dashboard/TFG/assets/img/products/';
        summaryHTML += `
            <div class="cart-item-summary">
                <div class="item-info">
                    <img src="${imagePath}${item.image || 'default.jpg'}" alt="${item.product_name || item.name}" class="item-image">
                    <div class="item-details">
                        <h4 class="item-name">${item.product_name || item.name}</h4>
                        <p class="item-quantity">Cantidad: ${item.quantity}</p>
                    </div>
                </div>
                <div class="item-price">
                    <span class="price">€${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            </div>
        `;
    });

    summaryHTML += `
        </div>
        <div class="cart-total">
            <div class="total-line">
                <span class="total-label">Total:</span>
                <span class="total-amount">€${cartData.totalCost.toFixed(2)}</span>
            </div>
        </div>
    `;

    summaryContainer.innerHTML = summaryHTML;
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
 * Check if shipping form is valid (without alerts)
 */
function isShippingFormValid() {
    const shipping = getShippingFormData();
    return shipping.province && shipping.locality && shipping.address && shipping.address.length >= 10;
}

/**
 * Update PayPal button visibility based on form validation
 */
function updatePayPalButtonVisibility() {
    const paypalContainer = document.getElementById('paypal-button-container');

    if (isShippingFormValid()) {
        // Show PayPal button if form is valid
        paypalContainer.style.display = 'block';
        if (!paypalContainer.hasAttribute('data-paypal-initialized')) {
            if (window.initializePayPal) {
                window.initializePayPal();
                paypalContainer.setAttribute('data-paypal-initialized', 'true');
            }
        }
    } else {
        // Hide PayPal button and show message if form is invalid
        paypalContainer.innerHTML = `
            <div class="form-validation-message">
                <i class="fas fa-info-circle"></i>
                <p>Completa todos los campos de envío para continuar con el pago</p>
            </div>
        `;
        paypalContainer.style.display = 'block';
    }
}

/**
 * Get user data from localStorage
 */
function getUserFromLocalStorage() {
    try {
        const userData = localStorage.getItem('mobilestore_user');
        return userData ? JSON.parse(userData) : null;
    } catch (error) {
        console.error('Error getting user data:', error);
        return null;
    }
}

/**
 * Clear cart after successful purchase
 */
function clearCartAfterPurchase() {
    // Clear cart from localStorage
    localStorage.removeItem('mobilestore_cart');

    // Clear cart from database via AJAX (fallback)
    const baseUrl = window.BASE_URL || '/dashboard/TFG/';
    fetch(`${baseUrl}index.php?controller=cart&action=clearCart`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'clear_cart=1'
    }).then(response => {
        console.log('Database cart cleared');
    }).catch(error => {
        console.error('Error clearing database cart:', error);
    });

    // Update cart count in header if cartStorage exists
    if (window.cartStorage) {
        window.cartStorage.updateCount(0);
    }

    // Update cart count badges immediately
    const cartCounts = document.querySelectorAll('.cart-count, .mobile-cart-count');
    cartCounts.forEach(element => {
        element.style.display = 'none';
        element.textContent = '0';
    });
}

// Make all functions globally available
window.getCartFromLocalStorage = getCartFromLocalStorage;
window.getShippingFormData = getShippingFormData;
window.validateShippingForm = validateShippingForm;
window.isShippingFormValid = isShippingFormValid;
window.getUserFromLocalStorage = getUserFromLocalStorage;
window.clearCartAfterPurchase = clearCartAfterPurchase;