//TODO: NO ALERTS AND VALIDATION ON THE INPUT
// Client-side validation for edit form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.auth-form');
    const nameInput = document.getElementById('name');
    const surnamesInput = document.getElementById('surnames');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const roleSelect = document.getElementById('role');

    // Detect if this is edit mode 
    const isEditMode = document.querySelector('input[name="id"][type="hidden"]') !== null;

    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errors = [];

        // Validate name
        if (nameInput.value.trim().length < 2) {
            errors.push('El nombre debe tener al menos 2 caracteres');
            isValid = false;
        }

        // Validate surnames
        if (surnamesInput.value.trim().length < 2) {
            errors.push('Los apellidos deben tener al menos 2 caracteres');
            isValid = false;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            errors.push('El formato del email no es válido');
            isValid = false;
        }

        // Validate password 
        if (isEditMode) {
            // Edit mode: password is optional, but if provided must be >= 4 chars
            if (passwordInput.value.length > 0 && passwordInput.value.length < 4) {
                errors.push('Si cambias la contraseña, debe tener al menos 4 caracteres');
                isValid = false;
            }
        } else {
            // Create mode: password is required and must be >= 4 chars
            if (passwordInput.value.length < 4) {
                errors.push('La contraseña debe tener al menos 4 caracteres');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
            alert('Errores encontrados:\n' + errors.join('\n'));
        }
    });
});