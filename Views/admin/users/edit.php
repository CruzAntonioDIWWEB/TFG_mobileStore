<?php
// Get current user data
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

// Get messages for display
$messages = $messages ?? [];

// Get user data
$userData = $user ?? null;
$userId = $userData ? $userData->getId() : null;
$userName = $userData ? $userData->getName() : '';
$userSurnames = $userData ? $userData->getSurnames() : '';
$userEmail = $userData ? $userData->getEmail() : '';
$userRole = $userData ? $userData->getRole() : 'cliente';
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

<!-- Edit User Section -->
<section class="auth-section">
    <div class="auth-container">
        
        <!-- Navigation Breadcrumb -->
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
            <span class="breadcrumb-current">Editar Usuario</span>
        </div>

        <!-- Edit User Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">
                    <i class="fas fa-user-edit"></i>
                    Editar Usuario
                </h1>
                <p class="auth-subtitle">Modifica los datos del usuario: <strong><?php echo htmlspecialchars($userName . ' ' . $userSurnames); ?></strong></p>
            </div>

            <?php if ($userData): ?>
                <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=user&action=update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($userId); ?>">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fas fa-user"></i>
                            Nombre
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($userName); ?>"
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
                               value="<?php echo htmlspecialchars($userSurnames); ?>"
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
                               value="<?php echo htmlspecialchars($userEmail); ?>"
                               placeholder="ejemplo@correo.com"
                               required
                               maxlength="100"
                               autocomplete="email">
                        <small class="form-hint">Debe ser único. Máximo 100 caracteres.</small>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Nueva Contraseña
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input" 
                               placeholder="Dejar vacío para mantener la actual"
                               minlength="4"
                               autocomplete="new-password">
                        <small class="form-hint">Opcional. Si se indica, mínimo 4 caracteres. Se encriptará automáticamente.</small>
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
                            <option value="cliente" <?php echo ($userRole === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                            <option value="admin" <?php echo ($userRole === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                        <small class="form-hint">Los administradores tienen acceso total al sistema.</small>
                    </div>

                    <div class="user-info">
                        <div class="info-item">
                            <span class="info-label">ID de usuario:</span>
                            <span class="info-value">#<?php echo htmlspecialchars($userId); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Rol actual:</span>
                            <span class="info-value">
                                <?php if ($userRole === 'admin'): ?>
                                    <span style="color: #E60000; font-weight: 600;">
                                        <i class="fas fa-crown" style="color: #ffc107;"></i> Administrador
                                    </span>
                                <?php else: ?>
                                    <span style="color: #333333;">
                                        <i class="fas fa-user"></i> Cliente
                                    </span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($userId == $currentUser['id']): ?>
                            <div class="info-item">
                                <span class="info-label">Nota:</span>
                                <span class="info-value" style="color: #dc3545; font-weight: 600;">Estás editando tu propia cuenta</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="auth-btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                        
                        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=index" class="auth-btn btn-secondary">
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
                    <h3>Error al cargar el usuario</h3>
                    <p>No se pudo encontrar el usuario solicitado.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=index" class="auth-btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Volver al listado
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/user/userFormValidation.js"></script>