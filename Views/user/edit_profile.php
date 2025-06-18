<?php
// Get current user data
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

// Get messages for display
$messages = $messages ?? [];

// Get user data
$userData = $user ?? $currentUser;
$userName = $userData['name'] ?? '';
$userSurnames = $userData['surnames'] ?? '';
$userEmail = $userData['email'] ?? '';
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

<!-- Edit Profile Section -->
<section class="edit-profile-section">
    <div class="settings-container">

        <!-- Back Navigation -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-arrow-left"></i>
                Volver al Perfil
            </a>
        </div>

        <!-- Edit Profile Form -->
        <div class="edit-profile-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="card-title">
                    <h2>Editar perfil</h2>
                    <p class="card-subtitle">Actualiza tu información personal</p>
                </div>
            </div>

            <div class="card-content">
                <form action="<?php echo BASE_URL; ?>index.php?controller=user&action=updateProfile" method="POST" class="edit-profile-form">

                    <!-- Name Fields -->
                    <div class="form-row">
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
                                placeholder="Tu nombre"
                                required>
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
                                placeholder="Tus apellidos"
                                required>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Correo electrónico
                        </label>
                        <input type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            value="<?php echo htmlspecialchars($userEmail); ?>"
                            placeholder="tu@email.com"
                            required>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Nueva contraseña (opcional)
                        </label>
                        <input type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Deja en blanco para mantener la actual"
                            minlength="4">
                        <small class="form-hint">Solo completa si deseas cambiar tu contraseña</small>
                    </div>

                    <!-- Form Buttons -->
                    <div class="form-buttons">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile"
                            class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</section>