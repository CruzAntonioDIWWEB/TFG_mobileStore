document.addEventListener('DOMContentLoaded', function() {
    const brandButtons = document.querySelectorAll('.brand-btn');
    const phoneCards = document.querySelectorAll('.phone-card');
    
    brandButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedBrand = this.getAttribute('data-brand');
            
            // Update active button
            brandButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter phones
            phoneCards.forEach(card => {
                const phoneName = card.getAttribute('data-brand').toLowerCase();
                
                if (selectedBrand === 'all') {
                    card.style.display = 'block';
                    card.classList.remove('hidden');
                } else {
                    if (phoneName.includes(selectedBrand)) {
                        card.style.display = 'block';
                        card.classList.remove('hidden');
                    } else {
                        card.style.display = 'none';
                        card.classList.add('hidden');
                    }
                }
            });
            
            // Show/hide no results message
            const visibleCards = document.querySelectorAll('.phone-card:not(.hidden)');
            const noResultsMessage = document.querySelector('.no-results-message');
            
            if (visibleCards.length === 0) {
                if (!noResultsMessage) {
                    const grid = document.getElementById('phones-grid');
                    
                    // Create main message container
                    const message = document.createElement('div');
                    message.className = 'no-results-message';
                    
                    // Create content wrapper
                    const content = document.createElement('div');
                    content.className = 'no-results-content';
                    
                    // Create icon
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-search no-results-icon';
                    
                    // Create title
                    const title = document.createElement('h3');
                    title.textContent = 'No se encontraron móviles';
                    
                    // Create description
                    const description = document.createElement('p');
                    description.textContent = 'No hay móviles disponibles para la marca seleccionada.';
                    
                    // Append elements in hierarchy
                    content.appendChild(icon);
                    content.appendChild(title);
                    content.appendChild(description);
                    message.appendChild(content);
                    
                    // Add to DOM
                    grid.parentNode.appendChild(message);
                }
            } else {
                if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }
        });
    });
    
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