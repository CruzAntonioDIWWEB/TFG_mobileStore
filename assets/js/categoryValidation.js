document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.auth-form');
    const nameInput = document.getElementById('name');
    const originalName = nameInput ? nameInput.value : null;

    // Real-time validation
    nameInput.addEventListener('input', function () {
        const value = this.value.trim();
        this.classList.remove('input-error', 'input-success');

        if (value.length === 0) {
            return;
        }

        if (value.length > 25) {
            this.classList.add('input-error');
        } else if (value.length >= 2) {
            this.classList.add('input-success');
        }
    });

    // Form submission validation
    form.addEventListener('submit', function (e) {
        const name = nameInput.value.trim();

        if (name.length === 0) {
            e.preventDefault();
            nameInput.classList.add('input-error');
            nameInput.focus();
            alert('El nombre de la categoría es obligatorio.');
            return false;
        }

        if (name.length > 25) {
            e.preventDefault();
            nameInput.classList.add('input-error');
            nameInput.focus();
            alert('El nombre de la categoría no puede exceder 25 caracteres.');
            return false;
        }

        if (name.length < 2) {
            e.preventDefault();
            nameInput.classList.add('input-error');
            nameInput.focus();
            alert('El nombre de la categoría debe tener al menos 2 caracteres.');
            return false;
        }

        if (originalName && name === originalName) {
            e.preventDefault();
            alert('No has realizado ningún cambio en el nombre de la categoría.');
            return false;
        }

        if (!confirm('¿Estás seguro de que quieres guardar los cambios en esta categoría?')) {
            e.preventDefault();
            return false;
        }

        return true;
    });
});