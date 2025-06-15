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
        <div class="breadcrumb breadcrumb-compact">
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

    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/categoryValidation.js"></script>