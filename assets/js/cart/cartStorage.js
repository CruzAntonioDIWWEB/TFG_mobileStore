// ========================================
// CART LOCALSTORAGE MANAGEMENT
// ========================================

/**
 * Parse European price format correctly
 * This produces: "59,99 €" which should become 59.99
 */
function parseEuropeanPrice(priceText) {
    // Remove whitespace and currency symbols
    priceText = priceText.replace(/\s/g, '').replace(/[€$]/g, '');
    
    // Handle European format: 59,99 or 1.299,99
    if (/,\d{2}$/.test(priceText)) {
        // Has comma with 2 digits at end = European decimal
        const parts = priceText.split(',');
        if (parts.length === 2 && parts[1].length === 2) {
            let wholePart = parts[0].replace(/\./g, ''); // Remove thousands dots
            let decimalPart = parts[1];
            return parseFloat(wholePart + '.' + decimalPart) || 0;
        }
    }
    
    // Fallback: remove commas and parse
    return parseFloat(priceText.replace(/,/g, '')) || 0;
}


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
            const nameElement = item.querySelector('.product-name, .item-name, [class*="name"]');
            const priceElement = item.querySelector('.product-price, .item-price, [class*="price"]');
            const quantityElement = item.querySelector('.quantity-input, input[type="number"]');
            
            if (nameElement && priceElement && quantityElement) {
                // Get price text and parse correctly
                let priceText = priceElement.textContent.trim();
                const price = parseEuropeanPrice(priceText);
                const quantity = parseInt(quantityElement.value) || 0;
                
                // Debug: log the conversion
                console.log(`Price conversion: "${priceText}" → ${price}`);
                
                cartItems.push({
                    product_name: nameElement.textContent.trim(),
                    name: nameElement.textContent.trim(),
                    price: price,
                    quantity: quantity
                });
            }
        });
        
        // Calculate totals properly
        const totalCost = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        const formattedTotal = `€${totalCost.toFixed(2)}`;
        
        // Debug: log the final totals
        console.log('Cart sync - Items:', cartItems.length, 'Total cost:', totalCost);
        
        // Store in localStorage using existing function
        storeCartData(cartItems, totalItems, totalCost, formattedTotal);
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