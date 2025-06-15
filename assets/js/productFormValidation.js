document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category_id');
    const accessoryTypeSelect = document.getElementById('accessory_type_id');
    const accessoryTypeGroup = accessoryTypeSelect ? accessoryTypeSelect.closest('.form-group') : null;
    const mobileBrandSelect = document.getElementById('mobile_brand');
    const mobileBrandGroup = mobileBrandSelect ? mobileBrandSelect.closest('.form-group') : null;
    const form = document.querySelector('.auth-form');

    // Detect if we're in edit mode (has hidden input with product id)
    const isEditMode = document.querySelector('input[name="id"][type="hidden"]') !== null;

    if (!categorySelect) {
        console.error('❌ Category select not found!');
        return;
    }

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

    // Function to check if selected category is mobile-related
    function isMobileCategory(categoryText) {
        if (!categoryText) return false;
        const lowerText = categoryText.toLowerCase();
        return lowerText.includes('móvil') ||
            lowerText.includes('movil') ||
            lowerText.includes('teléfono') ||
            lowerText.includes('telefono') ||
            lowerText.includes('mobile') ||
            lowerText.includes('phone');
    }

    // Function to update accessory type field requirement and visibility
    function updateAccessoryTypeRequirement() {
        if (!accessoryTypeGroup) return;

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
                accessoryTypeSelect.value = '';
            }

            // Reset label to optional
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

    // Function to update mobile brand field requirement and visibility
    function updateMobileBrandRequirement() {
        if (!mobileBrandGroup) return;

        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categoryText = selectedOption ? selectedOption.text : '';
        const isMobile = isMobileCategory(categoryText);

        if (isMobile) {
            // Show and make mobile brand required
            mobileBrandGroup.style.display = 'block';
            mobileBrandSelect.setAttribute('required', 'required');

            // Update label to show it's required
            const label = mobileBrandGroup.querySelector('.form-label');
            if (label && !label.innerHTML.includes('*')) {
                label.innerHTML = label.innerHTML.replace('(Opcional)', '<span style="color: #E60000;">*</span>');
            }

            // Update hint
            const hint = mobileBrandGroup.querySelector('.form-help');
            if (hint) {
                hint.innerHTML = '<i class="fas fa-info-circle"></i> Requerido para productos móviles - se usa para filtrar por marca en la tienda.';
                hint.style.color = '#E60000';
            }

            // Add smooth animation
            mobileBrandGroup.style.opacity = '0';
            setTimeout(() => {
                mobileBrandGroup.style.opacity = '1';
            }, 100);

        } else {
            // Hide and make mobile brand optional
            mobileBrandGroup.style.display = 'none';
            mobileBrandSelect.removeAttribute('required');

            // Clear selection behavior based on mode
            if (!isEditMode) {
                mobileBrandSelect.value = '';
            }

            // Reset label to optional
            const label = mobileBrandGroup.querySelector('.form-label');
            if (label) {
                label.innerHTML = label.innerHTML.replace('<span style="color: #E60000;">*</span>', '(Opcional)');
            }

            // Reset hint
            const hint = mobileBrandGroup.querySelector('.form-help');
            if (hint) {
                hint.innerHTML = '<i class="fas fa-info-circle"></i> Esta información se usa para filtrar los móviles por marca en la tienda';
                hint.style.color = '';
            }
        }
    }

    // Combined function to update both fields based on category
    function updateFieldRequirements() {
        updateAccessoryTypeRequirement();
        updateMobileBrandRequirement();
    }

    // Listen for category changes
    if (categorySelect) {
        categorySelect.addEventListener('change', updateFieldRequirements);
    }

    // Form submission validation
    if (form) {
        form.addEventListener('submit', function (e) {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryText = selectedOption ? selectedOption.text : '';
            const isAccessory = isAccessoryCategory(categoryText);
            const isMobile = isMobileCategory(categoryText);

            // Validate accessory type field
            if (accessoryTypeGroup && isAccessory && accessoryTypeGroup.style.display !== 'none' && !accessoryTypeSelect.value) {
                e.preventDefault();
                alert('Por favor, selecciona un tipo de accesorio para esta categoría.');
                accessoryTypeSelect.focus();
                return false;
            }

            // Validate mobile brand field
            if (mobileBrandGroup && isMobile && mobileBrandGroup.style.display !== 'none' && !mobileBrandSelect.value) {
                e.preventDefault();
                alert('Por favor, selecciona una marca para este producto móvil.');
                mobileBrandSelect.focus();
                return false;
            }
        });
    }

    // Initial check on page load
    if (categorySelect) {
        updateFieldRequirements();
    }
});