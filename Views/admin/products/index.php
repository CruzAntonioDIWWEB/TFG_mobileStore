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

<!-- Products Management Section -->
<section class="admin-section">
    <div class="admin-container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-arrow-left"></i>
                Volver a configuración
            </a>
        </div>

        <div class="admin-header">
            <div class="admin-title-section">
                <h1 class="admin-title">Gestión de Productos</h1>
                <p class="admin-subtitle">Administra el catálogo de productos de la tienda</p>
            </div>
            <div class="admin-actions">
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=create" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nuevo Producto
                </a>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Listado de Productos</h2>
                        <p>Total de productos: <?php echo count($products ?? []); ?></p>
                    </div>
                </div>

                <div class="card-content">
                    <?php if (!empty($products) && is_array($products)): ?>
                        <div class="table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <!-- Product ID -->
                                            <td class="product-id">
                                                <span class="id-badge">#<?php echo htmlspecialchars($product['id']); ?></span>
                                            </td>

                                            <!-- Product Image -->
                                            <td class="product-image">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="<?php echo ASSETS_URL; ?>img/products/<?php echo htmlspecialchars($product['image']); ?>"
                                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                        class="product-thumbnail">
                                                <?php else: ?>
                                                    <div class="no-image">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Product Name -->
                                            <td class="product-name">
                                                <div class="name-info">
                                                    <span class="name-text"><?php echo htmlspecialchars($product['name']); ?></span>
                                                    <?php if (!empty($product['description'])): ?>
                                                        <small class="description-text"><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Category -->
                                            <td class="product-category">
                                                <span class="category-badge">
                                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Sin categoría'); ?>
                                                </span>
                                            </td>

                                            <!-- Price -->
                                            <td class="product-price">
                                                <span class="price-amount">€<?php echo number_format($product['price'], 2); ?></span>
                                            </td>

                                            <!-- Stock -->
                                            <td class="product-stock">
                                                <?php if (isset($product['stock'])): ?>
                                                    <span class="stock-badge <?php echo ($product['stock'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                                                        <?php echo htmlspecialchars($product['stock']); ?> unidades
                                                    </span>
                                                    <span><?php echo $product['stock']; ?></span>
                                                <?php else: ?>
                                                    <span class="stock-badge out-of-stock">
                                                        Stock no disponible
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Date -->
                                            <td class="product-date">
                                                <?php if (!empty($product['created_at'])): ?>
                                                    <div class="date-info">
                                                        <span class="date"><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></span>
                                                        <span class="time"><?php echo date('H:i', strtotime($product['created_at'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="no-date">Sin fecha</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions -->
                                            <td class="product-actions">
                                                <div class="action-buttons">
                                                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>"
                                                        class="action-btn btn-edit"
                                                        title="Editar producto">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="<?php echo BASE_URL; ?>index.php?controller=product&action=delete"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit"
                                                            class="action-btn btn-delete"
                                                            title="Eliminar producto">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-products">
                            <div class="no-products-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3>No hay productos disponibles</h3>
                            <p>Aún no has añadido ningún producto al catálogo.</p>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=create" class="admin-btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Añadir Primer Producto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>