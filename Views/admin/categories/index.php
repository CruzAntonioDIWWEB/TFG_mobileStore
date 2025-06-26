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

<!-- Categories Management Section -->
<section class="admin-section">
    <div class="admin-container">
        <!-- Back Navigation -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-arrow-left"></i>
                Volver al Perfil
            </a>
        </div>

        <div class="admin-header">
            <div class="admin-title-section">
                <h1 class="admin-title">Gestión de Categorías</h1>
                <p class="admin-subtitle">Administra las categorías de productos de la tienda</p>
            </div>
            <div class="admin-actions">
                <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=create" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nueva Categoría
                </a>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Listado de Categorías</h2>
                        <p>Total de categorías: <?php echo count($categories ?? []); ?></p>
                    </div>
                </div>

                <div class="card-content">
                    <?php if (!empty($categories) && is_array($categories)): ?>
                        <div class="table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Fecha de Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td class="category-id">
                                                <span class="id-badge">#<?php echo htmlspecialchars($category['id']); ?></span>
                                            </td>
                                            <td class="category-name">
                                                <div class="name-info">
                                                    <span class="name-text"><?php echo htmlspecialchars($category['name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="category-date">
                                                <?php if (isset($category['created_at'])): ?>
                                                    <div class="date-info">
                                                        <span class="date"><?php echo date('d/m/Y', strtotime($category['created_at'])); ?></span>
                                                        <span class="time"><?php echo date('H:i', strtotime($category['created_at'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="no-date">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="category-actions">
                                                <div class="action-buttons">
                                                    <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=edit&id=<?php echo $category['id']; ?>"
                                                        class="action-btn btn-edit" title="Editar categoría">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="action-btn btn-delete"
                                                        title="Eliminar categoría"
                                                        onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-categories">
                            <div class="no-data-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h3>No hay categorías disponibles</h3>
                            <p>Aún no has creado ninguna categoría. Comienza agregando tu primera categoría.</p>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=category&action=create" class="create-first-btn">
                                <i class="fas fa-plus"></i>
                                Crear primera categoría
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar eliminación</h3>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p>¿Estás seguro de que quieres eliminar la categoría <strong id="category-name-to-delete"></strong>?</p>
            <p class="modal-subtitle">Esta acción no se puede deshacer. Si la categoría tiene productos asociados, no podrá ser eliminada.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-btn btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <form id="delete-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=category&action=delete" style="display: inline;">
                <input type="hidden" name="id" id="category-id-to-delete">
                <button type="submit" class="modal-btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Sí, eliminar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(categoryId, categoryName) {
        document.getElementById('category-id-to-delete').value = categoryId;
        document.getElementById('category-name-to-delete').textContent = categoryName;
        document.getElementById('delete-modal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>