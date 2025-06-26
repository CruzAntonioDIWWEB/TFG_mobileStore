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

<!-- User Settings Section -->
<section class="settings-section">
    <div class="settings-container">
        <div class="settings-header">
            <div class="settings-title-section">
                <h1 class="settings-title">Configuración de la cuenta</h1>
                <p class="settings-subtitle">Gestiona tu perfil y preferencias</p>
            </div>
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] . ' ' . $currentUser['surnames']); ?></span>
                    <?php if ($isAdmin): ?>
                        <span class="user-role admin-role">
                            <i class="fas fa-crown"></i>
                            Administrador
                        </span>
                    <?php else: ?>
                        <span class="user-role">Cliente</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="settings-content">
            <div class="settings-grid">

                <!-- Account Management Card -->
                <div class="settings-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="card-title">
                            <h3>Gestión de cuenta</h3>
                            <p>Edita tu información personal</p>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nombre completo:</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['name'] . ' ' . $currentUser['surnames']); ?></span>
                        </div>
                    </div>
                    <div class="card-actions">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=editProfile" class="settings-btn btn-primary">
                            <i class="fas fa-edit"></i>
                            Editar perfil
                        </a>
                    </div>
                </div>

                <!-- Orders History Card -->
                <div class="settings-card">
    <div class="card-header">
        <div class="card-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="card-title">
            <h3>Mis pedidos</h3>
            <p>Consulta tu historial de compras</p>
        </div>
    </div>
    <div class="card-content">
        <div class="admin-options">
            <div class="admin-option">
                <i class="fas fa-clock"></i>
                <span>Pedidos pendientes y en proceso</span>
            </div>
            <div class="admin-option">
                <i class="fas fa-calendar-alt"></i>
                <span>Fechas de Transacción</span>
            </div>
            <div class="admin-option">
                <i class="fas fa-check-circle"></i>
                <span>Historial de pedidos completados</span>
            </div>
        </div>
    </div>
    <div class="card-actions">
        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=orderHistory" class="settings-btn btn-primary">
            <i class="fas fa-history"></i>
            Ver pedidos
        </a>
    </div>
</div>

                <?php if ($isAdmin): ?>
                    <!-- Category Management Card -->
                    <div class="settings-card admin-card">
                        <div class="card-header">
                            <div class="card-icon admin-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="card-title">
                                <h3>Gestión de Categorías</h3>
                                <p>Administrar las categorías</p>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="admin-options">
                                <div class="admin-option">
                                    <i class="fas fa-list"></i>
                                    <span>Ver todas las categorías</span>
                                </div>
                                <div class="admin-option">
                                    <i class="fas fa-plus"></i>
                                    <span>Añadir categoría</span>
                                </div>
                                <div class="admin-option">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar y eliminar categorías</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=index" class="settings-btn btn-admin">
                                <i class="fas fa-cog"></i>
                                Gestionar categorías
                            </a>
                        </div>
                    </div>

                    <!-- Product Management Card -->
                    <div class="settings-card admin-card">
                        <div class="card-header">
                            <div class="card-icon admin-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="card-title">
                                <h3>Gestión de productos</h3>
                                <p>Administrar los productos</p>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="admin-options">
                                <div class="admin-option">
                                    <i class="fas fa-list"></i>
                                    <span>Ver todos los productos</span>
                                </div>
                                <div class="admin-option">
                                    <i class="fas fa-plus"></i>
                                    <span>Añadir productos</span>
                                </div>
                                <div class="admin-option">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar y eliminar productos</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="settings-btn btn-admin">
                                <i class="fas fa-plus"></i>
                                Gestionar productos
                            </a>
                        </div>
                    </div>

                    <!-- User Management Card -->
                    <div class="settings-card admin-card">
                        <div class="card-header">
                            <div class="card-icon admin-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="card-title">
                                <h3>Gestión de usuarios</h3>
                                <p>Administrar cuentas de usuarios</p>
                            </div>
                        </div>
                        <div class="card-content">
                            <p class="card-description">Revisa y administra las cuentas de los usuarios registrados.</p>
                        </div>
                        <div class="card-actions">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=listUsers" class="settings-btn btn-admin">
                                <i class="fas fa-list"></i>
                                Ver usuarios
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Logout Card -->
                <div class="settings-card logout-card">
                    <div class="card-header">
                        <div class="card-icon logout-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="card-title">
                            <h3>Cerrar sesión</h3>
                            <p>Salir de tu cuenta de forma segura</p>
                        </div>
                    </div>
                    <div class="card-content">
                        <p class="card-description">¿Estás seguro de que quieres cerrar sesión? Tendrás que volver a iniciar sesión para acceder a tu cuenta.</p>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="settings-btn btn-logout" onclick="confirmLogout()">
                            <i class="fas fa-door-open"></i>
                            Cerrar sesión
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar cierre de sesión</h3>
            <button type="button" class="modal-close" onclick="closeLogoutModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <p>¿Estás seguro de que quieres cerrar tu sesión?</p>
            <p class="modal-subtitle">Tendrás que volver a iniciar sesión para acceder a tu cuenta.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-btn btn-cancel" onclick="closeLogoutModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=logout" class="modal-btn btn-confirm">
                <i class="fas fa-check"></i>
                Sí, cerrar sesión
            </a>
        </div>
    </div>
</div>
<script src="<?php echo ASSETS_URL; ?>js/user/userSettings.js"></script>