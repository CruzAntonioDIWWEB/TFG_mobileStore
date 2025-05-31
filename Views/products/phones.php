<?php
// Get messages for display
$messages = $messages ?? [];
?>

<!-- Messages Display -->
<?php if (!empty($messages['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($messages['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($messages['error'])): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($messages['error']); ?>
    </div>
<?php endif; ?>

<!-- Page Header -->
<section class="catalog-header">
    <div class="catalog-header-container">
        <h1 class="catalog-title">Móviles</h1>
        <p class="catalog-description">Descubre nuestra amplia selección de teléfonos móviles de las mejores marcas</p>
    </div>
</section>

<!-- Brand Filters -->
<section class="brand-filters">
    <div class="filters-container">
        <h2 class="filters-title">Filtrar por marca</h2>
        <div class="brand-buttons">
            <button class="brand-btn active" data-brand="all">
                <span class="brand-text">Todos</span>
            </button>
            <button class="brand-btn" data-brand="apple">
                <span class="brand-text">Apple</span>
            </button>
            <button class="brand-btn" data-brand="samsung">
                <span class="brand-text">Samsung</span>
            </button>
            <button class="brand-btn" data-brand="xiaomi">
                <span class="brand-text">Xiaomi</span>
            </button>
            <button class="brand-btn" data-brand="huawei">
                <span class="brand-text">Huawei</span>
            </button>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="phones-catalog">
    <div class="catalog-container">
        <?php if (!empty($phones) && is_array($phones)): ?>
            <div class="phones-grid" id="phones-grid">
                <?php foreach ($phones as $phone): ?>
                    <article class="phone-card" data-brand="<?php echo strtolower(htmlspecialchars($phone['name'])); ?>">
                        <div class="phone-image">
                            <?php if (!empty($phone['image'])): ?>
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($phone['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($phone['name']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <img src="/dashboard/TFG/assets/img/placeholder-product.jpg" 
                                     alt="<?php echo htmlspecialchars($phone['name']); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <!-- Stock Badge -->
                            <?php if ($phone['stock'] > 0): ?>
                                <span class="stock-badge">
                                    <?php if ($phone['stock'] <= 5): ?>
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Quedan <?php echo $phone['stock']; ?>
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
                        
                        <div class="phone-info">
                            <h3 class="phone-name"><?php echo htmlspecialchars($phone['name']); ?></h3>
                            
                            <?php if (!empty($phone['description'])): ?>
                                <p class="phone-description">
                                    <?php echo htmlspecialchars(substr($phone['description'], 0, 120)) . (strlen($phone['description']) > 120 ? '...' : ''); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="phone-footer">
                                <div class="price-section">
                                    <span class="phone-price"><?php echo number_format($phone['price'], 2, ',', '.'); ?> €</span>
                                </div>
                                
                                <div class="phone-actions">
                                    <a href="index.php?controller=product&action=detail&id=<?php echo $phone['id']; ?>" 
                                       class="phone-btn btn-details">
                                        <i class="fas fa-eye"></i>
                                        Ver detalles
                                    </a>
                                    
                                    <?php if ($phone['stock'] > 0): ?>
                                        <?php if (isset($_SESSION['user'])): ?>
                                            <button class="phone-btn btn-cart" 
                                                    data-product-id="<?php echo $phone['id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($phone['name']); ?>">
                                                <i class="fas fa-shopping-cart"></i>
                                                Añadir
                                            </button>
                                        <?php else: ?>
                                            <a href="index.php?controller=user&action=login" 
                                               class="phone-btn btn-login-required">
                                                <i class="fas fa-user"></i>
                                                Iniciar sesión
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="phone-btn btn-disabled" disabled>
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
            <div class="no-phones-message">
                <div class="no-phones-content">
                    <i class="fas fa-mobile-alt no-phones-icon"></i>
                    <h3 class="no-phones-title">No hay móviles disponibles</h3>
                    <p class="no-phones-text">En este momento no tenemos móviles en stock. Vuelve pronto para ver las novedades.</p>
                    <a href="index.php?controller=home&action=index" class="back-home-btn">
                        <i class="fas fa-home"></i>
                        Volver al inicio
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="../../assets/js/brandFiltering.js"></script>