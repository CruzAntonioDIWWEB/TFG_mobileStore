function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validation functions
function validateName(input) {
    const value = input.value.trim();
    const isValid = value.length >= 2 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value);
    
    toggleInputState(input, isValid);
    return isValid;
}

function validateEmail(input) {
    const value = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailRegex.test(value);
    
    toggleInputState(input, isValid);
    return isValid;
}

function validatePassword(input) {
    const value = input.value;
    // Cambiado a mínimo 4 caracteres
    const isValid = value.length >= 4;
    
    toggleInputState(input, isValid);
    return isValid;
}

function toggleInputState(input, isValid) {
    if (input.value.trim() === '') {
        // Si está vacío, quitar todas las clases
        input.classList.remove('success', 'error');
        return;
    }
    
    if (isValid) {
        input.classList.remove('error');
        input.classList.add('success');
    } else {
        input.classList.remove('success');
        input.classList.add('error');
    }
}

// Add event listeners when page loads
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const surnamesInput = document.getElementById('surnames');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    // Verificar que todos los elementos existen
    if (!nameInput || !surnamesInput || !emailInput || !passwordInput) {
        console.error('No se pudieron encontrar todos los inputs del formulario');
        return;
    }
    
    // Name validation
    nameInput.addEventListener('input', function() {
        validateName(this);
    });
    
    nameInput.addEventListener('blur', function() {
        validateName(this);
    });
    
    // Surnames validation
    surnamesInput.addEventListener('input', function() {
        validateName(this);
    });
    
    surnamesInput.addEventListener('blur', function() {
        validateName(this);
    });
    
    // Email validation
    emailInput.addEventListener('input', function() {
        validateEmail(this);
    });
    
    emailInput.addEventListener('blur', function() {
        validateEmail(this);
    });
    
    // Password validation
    passwordInput.addEventListener('input', function() {
        console.log('Password input event:', this.value, 'Length:', this.value.length);
        validatePassword(this);
    });
    
    passwordInput.addEventListener('blur', function() {
        validatePassword(this);
    });
    
    // Form submission validation
    const form = document.querySelector('.auth-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameValid = validateName(nameInput);
            const surnamesValid = validateName(surnamesInput);
            const emailValid = validateEmail(emailInput);
            const passwordValid = validatePassword(passwordInput);
            
            console.log('Form validation:', {
                name: nameValid,
                surnames: surnamesValid,
                email: emailValid,
                password: passwordValid
            });
            
            if (!nameValid || !surnamesValid || !emailValid || !passwordValid) {
                e.preventDefault();
                alert('Por favor, corrige los errores en el formulario antes de enviarlo.');
            }
        });
    }
});