// ========================================
// CART LOCALSTORAGE MANAGEMENT
// ========================================

/**
 * Store cart data in localStorage
 * @param {Array} cartItems - Array of cart items with product info
 * @param {number} totalItems - Total number of items
 * @param {number} totalCost - Total cost
 * @param {string} formattedTotal - Formatted total cost
 */
function storeCartData(cartItems, totalItems, totalCost, formattedTotal) {
    try {
        const cartData = {
            items: cartItems || [],
            totalItems: totalItems || 0,
            totalCost: totalCost || 0,
            formattedTotal: formattedTotal || '€0.00',
            lastUpdated: new Date().toISOString()
        };
        
        localStorage.setItem('mobilestore_cart', JSON.stringify(cartData));
        console.log('Cart data stored in localStorage:', cartData.totalItems + ' items');
    } catch (error) {
        console.error('Error storing cart data:', error);
    }
}

/**
 * Get cart data from localStorage
 * @returns {Object|null} Cart data or null if not found
 */
function getCartData() {
    try {
        const cartData = localStorage.getItem('mobilestore_cart');
        return cartData ? JSON.parse(cartData) : null;
    } catch (error) {
        console.error('Error getting cart data:', error);
        return null;
    }
}

/**
 * Clear cart data from localStorage
 */
function clearCartData() {
    try {
        localStorage.removeItem('mobilestore_cart');
        console.log('Cart data cleared from localStorage');
    } catch (error) {
        console.error('Error clearing cart data:', error);
    }
}

/**
 * Update cart count in localStorage when adding items
 * @param {number} newCount - New cart count
 */
function updateCartCount(newCount) {
    try {
        let cartData = getCartData() || { items: [], totalItems: 0, totalCost: 0, formattedTotal: '€0.00' };
        cartData.totalItems = newCount;
        cartData.lastUpdated = new Date().toISOString();
        
        localStorage.setItem('mobilestore_cart', JSON.stringify(cartData));
        console.log('Cart count updated in localStorage:', newCount);
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

/**
 * Display current cart info in console (for debugging)
 */
function showCartInfo() {
    const cart = getCartData();
    if (cart && cart.items && cart.items.length > 0) {
        console.table(cart.items.map(item => ({
            'Producto': item.product_name || item.name,
            'Precio': '€' + (item.price || '0'),
            'Cantidad': item.quantity || 0,
            'Subtotal': '€' + ((item.subtotal || 0).toFixed ? (item.subtotal || 0).toFixed(2) : '0.00')
        })));
        console.log('Total Items:', cart.totalItems);
        console.log('Total Cost:', cart.formattedTotal);
        console.log('Last Updated:', cart.lastUpdated);
    } else {
        console.log('Cart is empty or no cart data found in localStorage');
    }
}

/**
 * Auto-sync cart data from the page (when on cart page)
 */
function autoSyncCartFromPage() {
    // Look for cart data in the page
    const cartSection = document.querySelector('.cart-section');
    if (cartSection) {
        // Get cart items from the page
        const cartItems = [];
        const itemElements = document.querySelectorAll('.cart-item');
        
        itemElements.forEach(item => {
            const nameElement = item.querySelector('.product-name');
            const priceElement = item.querySelector('.product-price');
            const quantityElement = item.querySelector('.quantity-input');
            
            if (nameElement && priceElement && quantityElement) {
                cartItems.push({
                    product_name: nameElement.textContent.trim(),
                    price: priceElement.textContent.replace('€', '').trim(),
                    quantity: parseInt(quantityElement.value) || 0
                });
            }
        });
        
        // Get totals from page
        const totalElement = document.querySelector('.summary-value.total');
        const totalText = totalElement ? totalElement.textContent.trim() : '€0.00';
        const totalCost = parseFloat(totalText.replace('€', '').replace(',', '.')) || 0;
        const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        
        // Store in localStorage
        storeCartData(cartItems, totalItems, totalCost, totalText);
    }
}

/**
 * Initialize cart storage on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-sync if we're on cart page
    autoSyncCartFromPage();
    
    // Sync cart count from session (if available)
    const cartCountElements = document.querySelectorAll('.cart-count, .mobile-cart-count');
    if (cartCountElements.length > 0) {
        const displayedCount = cartCountElements[0].textContent;
        if (displayedCount && displayedCount.trim() !== '') {
            updateCartCount(parseInt(displayedCount));
        }
    }
});

// Make functions available globally
window.cartStorage = {
    store: storeCartData,
    get: getCartData,
    clear: clearCartData,
    updateCount: updateCartCount,
    showInfo: showCartInfo,
    autoSync: autoSyncCartFromPage
};