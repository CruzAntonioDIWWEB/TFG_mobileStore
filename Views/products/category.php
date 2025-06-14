<?php
// Get data passed from controller
$products = $products ?? [];
$categoryName = $categoryName ?? 'Productos';
$categoryId = $categoryId ?? null;
?>

<!-- Category Products Section -->
<section class="accessories-catalog"> 
    <div class="catalog-container">

        <!-- Header -->
        <div class="catalog-header">
            <div class="catalog-title-section">
                <h1 class="catalog-title"><?php echo htmlspecialchars($categoryName); ?></h1>
            </div>
            <div class="catalog-info">
                <span class="product-count">
                    <?php
                        $count = count($products);
                        echo $count . ' producto' . ($count === 1 ? ' disponible' : 's disponibles');
                    ?>
                </span>
            </div>
        </div>

        <!-- Breadcrumb -->
        <?php if (!empty($products) && is_array($products)): ?>
            <div class="breadcrumb">
                <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="breadcrumb-link">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
                    <i class="fas fa-chevron-right"></i>
                </span>
                <span class="breadcrumb-current"><?php echo htmlspecialchars($categoryName); ?></span>
            </div>
        <?php endif; ?>

        <!-- Products Display -->
        <div class="products-content">
            <?php if (!empty($products) && is_array($products)): ?>
                <div class="accessories-grid">
                    <?php foreach ($products as $product): ?>
                        <article class="accessory-card">
                            <div class="accessory-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo ASSETS_URL; ?>img/products/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        loading="lazy">
                                <?php else: ?>
                                    <img src="<?php echo ASSETS_URL; ?>img/placeholder-product.jpg"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        loading="lazy">
                                <?php endif; ?>

                                <!-- Stock Badge -->
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="stock-badge">
                                        <?php if ($product['stock'] <= 5): ?>
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Quedan <?php echo $product['stock']; ?>
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
                                <h3 class="accessory-name"><?php echo htmlspecialchars($product['name']); ?></h3>

                                <?php if (!empty($product['description'])): ?>
                                    <p class="accessory-description">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?>
                                    </p>
                                <?php endif; ?>

                                <div class="accessory-footer">
                                    <div class="price-section">
                                        <span class="accessory-price"><?php echo number_format($product['price'], 2, ',', '.'); ?> €</span>
                                    </div>

                                    <div class="accessory-actions">
                                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $product['id']; ?>"
                                            class="accessory-btn btn-details">
                                            <i class="fas fa-eye"></i>
                                            Ver detalles
                                        </a>

                                        <?php if ($product['stock'] > 0): ?>
                                            <?php if (isset($_SESSION['user'])): ?>
                                                <button class="accessory-btn btn-cart"
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>">
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
                        <i class="fas fa-box-open no-accessories-icon"></i>
                        <h3 class="no-accessories-title">No hay productos disponibles</h3>
                        <p class="no-accessories-text">En este momento no hay productos en esta categoría.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/cart/addToCart.js"></script>