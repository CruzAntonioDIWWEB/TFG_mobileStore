<?php
// Get current user data
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

// Get messages for display
$messages = $messages ?? [];

// Get category data
$categoryData = $category ?? null;
$categoryId = $categoryData ? $categoryData->getId() : null;
$categoryName = $categoryData ? $categoryData->getName() : '';
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

<!-- Edit Category Section -->
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
            <span class="breadcrumb-current">Editar Categoría</span>
        </div>

        <!-- Edit Category Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">
                    <i class="fas fa-edit"></i>
                    Editar Categoría
                </h1>
                <p class="auth-subtitle">Modifica los datos de la categoría: <strong><?php echo htmlspecialchars($categoryName); ?></strong></p>
            </div>

            <?php if ($categoryData): ?>
                <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=category&action=update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoryId); ?>">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fas fa-tag"></i>
                            Nombre de la categoría
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($categoryName); ?>"
                               placeholder="Ej: Móviles, Accesorios, Fundas..."
                               required
                               maxlength="25"
                               autocomplete="off">
                        <small class="form-hint">Máximo 25 caracteres. Debe ser único.</small>
                    </div>

                    <div class="category-info">
                        <div class="info-item">
                            <span class="info-label">ID de categoría:</span>
                            <span class="info-value">#<?php echo htmlspecialchars($categoryId); ?></span>
                        </div>
                        <?php if ($categoryData->getCreatedAt()): ?>
                            <div class="info-item">
                                <span class="info-label">Fecha de creación:</span>
                                <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($categoryData->getCreatedAt())); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($categoryData->getUpdatedAt() && $categoryData->getUpdatedAt() !== $categoryData->getCreatedAt()): ?>
                            <div class="info-item">
                                <span class="info-label">Última modificación:</span>
                                <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($categoryData->getUpdatedAt())); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="auth-btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                        
                        <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=index" class="auth-btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Volver al listado
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-state">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Error al cargar la categoría</h3>
                    <p>No se pudo encontrar la categoría solicitada.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=index" class="auth-btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Volver al listado
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Warning Card -->
        <div class="warning-card">
            <div class="warning-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Importante</h3>
            </div>
            <div class="warning-content">
                <ul class="warning-list">
                    <li>
                        <i class="fas fa-info-circle"></i>
                        Si cambias el nombre, asegúrate de que no exista otra categoría con el mismo nombre
                    </li>
                    <li>
                        <i class="fas fa-warning"></i>
                        Los productos asociados a esta categoría mantendrán su relación después del cambio
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        Los cambios se aplicarán inmediatamente en toda la tienda
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
    const originalName = nameInput.value;
    
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
    if (form) {
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
            
            // Check if name actually changed
            if (name === originalName) {
                e.preventDefault();
                alert('No has realizado ningún cambio en el nombre de la categoría.');
                return false;
            }
            
            // Confirm the changes
            if (!confirm('¿Estás seguro de que quieres guardar los cambios en esta categoría?')) {
                e.preventDefault();
                return false;
            }
            
            // All validations passed
            return true;
        });
    }
});
</script>