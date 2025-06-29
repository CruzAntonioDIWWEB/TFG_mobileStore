document.addEventListener('DOMContentLoaded', function () {

    // Get all cart buttons
    const cartButtons = document.querySelectorAll('.btn-cart');

    cartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');

            if (!productId) {
                alert('Error: No se pudo identificar el producto');
                return;
            }

            // Save original button content
            const originalContent = this.innerHTML;

            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Añadiendo...';

            // Send AJAX request
            addToCart(productId, this, originalContent, productName);
        });
    });

    function addToCart(productId, button, originalContent, productName) {
        // Create form data
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);

        // Send request
        fetch('index.php?controller=cart&action=ajaxAddToCart', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // FIXED: Show success with .added class for green styling
                    button.innerHTML = '<i class="fas fa-check"></i> Añadido';
                    button.classList.add('added'); 
                    // Keep disabled = true

                    // Update cart count if element exists
                    updateCartCount(data.cartCount);

                    // Update localStorage with new cart count
                    if (window.cartStorage) {
                        window.cartStorage.updateCount(data.cartCount);
                    }

                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalContent;
                        button.classList.remove('added'); // Remove the class when resetting
                    }, 2000);

                } else {
                    // Show error
                    alert(data.message || 'Error al añadir producto al carrito');
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión. Inténtalo de nuevo.');
                button.disabled = false;
                button.innerHTML = originalContent;
            });
    }

    /**
     * Update cart count in header
     * @param {number} count - New cart count
     */
    function updateCartCount(newCount) {
    // Update cart count in header - FIXED selector and display
    const cartCounts = document.querySelectorAll('.cart-count, .mobile-cart-count');
    cartCounts.forEach(element => {
        if (newCount > 0) {
            element.textContent = newCount;
            element.style.display = 'flex'; // FIXED: was 'inline', should be 'flex'
        } else {
            element.style.display = 'none';
        }
    });
}
});