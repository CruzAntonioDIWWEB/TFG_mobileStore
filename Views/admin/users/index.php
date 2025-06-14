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

<!-- Users Management Section -->
<section class="admin-section">
    <div class="admin-container">
        <!-- Back Navigation -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-arrow-left"></i>
                Volver a configuración
            </a>
        </div>
        
        <div class="admin-header">
            <div class="admin-title-section">
                <h1 class="admin-title">Gestión de Usuarios</h1>
                <p class="admin-subtitle">Administra los usuarios de la tienda</p>
            </div>
            <div class="admin-actions">
                <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=create" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nuevo Usuario
                </a>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Listado de Usuarios</h2>
                        <p>Total de usuarios: <?php echo count($users ?? []); ?></p>
                    </div>
                </div>

                <div class="card-content">
                    <?php if (!empty($users)): ?>
                        <div class="table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td class="user-id">
                                                <span class="id-badge"><?php echo htmlspecialchars($user['id']); ?></span>
                                            </td>
                                            <td class="user-name">
                                                <div class="name-info">
                                                    <span class="full-name">
                                                        <?php echo htmlspecialchars($user['name'] . ' ' . $user['surnames']); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="user-email">
                                                <span class="email-text">
                                                    <?php echo htmlspecialchars($user['email']); ?>
                                                </span>
                                            </td>
                                            <td class="user-role">
                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <span class="role-badge admin-role">
                                                        <i class="fas fa-crown"></i>
                                                        Administrador
                                                    </span>
                                                <?php else: ?>
                                                    <span class="role-badge client-role">
                                                        <i class="fas fa-user"></i>
                                                        Cliente
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="user-actions">
                                                <div class="action-buttons">
                                                    <!-- Edit Button -->
                                                    <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=edit&id=<?php echo $user['id']; ?>" 
                                                       class="action-btn edit-btn" 
                                                       title="Editar usuario">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>

                                                    <!-- Delete Button - Only if not current user -->
                                                    <?php if ($user['id'] != $currentUser['id']): ?>
                                                        <form method="POST" 
                                                              action="<?php echo BASE_URL; ?>index.php?controller=user&action=delete" 
                                                              class="delete-form" 
                                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario? Esta acción no se puede deshacer.');">
                                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                            <button type="submit" class="action-btn delete-btn" title="Eliminar usuario">
                                                                <i class="fas fa-trash"></i>
                                                                <span>Eliminar</span>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="action-btn disabled-btn" title="No puedes eliminar tu propia cuenta">
                                                            <i class="fas fa-lock"></i>
                                                            <span>Protegido</span>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>No hay usuarios registrados</h3>
                            <p>Aún no se han registrado usuarios en el sistema.</p>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=create" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Crear primer usuario
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>