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

<!-- Register Form Section -->
<section class="auth-section">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Form Header -->
            <div class="auth-header">
                <h1 class="auth-title">Registro</h1>
                <p class="auth-subtitle">Crea tu cuenta para empezar a comprar</p>
            </div>

            <!-- Register Form -->
            <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>process_registration.php">
                <div class="form-group">
                    <label class="form-label" for="name">
                        <i class="fas fa-user"></i>
                        Nombre
                    </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Introduce tu nombre" required>
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="surnames">
                        <i class="fas fa-user"></i>
                        Apellidos
                    </label>
                    <input type="text" id="surnames" name="surnames" class="form-input" placeholder="Introduce tus apellidos" required>
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required>
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Introduce tu contraseña" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <span class="form-error"></span>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <i class="fas fa-user-plus"></i>
                    Registrarse
                </button>
            </form>

            <!-- Auth Footer -->
            <div class="auth-footer">
                <p class="auth-link-text">
                    ¿Ya tienes una cuenta?
                    <a href="<?php echo BASE_URL; ?>login" class="auth-link">Inicia sesión</a>
                </p>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="auth-info">
            <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <span>Tus datos están protegidos</span>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <span>Registro rápido y sencillo</span>
            </div>
            <div class="info-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Acceso inmediato a compras</span>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/user/register.js"></script>