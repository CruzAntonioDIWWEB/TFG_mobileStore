<?php
// Get current user data
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

// Get messages for display
$messages = $messages ?? [];
?>

<!-- Messages Display -->
<?php if (!empty($messages['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($messages['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($messages['error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?php echo htmlspecialchars($messages['error']); ?>
    </div>
<?php endif; ?>

<!-- Create Category Section -->
<section class="auth-section">
    <div class="auth-container">
        
        <!-- Navigation Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-user"></i>
                Perfil
            </a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=index" class="breadcrumb-link">
                <i class="fas fa-tags"></i>
                Categorías
            </a>
            <i class="fas fa-chevron-right"></i>
            <span class="breadcrumb-current">Nueva Categoría</span>
        </div>

        <!-- Create Category Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">
                    <i class="fas fa-plus"></i>
                    Nueva Categoría
                </h1>
                <p class="auth-subtitle">Agrega una nueva categoría de productos a la tienda</p>
            </div>

            <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=category&action=save">
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-tag"></i>
                        Nombre de la categoría
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-input" 
                           placeholder="Ej: Móviles, Accesorios, Fundas..."
                           required
                           maxlength="25"
                           autocomplete="off">
                    <small class="form-hint">Máximo 25 caracteres. Debe ser único.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="auth-btn btn-primary">
                        <i class="fas fa-save"></i>
                        Crear Categoría
                    </button>
                    
                    <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=index" class="auth-btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver al listado
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-header">
                <i class="fas fa-info-circle"></i>
                <h3>Información sobre categorías</h3>
            </div>
            <div class="info-content">
                <ul class="info-list">
                    <li>
                        <i class="fas fa-check"></i>
                        Las categorías organizan los productos de tu tienda
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Cada producto debe pertenecer a una categoría
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Los nombres de categoría deben ser únicos
                    </li>
                    <li>
                        <i class="fas fa-exclamation-triangle"></i>
                        No podrás eliminar categorías que tengan productos asociados
                    </li>
                </ul>
            </div>
        </div>

    </div>
</section>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.auth-form');
    const nameInput = document.getElementById('name');
    
    // Real-time validation
    nameInput.addEventListener('input', function() {
        const value = this.value.trim();
        
        // Remove any existing validation classes
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
    form.addEventListener('submit', function(e) {
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
        
        // All validations passed
        return true;
    });
});
</script>