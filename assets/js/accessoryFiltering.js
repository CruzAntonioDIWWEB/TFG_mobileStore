document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('accessory-type-filter');
    const accessoryCards = document.querySelectorAll('.accessory-card');
    
    // Check if required elements exist
    if (!typeSelect || accessoryCards.length === 0) {
        console.warn('Accessory filtering elements not found');
        return;
    }
    
    // Add change event listener to the select dropdown
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        filterAccessories(selectedType);
    });
    
    /**
     * Filter accessories based on selected type 
     * @param {string} selectedType - The type ID to filter by ('all' for no filter)
     */
    function filterAccessories(selectedType) {
        accessoryCards.forEach(function(card, index) {
            const cardType = card.getAttribute('data-type');
            
            if (selectedType === 'all' || cardType === selectedType) {
                // Show card
                card.classList.remove('hidden');
                
                // Reset any manual styles that might interfere (porsiacaso)
                card.style.display = '';
            } else {
                // Hide card 
                card.classList.add('hidden');
                
                // After transition completes, set display none for better performance
                setTimeout(() => {
                    if (card.classList.contains('hidden')) {
                        card.style.display = 'none';
                    }
                }, 300); // Match the CSS transition duration
            }
        });
    }
    
    /**
     * Initialize cards to be visible by default
     */
    function initializeCards() {
        accessoryCards.forEach(function(card) {
            card.classList.remove('hidden');
            card.style.display = '';
        });
    }
    
    // Initialize
    initializeCards();
    
});