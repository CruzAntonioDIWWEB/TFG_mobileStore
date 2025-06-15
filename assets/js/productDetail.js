document.addEventListener('DOMContentLoaded', function () {

    // Get quantity control elements
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.querySelector('.quantity-minus');
    const increaseBtn = document.querySelector('.quantity-plus');

    // Check if elements exist
    if (!quantityInput || !decreaseBtn || !increaseBtn) {
        console.log('Quantity controls not found on page');
        return;
    }

    console.log('Quantity controls found:', { quantityInput, decreaseBtn, increaseBtn });

    // Get stock limits
    const maxStock = parseInt(quantityInput.getAttribute('max')) || 999;
    const minQuantity = parseInt(quantityInput.getAttribute('min')) || 1;

    console.log('Stock limits:', { maxStock, minQuantity });

    // Make input editable 
    quantityInput.removeAttribute('readonly');

    /**
     * Update quantity value safely
     */
    function updateQuantity(newValue) {
        console.log('Updating quantity to:', newValue);
        
        // Ensure value is within bounds
        if (newValue < minQuantity) {
            newValue = minQuantity;
        } else if (newValue > maxStock) {
            newValue = maxStock;
            showMessage('No hay suficiente stock disponible');
            return;
        }

        // Update input value
        quantityInput.value = newValue;
        
        // Update button states
        updateButtonStates(newValue);
        
        console.log('Quantity updated to:', newValue);
    }

    /**
     * Update button states
     */
    function updateButtonStates(currentValue) {
        // Disable/enable decrease button
        if (currentValue <= minQuantity) {
            decreaseBtn.disabled = true;
            decreaseBtn.style.opacity = '0.5';
        } else {
            decreaseBtn.disabled = false;
            decreaseBtn.style.opacity = '1';
        }

        // Disable/enable increase button
        if (currentValue >= maxStock) {
            increaseBtn.disabled = true;
            increaseBtn.style.opacity = '0.5';
        } else {
            increaseBtn.disabled = false;
            increaseBtn.style.opacity = '1';
        }
    }

    /**
     * Decrease quantity handler
     */
    decreaseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Decrease button clicked');
        
        const currentValue = parseInt(quantityInput.value) || minQuantity;
        const newValue = currentValue - 1;
        
        console.log('Current:', currentValue, 'New:', newValue);
        
        updateQuantity(newValue);
    });

    /**
     * Increase quantity handler
     */
    increaseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const currentValue = parseInt(quantityInput.value) || minQuantity;
        const newValue = currentValue + 1;
        
        console.log('Current:', currentValue, 'New:', newValue);
        
        updateQuantity(newValue);
    });

    /**
     * Handle direct input changes
     */
    quantityInput.addEventListener('input', function() {
        console.log('Input changed to:', this.value);
        
        const value = parseInt(this.value);
        
        if (isNaN(value) || value < minQuantity) {
            this.value = minQuantity;
            updateButtonStates(minQuantity);
            return;
        }

        if (value > maxStock) {
            this.value = maxStock;
            showMessage('No hay suficiente stock disponible');
            updateButtonStates(maxStock);
            return;
        }

        updateButtonStates(value);
    });

    /**
     * Handle focus out to ensure valid value
     */
    quantityInput.addEventListener('blur', function() {
        const value = parseInt(this.value);
        if (isNaN(value) || value < minQuantity) {
            this.value = minQuantity;
            updateButtonStates(minQuantity);
        }
    });

    /**
     * Prevent non-numeric input
     */
    quantityInput.addEventListener('keydown', function(e) {
        // Allow: backspace, delete, tab, escape, enter, arrows
        if ([46, 8, 9, 27, 13, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }
        
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // Initialize button states
    const initialValue = parseInt(quantityInput.value) || minQuantity;
    updateButtonStates(initialValue);

    // Test button accessibility
    [decreaseBtn, increaseBtn].forEach((btn, index) => {
        btn.setAttribute('tabindex', '0');
        btn.setAttribute('role', 'button');
        
        if (index === 0) {
            btn.setAttribute('aria-label', 'Disminuir cantidad');
        } else {
            btn.setAttribute('aria-label', 'Aumentar cantidad');
        }
        
        // Keyboard support
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Form validation before submission
    const form = document.querySelector('.add-to-cart-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const quantity = parseInt(quantityInput.value);
            
            if (isNaN(quantity) || quantity < 1) {
                e.preventDefault();
                showMessage('Por favor, selecciona una cantidad válida');
                return false;
            }

            if (quantity > maxStock) {
                e.preventDefault();
                showMessage('La cantidad seleccionada excede el stock disponible');
                return false;
            }

            console.log('Form submitted with quantity:', quantity);
        });
    }
});

/**
 * Simple message display function
 */
function showMessage(message) {

    // Remove existing messages
    const existingMessages = document.querySelectorAll('.temp-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create message element
    const messageEl = document.createElement('div');
    messageEl.className = 'temp-message';
    messageEl.textContent = message;
    
    // Add to page
    document.body.appendChild(messageEl);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (messageEl.parentNode) {
            messageEl.remove();
        }
    }, 3000);
}