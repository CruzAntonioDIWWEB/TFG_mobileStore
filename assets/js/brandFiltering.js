document.addEventListener('DOMContentLoaded', function() {
    const brandButtons = document.querySelectorAll('.brand-btn');
    const phoneCards = document.querySelectorAll('.phone-card');
    
    // Check if required elements exist
    if (brandButtons.length === 0 || phoneCards.length === 0) {
        console.warn('Brand filtering elements not found');
        return;
    }
    
    brandButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedBrand = this.getAttribute('data-brand');
            
            // Update active button
            brandButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter phones with smooth animation
            filterPhones(selectedBrand);
            
            // Update URL without page reload
            updateURL(selectedBrand);
        });
    });
    
    /**
     * Filter phones based on selected brand
     * @param {string} selectedBrand - The brand to filter by ('all' for no filter)
     */
    function filterPhones(selectedBrand) {
        let visibleCount = 0;
        
        phoneCards.forEach(card => {
            const cardBrand = card.getAttribute('data-brand');
            
            if (selectedBrand === 'all' || cardBrand === selectedBrand) {
                // Show card with smooth animation
                card.style.display = 'block';
                card.classList.remove('hidden');
                
                // Smooth fade in effect
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 50);
                
                visibleCount++;
            } else {
                // Hide card
                card.style.display = 'none';
                card.classList.add('hidden');
            }
        });
        
        // Show/hide no results message
        toggleNoResultsMessage(visibleCount === 0, selectedBrand);
    }
    
    /**
     * Update URL parameters without page reload
     * @param {string} selectedBrand - The selected brand
     */
    function updateURL(selectedBrand) {
        const url = new URL(window.location);
        if (selectedBrand === 'all') {
            url.searchParams.delete('brand');
        } else {
            url.searchParams.set('brand', selectedBrand);
        }
        window.history.replaceState({}, '', url);
    }
    
    /**
     * Show or hide no results message
     * @param {boolean} show - Whether to show the message
     * @param {string} brandName - The brand name for the message
     */
    function toggleNoResultsMessage(show, brandName) {
        let noResultsMessage = document.querySelector('.no-results-message');
        
        if (show) {
            if (!noResultsMessage) {
                const grid = document.getElementById('phones-grid') || document.querySelector('.phones-grid');
                if (grid) {
                    noResultsMessage = createNoResultsMessage(brandName);
                    grid.parentNode.appendChild(noResultsMessage);
                }
            } else {
                // Update existing message
                const description = noResultsMessage.querySelector('p');
                if (description && brandName !== 'all') {
                    description.textContent = `No hay móviles disponibles para la marca seleccionada: ${brandName}.`;
                }
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }
    
    /**
     * Create no results message element
     * @param {string} brandName - The brand name
     * @returns {HTMLElement} The no results message element
     */
    function createNoResultsMessage(brandName) {
        const message = document.createElement('div');
        message.className = 'no-results-message';
        
        const content = document.createElement('div');
        content.className = 'no-results-content';
        
        const icon = document.createElement('i');
        icon.className = 'fas fa-search no-results-icon';
        
        const title = document.createElement('h3');
        title.textContent = 'No se encontraron móviles';
        
        const description = document.createElement('p');
        description.textContent = brandName === 'all' 
            ? 'No hay móviles disponibles.' 
            : `No hay móviles disponibles para la marca: ${brandName}.`;
        
        content.appendChild(icon);
        content.appendChild(title);
        content.appendChild(description);
        message.appendChild(content);
        
        return message;
    }
    
    /**
     * Initialize brand filtering from URL parameters
     */
    function initializeFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const brandParam = urlParams.get('brand');
        
        if (brandParam) {
            const targetButton = document.querySelector(`[data-brand="${brandParam}"]`);
            if (targetButton) {
                targetButton.click();
            }
        }
    }
    
    // Initialize from URL on page load
    initializeFromURL();
    
    // Add to cart functionality (if user is logged in)
    const cartButtons = document.querySelectorAll('.btn-cart');
    cartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            // Store original button content
            const originalContent = this.innerHTML;
            
            // Create success icon
            const successIcon = document.createElement('i');
            successIcon.className = 'fas fa-check';
            
            // Create success text
            const successText = document.createTextNode(' Añadido');
            
            // Clear button and add success content
            this.innerHTML = '';
            this.appendChild(successIcon);
            this.appendChild(successText);
            this.classList.add('added');
            this.disabled = true;
            
            // Reset after 2 seconds
            setTimeout(() => {
                // Create cart icon
                const cartIcon = document.createElement('i');
                cartIcon.className = 'fas fa-shopping-cart';
                
                // Create cart text
                const cartText = document.createTextNode(' Añadir');
                
                // Clear button and add original content
                this.innerHTML = '';
                this.appendChild(cartIcon);
                this.appendChild(cartText);
                this.classList.remove('added');
                this.disabled = false;
            }, 2000);
        });
    });
});