<?php
// Get current user data
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

// Get messages for display
$messages = $messages ?? [];

// Get data passed from controller
$product = $product ?? null;
$categories = $categories ?? [];
$accessoryTypes = $accessoryTypes ?? [];

// Product data
$productId = $product ? $product->getId() : null;
$productName = $product ? $product->getName() : '';
$productDescription = $product ? $product->getDescription() : '';
$productPrice = $product ? $product->getPrice() : '';
$productStock = $product ? $product->getStock() : '';
$productCategoryId = $product ? $product->getCategoryId() : '';
$productAccessoryTypeId = $product ? $product->getAccessoryType() : '';
$productImage = $product ? $product->getImage() : '';
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

<!-- Edit Product Section -->
<section class="auth-section">
    <div class="auth-container">
        
        <!-- Navigation Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="breadcrumb-link">
                <i class="fas fa-user"></i>
                Perfil
            </a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="breadcrumb-link">
                <i class="fas fa-box"></i>
                Productos
            </a>
            <i class="fas fa-chevron-right"></i>
            <span class="breadcrumb-current">Editar Producto</span>
        </div>

        <!-- Edit Product Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">
                    <i class="fas fa-edit"></i>
                    Editar Producto
                </h1>
                <p class="auth-subtitle">Modifica los datos del producto: <strong><?php echo htmlspecialchars($productName); ?></strong></p>
            </div>

            <?php if ($product): ?>
                <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=product&action=update" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($productId); ?>">
                    
                    <!-- Current Product Image (if exists) -->
                    <?php if (!empty($productImage)): ?>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image"></i>
                                Imagen actual
                            </label>
                            <div style="margin-bottom: 1rem;">
                                <img src="<?php echo ASSETS_URL; ?>img/products/<?php echo htmlspecialchars($productImage); ?>" 
                                     alt="<?php echo htmlspecialchars($productName); ?>"
                                     style="max-width: 150px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Product Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fas fa-tag"></i>
                            Nombre del producto
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($productName); ?>"
                               placeholder="Ej: iPhone 15 Pro, Samsung Galaxy S24..."
                               required
                               maxlength="25"
                               autocomplete="off">
                        <small class="form-hint">Máximo 25 caracteres.</small>
                    </div>

                    <!-- Product Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-textarea" 
                                  placeholder="Describe las características principales del producto..."
                                  required
                                  maxlength="255"><?php echo htmlspecialchars($productDescription); ?></textarea>
                        <small class="form-hint">Máximo 255 caracteres.</small>
                    </div>

                    <!-- Price and Stock Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <!-- Price -->
                        <div class="form-group">
                            <label for="price" class="form-label">
                                <i class="fas fa-euro-sign"></i>
                                Precio
                            </label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   class="form-input" 
                                   value="<?php echo htmlspecialchars($productPrice); ?>"
                                   placeholder="99.99"
                                   step="0.01"
                                   min="0"
                                   required>
                            <small class="form-hint">En euros (€).</small>
                        </div>

                        <!-- Stock -->
                        <div class="form-group">
                            <label for="stock" class="form-label">
                                <i class="fas fa-boxes"></i>
                                Stock
                            </label>
                            <input type="number" 
                                   id="stock" 
                                   name="stock" 
                                   class="form-input" 
                                   value="<?php echo htmlspecialchars($productStock); ?>"
                                   placeholder="0"
                                   min="0"
                                   required>
                            <small class="form-hint">Unidades disponibles.</small>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="category_id" class="form-label">
                            <i class="fas fa-folder"></i>
                            Categoría
                        </label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Selecciona una categoría</option>
                            <?php if (!empty($categories) && is_array($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                            <?php echo ($category['id'] == $productCategoryId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-hint">Categoría principal del producto.</small>
                    </div>

                    <!-- Accessory Type (Optional) -->
                    <div class="form-group">
                        <label for="accessory_type_id" class="form-label">
                            <i class="fas fa-puzzle-piece"></i>
                            Tipo de Accesorio (Opcional)
                        </label>
                        <select id="accessory_type_id" name="accessory_type_id" class="form-select">
                            <option value="">Selecciona tipo (si es accesorio)</option>
                            <?php if (!empty($accessoryTypes) && is_array($accessoryTypes)): ?>
                                <?php foreach ($accessoryTypes as $accessoryType): ?>
                                    <option value="<?php echo htmlspecialchars($accessoryType['id']); ?>"
                                            <?php echo ($accessoryType['id'] == $productAccessoryTypeId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($accessoryType['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-hint">Solo si el producto es un accesorio.</small>
                    </div>

                    <!-- Product Image -->
                    <div class="form-group">
                        <label for="image" class="form-label">
                            <i class="fas fa-image"></i>
                            <?php echo !empty($productImage) ? 'Cambiar imagen (Opcional)' : 'Imagen del producto (Opcional)'; ?>
                        </label>
                        <div class="form-file">
                            <div class="file-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p><?php echo !empty($productImage) ? 'Subir nueva imagen (la actual será reemplazada)' : 'Arrastra una imagen aquí o haz clic para seleccionar'; ?></p>
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif">
                        </div>
                        <small class="form-hint">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB.</small>
                    </div>

                    <!-- Form Buttons -->
                    <div class="form-buttons">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Actualizar Producto
                        </button>
                    </div>

                </form>
            <?php else: ?>
                <!-- Error State -->
                <div class="error-state">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Producto no encontrado</h3>
                    <p>El producto que intentas editar no existe o ha sido eliminado.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Volver a productos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<script src="<?php echo ASSETS_URL; ?>js/productFormValidation.js"></script>