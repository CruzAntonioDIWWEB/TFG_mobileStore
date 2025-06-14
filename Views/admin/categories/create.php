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
$categories = $categories ?? [];
$accessoryTypes = $accessoryTypes ?? [];

// Find the "Móvil" category ID
$mobileCategoryId = null;
foreach ($categories as $category) {
    if (stripos($category['name'], 'móvil') !== false || 
        stripos($category['name'], 'teléfono') !== false) {
        $mobileCategoryId = $category['id'];
        break;
    }
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

<!-- Create Product Section -->
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
            <span class="breadcrumb-current">Nuevo Producto</span>
        </div>

        <!-- Create Product Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">
                    <i class="fas fa-plus"></i>
                    Nuevo Producto
                </h1>
                <p class="auth-subtitle">Añade un nuevo producto al catálogo de la tienda</p>
            </div>

            <form class="auth-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=product&action=save" enctype="multipart/form-data">
                
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
                              rows="4"></textarea>
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
                                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Mobile Brand Selection (Conditional) -->
                <div class="form-group" id="mobile-brand-group" style="display: none;">
                    <label for="mobile_brand" class="form-label">
                        <i class="fas fa-mobile-alt"></i>
                        Marca del Móvil
                    </label>
                    <select id="mobile_brand" 
                            name="mobile_brand" 
                            class="form-input form-select">
                        <option value="">Selecciona la marca</option>
                        <option value="iphone">iPhone</option>
                        <option value="samsung">Samsung</option>
                        <option value="xiaomi">Xiaomi</option>
                        <option value="huawei">Huawei</option>
                        <option value="google">Google Pixel</option>
                        <option value="oppo">Oppo</option>
                        <option value="other">Otra marca</option>
                    </select>
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i>
                        Esta información se usa para filtrar los móviles por marca en la tienda
                    </small>
                </div>

                <!-- Accessory Type Selection (Only for accessories) -->
                <div class="form-group" id="accessory-type-group" style="display: none;">
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
                                <option value="<?php echo htmlspecialchars($type['id']); ?>">
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Product Image -->
                <div class="form-group">
                    <label for="image" class="form-label">
                        <i class="fas fa-image"></i>
                        Imagen del Producto
                    </label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           class="form-input form-file" 
                           accept="image/*">
                    <small class="form-help">Formatos permitidos: JPG, PNG, GIF (máximo 5MB)</small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Crear Producto
                    </button>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
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
            // Check if it's an accessory category (you can modify this logic as needed)
            // For now, we assume any non-mobile category might need accessory type
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
</script>