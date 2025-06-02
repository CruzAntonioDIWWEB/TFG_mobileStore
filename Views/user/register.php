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
<form method="POST" action="../../Public/register_test.php">
    <input type="text" name="name" placeholder="Name" required>
    <input type="text" name="surnames" placeholder="Surnames" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
        </div>

        <!-- Additional Info -->
        <div class="auth-info">
            <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <span>Tus datos están protegidos</span>
            </div>
            <div class="info-item">
                <i class="fas fa-shipping-fast"></i>
                <span>Envíos rápidos y seguros</span>
            </div>
            <div class="info-item">
                <i class="fas fa-headset"></i>
                <span>Soporte a tu disposicion</span>
            </div>
        </div>
    </div>
</section>

