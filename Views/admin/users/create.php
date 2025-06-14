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

<!-- Create User Section -->
<section class="auth-section">
    <div class="auth-container">

        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-user"></i>
                Perfil
            </a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=index" class="breadcrumb-link">
                <i class="fas fa-users"></i>
                Usuarios
            </a>
            <i class="fas fa-chevron-right"></i>
            <span class="breadcrumb-current">Nuevo Usuario</span>
        </div>

        <!-- Create User Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">
                    <i class="fas fa-user-plus"></i>
                    Nuevo Usuario
                </h1>
                <p class="auth-subtitle">Crea una nueva cuenta de usuario para la tienda</p>
            </div>

            <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=user&action=save">

                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i>
                        Nombre
                    </label>
                    <input type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        placeholder="Ej: Antonio, María, Carlos..."
                        required
                        maxlength="25"
                        autocomplete="given-name">
                    <small class="form-hint">Máximo 25 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="surnames" class="form-label">
                        <i class="fas fa-user"></i>
                        Apellidos
                    </label>
                    <input type="text"
                        id="surnames"
                        name="surnames"
                        class="form-input"
                        placeholder="Ej: García López, Martín Ruiz..."
                        required
                        maxlength="40"
                        autocomplete="family-name">
                    <small class="form-hint">Máximo 40 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="ejemplo@correo.com"
                        required
                        maxlength="100"
                        autocomplete="email">
                    <small class="form-hint">Debe ser único. Máximo 100 caracteres.</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <input type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Mínimo 4 caracteres"
                        required
                        minlength="4"
                        autocomplete="new-password">
                    <small class="form-hint">Mínimo 4 caracteres. Se encriptará automáticamente.</small>
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">
                        <i class="fas fa-crown"></i>
                        Rol de usuario
                    </label>
                    <select id="role"
                        name="role"
                        class="form-select"
                        required>
                        <option value="cliente" selected>Cliente</option>
                        <option value="admin">Administrador</option>
                    </select>
                    <small class="form-hint">Los administradores tienen acceso total al sistema.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="auth-btn btn-primary">
                        <i class="fas fa-save"></i>
                        Crear Usuario
                    </button>

                    <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=index" class="auth-btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver al listado
                    </a>
                </div>
            </form>
        </div>

    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/user/userFormValidation.js"></script>