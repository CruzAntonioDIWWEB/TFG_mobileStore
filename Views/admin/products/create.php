<?php
// Get current user data
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

// Get messages for display
$messages = $messages ?? [];

// Get data passed from controller
$categories = $categories ?? [];
$accessoryTypes = $accessoryTypes ?? [];
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
                        Nombre del producto
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-input" 
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
                              maxlength="255"></textarea>
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
                                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-hint">Categoría principal del producto.</small>
                </div>

                <!-- Mobile Brand Selection (Initially Hidden) -->
                <div class="form-group">
                    <label for="mobile_brand" class="form-label">
                        <i class="fas fa-mobile-alt"></i>
                        Marca del Móvil (Opcional)
                    </label>
                    <select id="mobile_brand" name="mobile_brand" class="form-select">
                        <option value="">Selecciona la marca</option>
                        <option value="iphone">iPhone</option>
                        <option value="samsung">Samsung</option>
                        <option value="xiaomi">Xiaomi</option>
                        <option value="huawei">Huawei</option>
                        <option value="google">Google Pixel</option>
                        <option value="oppo">Oppo</option>
                        <option value="other">Otra marca</option>
                    </select>
                    <small class="form-hint">Solo si el producto es un móvil.</small>
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
                                <option value="<?php echo htmlspecialchars($accessoryType['id']); ?>">
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
                        Imagen del Producto
                    </label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           class="form-input form-file" 
                           accept="image/*">
                    <small class="form-help">Formatos permitidos: JPG, PNG, GIF (máximo 5MB)</small>
                </div>

                <!-- Form Buttons -->
                <div class="form-buttons">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=index" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Crear Producto
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/productFormValidation.js"></script>
