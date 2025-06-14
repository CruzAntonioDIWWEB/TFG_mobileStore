<?php
// Include the BrandHelper for brand detection
require_once __DIR__ . '/../../../Helpers/BrandHelper.php';

use Helpers\BrandHelper;

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

// Detect current brand if it's a mobile product 
$currentBrand = '';
if ($productName) {
    $currentBrand = BrandHelper::detectBrand($productName);
}
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

                    <!-- Hidden Product ID -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($productId); ?>">

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
                                max="999.99"
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

                    <!-- Mobile Brand Selection -->
                    <div class="form-group">
                        <label for="mobile_brand" class="form-label">
                            <i class="fas fa-mobile-alt"></i>
                            Marca del Móvil (Opcional)
                        </label>
                        <select id="mobile_brand" name="mobile_brand" class="form-select">
                            <option value="">Selecciona la marca</option>
                            <option value="iphone" <?php echo ($currentBrand === 'iphone') ? 'selected' : ''; ?>>iPhone</option>
                            <option value="samsung" <?php echo ($currentBrand === 'samsung') ? 'selected' : ''; ?>>Samsung</option>
                            <option value="xiaomi" <?php echo ($currentBrand === 'xiaomi') ? 'selected' : ''; ?>>Xiaomi</option>
                            <option value="huawei" <?php echo ($currentBrand === 'huawei') ? 'selected' : ''; ?>>Huawei</option>
                            <option value="google" <?php echo ($currentBrand === 'google') ? 'selected' : ''; ?>>Google Pixel</option>
                            <option value="oppo" <?php echo ($currentBrand === 'oppo') ? 'selected' : ''; ?>>Oppo</option>
                            <option value="other" <?php echo ($currentBrand === 'other') ? 'selected' : ''; ?>>Otra marca</option>
                        </select>
                        <small class="form-hint">Solo si el producto es un móvil.</small>
                    </div>

                    <!-- Accessory Type Selection -->
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

                    <!-- Current Image Display -->
                    <?php if (!empty($productImage)): ?>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image"></i>
                                Imagen Actual
                            </label>
                            <div class="current-image-container" style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 2px dashed #CCCCCC; border-radius: 5px; background-color: #F2F2F2;">
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($productImage); ?>"
                                    alt="<?php echo htmlspecialchars($productName); ?>"
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px; border: 1px solid #CCCCCC;">
                                <p style="margin: 0; color: #666666; font-size: 0.9rem;"><?php echo htmlspecialchars($productImage); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Product Image -->
                    <div class="form-group">
                        <label for="image" class="form-label">
                            <i class="fas fa-image"></i>
                            <?php echo !empty($productImage) ? 'Cambiar Imagen' : 'Imagen del Producto'; ?>
                        </label>
                        <input type="file"
                            id="image"
                            name="image"
                            class="form-input form-file"
                            accept="image/*">
                        <small class="form-help">
                            <?php echo !empty($productImage) ? 'Deja vacío para mantener la imagen actual. ' : ''; ?>
                            Formatos permitidos: JPG, PNG, GIF (máximo 5MB)
                        </small>
                    </div>

                    <!-- Form Buttons -->
                    <div class="form-buttons">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                    </div>

                </form>
            <?php else: ?>
                <div class="error-message" style="text-align: center; padding: 40px 20px; color: #E60000;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3 style="margin: 10px 0; color: #333333;">Producto no encontrado</h3>
                    <p style="margin-bottom: 20px; color: #666666;">El producto que intentas editar no existe o ha sido eliminado.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Productos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/productFormValidation.js"></script>