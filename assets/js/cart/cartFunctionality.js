document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // QUANTITY CONTROLS
    // ========================================
    
    /**
     * Update quantity controls
     * @param {number} cartItemId - Cart item ID
     * @param {number} change - Change amount (+1 or -1)
     * @param {number} maxStock - Maximum available stock
     */
    window.updateQuantity = function(cartItemId, change, maxStock) {
        const quantityInput = document.getElementById(`quantity-${cartItemId}`);
        if (!quantityInput) return;
        
        let currentQuantity = parseInt(quantityInput.value) || 1;
        let newQuantity = currentQuantity + change;
        
        // Ensure quantity is within valid range
        if (newQuantity < 1) {
            newQuantity = 1;
        } else if (newQuantity > maxStock) {
            newQuantity = maxStock;
            showToast('No hay suficiente stock disponible', 'warning');
            return;
        }
        
        quantityInput.value = newQuantity;
        
        // Auto-submit the form after a short delay
        setTimeout(() => {
            submitQuantityForm(cartItemId);
        }, 500);
    };
    
    /**
     * Submit quantity form
     * @param {number} cartItemId - Cart item ID
     */
    window.submitQuantityForm = function(cartItemId) {
        const quantityInput = document.getElementById(`quantity-${cartItemId}`);
        const form = quantityInput.closest('.quantity-form');
        
        if (form && quantityInput.value > 0) {
            form.submit();
        } else if (quantityInput.value <= 0) {
            // If quantity is 0, ask for confirmation to remove
            confirmRemoveItem(cartItemId, 'este producto');
        }
    };
    
    // ========================================
    // MODAL FUNCTIONS
    // ========================================
    
    /**
     * Show confirmation modal for removing item
     * @param {number} cartItemId - Cart item ID
     * @param {string} productName - Product name
     */
    window.confirmRemoveItem = function(cartItemId, productName) {
        const modal = document.getElementById('remove-item-modal');
        const confirmBtn = document.getElementById('confirm-remove-btn');
        const textElement = document.getElementById('remove-item-text');
        
        if (modal && confirmBtn && textElement) {
            textElement.textContent = `¿Estás seguro de que quieres eliminar "${productName}" del carrito?`;
            
            // Remove any existing event listeners
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
            
            // Add new event listener
            newConfirmBtn.addEventListener('click', function() {
                removeCartItem(cartItemId);
            });
            
            showModal('remove-item-modal');
        }
    };
    
    /**
     * Show confirmation modal for clearing cart
     */
    window.confirmClearCart = function() {
        showModal('clear-cart-modal');
    };
    
    /**
     * Show modal
     * @param {string} modalId - Modal element ID
     */
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Focus trap for accessibility
            const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }
    }
    
    /**
     * Close modal
     * @param {string} modalId - Modal element ID
     */
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };
    
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target.id);
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal-overlay[style*="flex"]');
            if (openModal) {
                closeModal(openModal.id);
            }
        }
    });
    
    // ========================================
    // CART ACTIONS
    // ========================================
    
    /**
     * Remove item from cart
     * @param {number} cartItemId - Cart item ID
     */
    function removeCartItem(cartItemId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=cart&action=removeItem';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cart_item_id';
        input.value = cartItemId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
    
    // ========================================
    // UTILITY FUNCTIONS
    // ========================================
    
    /**
     * Show toast notification
     * @param {string} message - Message to show
     * @param {string} type - Type of toast (success, error, warning, info)
     */
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${getToastIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add styles
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${getToastColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            max-width: 300px;
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    /**
     * Get toast icon based on type
     * @param {string} type - Toast type
     * @returns {string} Font Awesome icon class
     */
    function getToastIcon(type) {
        switch (type) {
            case 'success': return 'check-circle';
            case 'error': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            case 'info': 
            default: return 'info-circle';
        }
    }
    
    /**
     * Get toast color based on type
     * @param {string} type - Toast type
     * @returns {string} CSS color value
     */
    function getToastColor(type) {
        switch (type) {
            case 'success': return '#28a745';
            case 'error': return '#dc3545';
            case 'warning': return '#ffc107';
            case 'info': 
            default: return '#17a2b8';
        }
    }
    
    // ========================================
    // FORM ENHANCEMENTS
    // ========================================
    
    // Auto-submit quantity forms when input changes
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        let timeout;
        
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const cartItemId = this.id.replace('quantity-', '');
            
            // Debounce the submission
            timeout = setTimeout(() => {
                if (this.value > 0) {
                    submitQuantityForm(cartItemId);
                }
            }, 1000);
        });
        
        // Prevent form submission on Enter key (let the debounce handle it)
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur(); // Trigger the input event
            }
        });
    });
    
    // ========================================
    // ACCESSIBILITY ENHANCEMENTS
    // ========================================
    
    // Add keyboard navigation for quantity buttons
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    quantityButtons.forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
    
    // Add ARIA labels for better accessibility
    quantityInputs.forEach(input => {
        const cartItemId = input.id.replace('quantity-', '');
        input.setAttribute('aria-label', `Cantidad para el producto en el carrito`);
    });
    
    // ========================================
    // LOADING STATES
    // ========================================
    
    // Add loading state to forms
    const forms = document.querySelectorAll('.quantity-form, .remove-form, .clear-cart-form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            }
        });
    });
    
});

// ========================================
// CART SUMMARY UPDATES (if needed for AJAX)
// ========================================

/**
 * Update cart summary without page reload (for future AJAX implementation)
 * @param {Object} data - Cart data from server
 */
function updateCartSummary(data) {
    const totalItemsElements = document.querySelectorAll('.cart-subtitle, .summary-label');
    const totalCostElements = document.querySelectorAll('.summary-value.total');
    
    if (data.totalItems !== undefined) {
        totalItemsElements.forEach(element => {
            if (element.classList.contains('cart-subtitle')) {
                element.textContent = data.totalItems > 0 
                    ? `Tienes ${data.totalItems} ${data.totalItems === 1 ? 'producto' : 'productos'} en tu carrito`
                    : 'Tu carrito está vacío';
            }
        });
    }
    
    if (data.formattedTotal) {
        totalCostElements.forEach(element => {
            element.textContent = data.formattedTotal;
        });
    }
}

/**
 * Proceed to checkout
 */
window.proceedToCheckout = function() {
    // Get cart data from localStorage, or sync from page if not found
    let cartData = getCartFromLocalStorage();
    
    if (!cartData || !cartData.items || cartData.items.length === 0) {
        // Force sync from page
        if (window.cartStorage && window.cartStorage.autoSync) {
            window.cartStorage.autoSync();
            cartData = getCartFromLocalStorage();
        }
    }
    
    // Check if cart has items
    if (!cartData || !cartData.items || cartData.items.length === 0) {
        alert('Tu carrito está vacío. Agrega productos antes de continuar.');
        return;
    }
    
    // Redirect to checkout page
    window.location.href = 'index.php?controller=checkout&action=index';
};

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

// Make sure the checkout button in cart works
document.addEventListener('DOMContentLoaded', function() {
    // Find checkout button and add event listener if it doesn't have onclick
    const checkoutBtns = document.querySelectorAll('.checkout-btn, .btn-checkout');
    checkoutBtns.forEach(btn => {
        if (!btn.onclick) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                proceedToCheckout();
            });
        }
    });
});