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

<!-- Page Header -->
<section class="catalog-header">
    <div class="catalog-header-container">
        <h1 class="catalog-title">Accesorios</h1>
        <p class="catalog-description">Encuentra los mejores accesorios para tu móvil: fundas, cargadores, auriculares y mucho más</p>
    </div>
</section>

<!-- Type Filters -->
<section class="type-filters">
    <div class="filters-container">
        <h2 class="filters-title">Filtrar por tipo</h2>
        <div class="select-wrapper">
            <select class="type-select" id="accessory-type-filter">
                <option value="all" <?php echo (!$selectedType || $selectedType === 'all') ? 'selected' : ''; ?>>
                    Todos los accesorios
                </option>
                <?php if (!empty($accessoryTypes) && is_array($accessoryTypes)): ?>
                    <?php foreach ($accessoryTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>" 
                                <?php echo ($selectedType == $type['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <i class="fas fa-chevron-down select-icon"></i>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="accessories-catalog">
    <div class="catalog-container">
        <?php if (!empty($accessories) && is_array($accessories)): ?>
            <div class="accessories-grid" id="accessories-grid">
                <?php foreach ($accessories as $accessory): ?>
                    <article class="accessory-card" data-type="<?php echo $accessory['accessory_type_id'] ?? 'none'; ?>">
                        <div class="accessory-image">
                            <?php if (!empty($accessory['image'])): ?>
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($accessory['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($accessory['name']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <img src="/dashboard/TFG/assets/img/placeholder-product.jpg" 
                                     alt="<?php echo htmlspecialchars($accessory['name']); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <!-- Stock Badge -->
                            <?php if ($accessory['stock'] > 0): ?>
                                <span class="stock-badge">
                                    <?php if ($accessory['stock'] <= 5): ?>
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Quedan <?php echo $accessory['stock']; ?>
                                    <?php else: ?>
                                        <i class="fas fa-check"></i>
                                        En stock
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="stock-badge out-of-stock">
                                    <i class="fas fa-times"></i>
                                    Agotado
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="accessory-info">
                            <h3 class="accessory-name"><?php echo htmlspecialchars($accessory['name']); ?></h3>
                            
                            <?php if (!empty($accessory['description'])): ?>
                                <p class="accessory-description">
                                    <?php echo htmlspecialchars(substr($accessory['description'], 0, 100)) . (strlen($accessory['description']) > 100 ? '...' : ''); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="accessory-footer">
                                <div class="price-section">
                                    <span class="accessory-price"><?php echo number_format($accessory['price'], 2, ',', '.'); ?> €</span>
                                </div>
                                
                                <div class="accessory-actions">
                                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $accessory['id']; ?>" 
                                       class="accessory-btn btn-details">
                                        <i class="fas fa-eye"></i>
                                        Ver detalles
                                    </a>
                                    
                                    <?php if ($accessory['stock'] > 0): ?>
                                        <?php if (isset($_SESSION['user'])): ?>
                                            <button class="accessory-btn btn-cart" 
                                                    data-product-id="<?php echo $accessory['id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($accessory['name']); ?>">
                                                <i class="fas fa-shopping-cart"></i>
                                                Añadir
                                            </button>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=login" 
                                               class="accessory-btn btn-login-required">
                                                <i class="fas fa-user"></i>
                                                Iniciar sesión
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="accessory-btn btn-disabled" disabled>
                                            <i class="fas fa-ban"></i>
                                            No disponible
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-accessories-message">
                <div class="no-accessories-content">
                    <i class="fas fa-plug no-accessories-icon"></i>
                    <h3 class="no-accessories-title">No hay accesorios disponibles</h3>
                    <p class="no-accessories-text">En este momento no tenemos accesorios en stock. Vuelve pronto para ver las novedades.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="back-home-btn">
                        <i class="fas fa-home"></i>
                        Volver al inicio
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/accessoryFiltering.js"></script>
<script src="<?php echo ASSETS_URL; ?>js/cart/addToCart.js"></script>