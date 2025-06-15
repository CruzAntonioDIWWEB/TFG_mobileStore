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

<!-- Contact Form Section -->
<section class="contact-section">
    <div class="contact-container">
        <div class="contact-form-card">
            <div class="contact-header">
                <h1 class="contact-title">Contacto</h1>
                <p class="contact-subtitle">¿Tienes alguna pregunta? Nos encantaría ayudarte</p>
            </div>

            <!-- Contact Form -->
            <form class="contact-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=contact&action=processContact">
                <div class="form-group">
                    <label class="form-label" for="name">
                        <i class="fas fa-user"></i>
                        Nombre *
                    </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Introduce tu nombre" required>
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i>
                        Email *
                    </label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required>
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">
                        <i class="fas fa-comment-alt"></i>
                        Mensaje *
                    </label>
                    <textarea id="message" name="message" class="form-input form-textarea" rows="6" placeholder="Escribe tu mensaje aquí..." required></textarea>
                    <span class="form-error"></span>
                </div>

                <button type="submit" class="contact-submit-btn">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Mensaje
                </button>
            </form>
        </div>

        <!-- Contact Info Below Form -->
        <div class="contact-info-section">
            <div class="contact-methods">
                <div class="contact-method">
                    <div class="contact-method-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-method-text">
                        <strong>Email:</strong>
                        <span>acg.purullena@gmail.com</span>
                    </div>
                </div>

                <div class="contact-method">
                    <div class="contact-method-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-method-text">
                        <strong>Teléfono:</strong>
                        <span>+34 653 88 00 06</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>