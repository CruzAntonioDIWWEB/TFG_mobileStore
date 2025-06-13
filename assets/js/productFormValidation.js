document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const accessoryTypeSelect = document.getElementById('accessory_type_id');
    const accessoryTypeGroup = accessoryTypeSelect.closest('.form-group');
    const form = document.querySelector('.auth-form');
    
    // Detect if we're in edit mode (has hidden input with product id)
    const isEditMode = document.querySelector('input[name="id"][type="hidden"]') !== null;

    // Function to check if selected category is accessory-related
    function isAccessoryCategory(categoryText) {
        if (!categoryText) return false;
        const lowerText = categoryText.toLowerCase();
        return lowerText.includes('accesorio') || 
               lowerText.includes('accessory') || 
               lowerText.includes('funda') || 
               lowerText.includes('cargador') ||
               lowerText.includes('cable') ||
               lowerText.includes('protector');
    }

    // Function to update accessory type field requirement and visibility
    function updateAccessoryTypeRequirement() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categoryText = selectedOption ? selectedOption.text : '';
        const isAccessory = isAccessoryCategory(categoryText);

        if (isAccessory) {
            // Show and make accessory type required
            accessoryTypeGroup.style.display = 'block';
            accessoryTypeSelect.setAttribute('required', 'required');
            
            // Update label to show it's required
            const label = accessoryTypeGroup.querySelector('.form-label');
            if (label && !label.innerHTML.includes('*')) {
                label.innerHTML = label.innerHTML.replace('(Opcional)', '<span style="color: #E60000;">*</span>');
            }
            
            // Update hint
            const hint = accessoryTypeGroup.querySelector('.form-hint');
            if (hint) {
                hint.textContent = 'Requerido para productos de tipo accesorio.';
                hint.style.color = '#E60000';
            }
        } else {
            // Hide and make accessory type optional
            accessoryTypeGroup.style.display = 'none';
            accessoryTypeSelect.removeAttribute('required');
            
            // Clear selection behavior based on mode
            if (!isEditMode) {
                // In create mode: clear selection when hidden
                accessoryTypeSelect.value = '';
            }
            // In edit mode: don't clear the selection, just hide it
            
            // Reset label to optional (for when it becomes visible again)
            const label = accessoryTypeGroup.querySelector('.form-label');
            if (label) {
                label.innerHTML = label.innerHTML.replace('<span style="color: #E60000;">*</span>', '(Opcional)');
            }
            
            // Reset hint
            const hint = accessoryTypeGroup.querySelector('.form-hint');
            if (hint) {
                hint.textContent = 'Solo si el producto es un accesorio.';
                hint.style.color = '';
            }
        }
    }

    // Listen for category changes
    categorySelect.addEventListener('change', updateAccessoryTypeRequirement);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categoryText = selectedOption ? selectedOption.text : '';
        const isAccessory = isAccessoryCategory(categoryText);
        
        // Only validate if the accessory type field is visible and category is accessory
        if (isAccessory && accessoryTypeGroup.style.display !== 'none' && !accessoryTypeSelect.value) {
            e.preventDefault();
            alert('Por favor, selecciona un tipo de accesorio para esta categoría.');
            accessoryTypeSelect.focus();
            return false;
        }
    });

    // Initial check on page load
    updateAccessoryTypeRequirement();
});