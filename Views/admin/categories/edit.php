<?php
// Include the BrandHelper
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

// Find the "Móvil" category ID
$mobileCategoryId = null;
foreach ($categories as $category) {
    if (stripos($category['name'], 'móvil') !== false || 
        stripos($category['name'], 'teléfono') !== false) {
        $mobileCategoryId = $category['id'];
        break;
    }
}

// Detect current brand if it's a mobile product
$currentBrand = '';
$isMobileProduct = ($productCategoryId == $mobileCategoryId);
if ($isMobileProduct && $productName) {
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
                            Nombre del Producto
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($productName); ?>"
                               placeholder="Ej: iPhone 15 Pro, Samsung Galaxy S24, etc." 
                               required 
                               maxlength="25">
                        <small class="form-help">Máximo 25 caracteres</small>
                    </div>

                    <!-- Product Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-input form-textarea" 
                                  placeholder="Describe las características principales del producto..." 
                                  maxlength="255" 
                                  rows="4"><?php echo htmlspecialchars($productDescription); ?></textarea>
                        <small class="form-help">Máximo 255 caracteres</small>
                    </div>

                    <!-- Product Price -->
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
                               placeholder="Ej: 699.99" 
                               step="0.01" 
                               min="0" 
                               max="999.99" 
                               required>
                        <small class="form-help">Precio en euros (máximo 999.99 €)</small>
                    </div>

                    <!-- Product Stock -->
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
                               placeholder="Ej: 50" 
                               min="0" 
                               required>
                        <small class="form-help">Cantidad disponible en inventario</small>
                    </div>

                    <!-- Category Selection -->
                    <div class="form-group">
                        <label for="category_id" class="form-label">
                            <i class="fas fa-folder"></i>
                            Categoría
                        </label>
                        <select id="category_id" 
                                name="category_id" 
                                class="form-input form-select" 
                                required>
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
                    </div>

                    <!-- Mobile Brand Selection (Conditional) -->
                    <div class="form-group" id="mobile-brand-group" style="display: <?php echo $isMobileProduct ? 'block' : 'none'; ?>;">
                        <label for="mobile_brand" class="form-label">
                            <i class="fas fa-mobile-alt"></i>
                            Marca del Móvil
                        </label>
                        <select id="mobile_brand" 
                                name="mobile_brand" 
                                class="form-input form-select"
                                <?php echo $isMobileProduct ? 'required' : ''; ?>>
                            <option value="">Selecciona la marca</option>
                            <option value="iphone" <?php echo ($currentBrand === 'iphone') ? 'selected' : ''; ?>>iPhone</option>
                            <option value="samsung" <?php echo ($currentBrand === 'samsung') ? 'selected' : ''; ?>>Samsung</option>
                            <option value="xiaomi" <?php echo ($currentBrand === 'xiaomi') ? 'selected' : ''; ?>>Xiaomi</option>
                            <option value="huawei" <?php echo ($currentBrand === 'huawei') ? 'selected' : ''; ?>>Huawei</option>
                            <option value="google" <?php echo ($currentBrand === 'google') ? 'selected' : ''; ?>>Google Pixel</option>
                            <option value="oppo" <?php echo ($currentBrand === 'oppo') ? 'selected' : ''; ?>>Oppo</option>
                            <option value="other" <?php echo ($currentBrand === 'other') ? 'selected' : ''; ?>>Otra marca</option>
                        </select>
                        <small class="form-help">
                            <i class="fas fa-info-circle"></i>
                            Esta información se usa para filtrar los móviles por marca en la tienda
                        </small>
                    </div>

                    <!-- Accessory Type Selection (Only for accessories) -->
                    <div class="form-group" id="accessory-type-group" style="display: <?php echo (!$isMobileProduct && $productAccessoryTypeId) ? 'block' : 'none'; ?>;">
                        <label for="accessory_type_id" class="form-label">
                            <i class="fas fa-puzzle-piece"></i>
                            Tipo de Accesorio
                        </label>
                        <select id="accessory_type_id" 
                                name="accessory_type_id" 
                                class="form-input form-select">
                            <option value="">Selecciona el tipo de accesorio</option>
                            <?php if (!empty($accessoryTypes) && is_array($accessoryTypes)): ?>
                                <?php foreach ($accessoryTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['id']); ?>"
                                            <?php echo ($type['id'] == $productAccessoryTypeId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Current Image Display -->
                    <?php if (!empty($productImage)): ?>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image"></i>
                                Imagen Actual
                            </label>
                            <div class="current-image-container">
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($productImage); ?>" 
                                     alt="<?php echo htmlspecialchars($productName); ?>"
                                     class="current-product-image">
                                <p class="current-image-name"><?php echo htmlspecialchars($productImage); ?></p>
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

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=show&id=<?php echo $productId; ?>" 
                           class="btn btn-outline" target="_blank">
                            <i class="fas fa-eye"></i>
                            Ver Producto
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Producto no encontrado</h3>
                    <p>El producto que intentas editar no existe o ha sido eliminado.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Productos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- JavaScript for Conditional Fields -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const mobileBrandGroup = document.getElementById('mobile-brand-group');
    const accessoryTypeGroup = document.getElementById('accessory-type-group');
    const mobileBrandSelect = document.getElementById('mobile_brand');
    const accessoryTypeSelect = document.getElementById('accessory_type_id');
    
    // Mobile category ID from PHP
    const mobileCategoryId = '<?php echo $mobileCategoryId; ?>';
    
    function toggleConditionalFields() {
        const selectedCategoryId = categorySelect.value;
        
        // Reset fields
        mobileBrandGroup.style.display = 'none';
        accessoryTypeGroup.style.display = 'none';
        mobileBrandSelect.removeAttribute('required');
        accessoryTypeSelect.removeAttribute('required');
        
        if (selectedCategoryId === mobileCategoryId) {
            // Show mobile brand selection
            mobileBrandGroup.style.display = 'block';
            mobileBrandSelect.setAttribute('required', 'required');
            
            // Add animation
            mobileBrandGroup.style.opacity = '0';
            setTimeout(() => {
                mobileBrandGroup.style.opacity = '1';
            }, 100);
            
        } else if (selectedCategoryId) {
            // Check if it's an accessory category
            const selectedCategoryName = categorySelect.options[categorySelect.selectedIndex].text.toLowerCase();
            
            if (selectedCategoryName.includes('accesorio') || 
                selectedCategoryName.includes('funda') || 
                selectedCategoryName.includes('cargador')) {
                accessoryTypeGroup.style.display = 'block';
                
                // Add animation
                accessoryTypeGroup.style.opacity = '0';
                setTimeout(() => {
                    accessoryTypeGroup.style.opacity = '1';
                }, 100);
            }
        }
    }
    
    // Listen for category changes
    categorySelect.addEventListener('change', toggleConditionalFields);
    
    // Check initial state
    toggleConditionalFields();
});

