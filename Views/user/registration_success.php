<!-- Registration Success Page -->
<section class="success-section">
    <div class="success-container">
        <div class="success-card">
            <!-- Success Icon -->
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <!-- Success Content -->
            <div class="success-content">
                <h1 class="success-title">¡Registro Completado!</h1>
                <p class="success-message">
                    Tu cuenta ha sido creada exitosamente. Ya puedes iniciar sesión y comenzar a explorar nuestros productos.
                </p>
                
                <!-- Action Buttons -->
                <div class="success-actions">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=login" class="success-btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="success-btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Ir al Inicio
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="success-info">
            <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <span>Tu cuenta está protegida</span>
            </div>
            <div class="info-item">
                <i class="fas fa-shopping-cart"></i>
                <span>¡Comienza a comprar ahora!</span>
            </div>
        </div>
    </div>
</section>