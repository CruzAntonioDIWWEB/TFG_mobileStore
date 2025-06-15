document.addEventListener('DOMContentLoaded', function () {
    const authForm = document.querySelector('.auth-form');
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.querySelector('.password-toggle');

    /**
     * Check for remembered email and populate the field
     */
    function checkRememberedEmail() {
        // Function to get cookie value
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        const emailInput = document.getElementById('email');
        const rememberCheckbox = document.getElementById('remember');
        const rememberedEmail = getCookie('emailLogin');

        if (rememberedEmail && emailInput) {
            // Decode the URL-encoded email
            const decodedEmail = decodeURIComponent(rememberedEmail);
            emailInput.value = decodedEmail;

            // Optionally check the remember checkbox since email was remembered
            if (rememberCheckbox) {
                rememberCheckbox.checked = true;
            }
        }
    }

    // Call the function when page loads
    checkRememberedEmail();

    if (!authForm) {
        console.warn('Auth form not found');
        return;
    }

    // Password toggle functionality
    initializePasswordToggle();

    // Form validation on submit
    authForm.addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    // Real-time validation on focus/blur
    initializeFocusValidation();

    /**
     * Initialize password visibility toggle
     */
    function initializePasswordToggle() {
        if (!passwordToggle || !passwordInput) return;

        passwordToggle.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            const icon = document.getElementById('password-toggle-icon');

            passwordInput.setAttribute('type', type);

            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                passwordToggle.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                passwordToggle.setAttribute('aria-label', 'Mostrar contraseña');
            }
        });
    }

    /**
     * Validate the entire form (only on submit)
     * @returns {boolean} - True if form is valid
     */
    function validateForm() {
        let isValid = true;

        // Get all form inputs
        const nameInput = document.getElementById('name');
        const surnamesInput = document.getElementById('surnames');
        const emailInput = document.getElementById('email');

        // Validate name
        if (nameInput && !validateName(nameInput.value)) {
            showError('name', 'El nombre es obligatorio');
            isValid = false;
        }

        // Validate surnames (only if register form)
        if (surnamesInput && !validateSurnames(surnamesInput.value)) {
            showError('surnames', 'Los apellidos son obligatorios');
            isValid = false;
        }

        // Validate email
        if (emailInput && !validateEmail(emailInput.value)) {
            showError('email', 'Introduce un email válido');
            isValid = false;
        }

        // Validate password
        if (passwordInput && !validatePassword(passwordInput.value)) {
            showError('password', 'La contraseña debe tener al menos 4 caracteres');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Initialize focus-based validation (only show errors for current field)
     */
    function initializeFocusValidation() {
        const inputs = authForm.querySelectorAll('.form-input');

        inputs.forEach(function (input) {
            // When user focuses on a field, clear all other errors
            input.addEventListener('focus', function () {
                clearAllErrors();
            });

            // When user leaves a field, validate only that field
            input.addEventListener('blur', function () {
                validateCurrentField(this);
            });

            // Clear error as user types
            input.addEventListener('input', function () {
                const fieldName = this.getAttribute('name');
                if (fieldName) {
                    clearError(fieldName);
                }
            });
        });
    }

    /**
     * Validate only the current field that user just left
     * @param {HTMLElement} input - The input element to validate
     */
    function validateCurrentField(input) {
        const fieldName = input.getAttribute('name');
        const value = input.value;

        // Only validate if the field has some content or if it's required and empty
        switch (fieldName) {
            case 'name':
                if (!validateName(value)) {
                    showError('name', 'El nombre es obligatorio');
                } else {
                    clearError('name');
                }
                break;

            case 'surnames':
                if (!validateSurnames(value)) {
                    showError('surnames', 'Los apellidos son obligatorios');
                } else {
                    clearError('surnames');
                }
                break;

            case 'email':
                if (value.trim() && !validateEmail(value)) {
                    showError('email', 'Introduce un email válido');
                } else if (!value.trim()) {
                    showError('email', 'El email es obligatorio');
                } else {
                    clearError('email');
                }
                break;

            case 'password':
                if (value && !validatePassword(value)) {
                    showError('password', 'La contraseña debe tener al menos 4 caracteres');
                } else if (!value) {
                    showError('password', 'La contraseña es obligatoria');
                } else {
                    clearError('password');
                }
                break;
        }
    }

    /**
     * Clear all error messages and styles
     */
    function clearAllErrors() {
        const inputs = authForm.querySelectorAll('.form-input');

        inputs.forEach(function (input) {
            const fieldName = input.getAttribute('name');
            if (fieldName) {
                clearError(fieldName);
            }
        });
    }

    /**
     * Validation functions
     */
    function validateName(name) {
        return name && name.trim().length > 0;
    }

    function validateSurnames(surnames) {
        return surnames && surnames.trim().length > 0;
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return email && emailRegex.test(email.trim());
    }

    function validatePassword(password) {
        return password && password.length >= 4;
    }

    /**
     * Show error message for a field
     * @param {string} fieldName - Name of the field
     * @param {string} message - Error message
     */
    function showError(fieldName, message) {
        const input = document.getElementById(fieldName);
        const errorElement = document.getElementById(fieldName + '-error');

        if (input) {
            input.classList.add('error');
            input.classList.remove('success');
        }

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    /**
     * Clear error message for a field
     * @param {string} fieldName - Name of the field
     */
    function clearError(fieldName) {
        const input = document.getElementById(fieldName);
        const errorElement = document.getElementById(fieldName + '-error');

        if (input) {
            input.classList.remove('error');
            if (input.value.trim()) {
                input.classList.add('success');
            }
        }

        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.remove('show');
        }
    }

});