// Form validation enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.auth-form');
    const nameInput = document.getElementById('name');
    const mobileBrandSelect = document.getElementById('mobile_brand');
    const categorySelect = document.getElementById('category_id');
    
    form.addEventListener('submit', function(e) {
        const selectedCategoryId = categorySelect.value;
        const mobileCategoryId = '<?php echo $mobileCategoryId; ?>';
        
        // If mobile category is selected, validate brand selection
        if (selectedCategoryId === mobileCategoryId) {
            if (!mobileBrandSelect.value) {
                e.preventDefault();
                alert('Por favor selecciona la marca del móvil.');
                mobileBrandSelect.focus();
                return false;
            }
            
            // Additional validation: check if the product name matches the selected brand
            const productName = nameInput.value.toLowerCase();
            const selectedBrand = mobileBrandSelect.value;
            
            let nameMatchesBrand = false;
            
            switch(selectedBrand) {
                case 'iphone':
                    nameMatchesBrand = productName.includes('iphone');
                    break;
                case 'samsung':
                    nameMatchesBrand = productName.includes('samsung') || productName.includes('galaxy');
                    break;
                case 'xiaomi':
                    nameMatchesBrand = productName.includes('xiaomi') || productName.includes('redmi');
                    break;
                case 'huawei':
                    nameMatchesBrand = productName.includes('huawei');
                    break;
                case 'google':
                    nameMatchesBrand = productName.includes('pixel') || productName.includes('google');
                    break;
                case 'oppo':
                    nameMatchesBrand = productName.includes('oppo');
                    break;
                case 'other':
                    nameMatchesBrand = true; // Allow any name for "other" brands
                    break;
            }
            
            if (!nameMatchesBrand && selectedBrand !== 'other') {
                const brandName = mobileBrandSelect.options[mobileBrandSelect.selectedIndex].text;
                if (!confirm(`El nombre del producto no parece coincidir con la marca "${brandName}". ¿Estás seguro de que quieres continuar?`)) {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });
});

// Character count for description
document.addEventListener('DOMContentLoaded', function() {
    const descriptionTextarea = document.getElementById('description');
    const maxLength = 255;
    
    function updateCharacterCount() {
        const currentLength = descriptionTextarea.value.length;
        const helpText = descriptionTextarea.nextElementSibling;
        helpText.textContent = `${currentLength}/${maxLength} caracteres`;
        
        if (currentLength > maxLength * 0.9) {
            helpText.style.color = '#E60000';
        } else {
            helpText.style.color = '';
        }
    }
    
    descriptionTextarea.addEventListener('input', updateCharacterCount);
    updateCharacterCount(); // Initial count
});
</script>