<?php
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

<!-- Login Form Section -->
<section class="auth-section">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Form Header -->
            <div class="auth-header">
                <h1 class="auth-title">Iniciar Sesión</h1>
                <p class="auth-subtitle">Accede a tu cuenta para continuar</p>
            </div>

            <!-- Login Form -->
            <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=user&action=processLogin">
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required>
                    <span class="form-error" id="email-error"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Introduce tu contraseña" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-toggle-icon"></i>
                        </button>
                    </div>
                    <span class="form-error" id="password-error"></span>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember" class="form-checkbox">
                        <label for="remember" class="checkbox-label">
                            <span class="checkbox-custom"></span>
                            Recordarme
                        </label>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Auth Footer -->
            <div class="auth-footer">
                <p class="auth-link-text">
                    ¿No tienes una cuenta?
                    <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=register" class="auth-link">Regístrate</a>
                </p>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="auth-info">
            <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <span>Conexión segura</span>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <span>Acceso rápido</span>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/user/authValidation.js"></script>