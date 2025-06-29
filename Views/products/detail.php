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

<!-- Product Detail Section -->
<section class="product-detail-section">
    <div class="detail-container">

        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb-nav">
            <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="breadcrumb-link">
                <i class="fas fa-home"></i>
                Inicio
            </a>

            <?php if ($product->isPhone()): ?>
                <span class="breadcrumb-separator">></span>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="breadcrumb-link">
                    Móviles
                </a>
            <?php elseif ($product->isAccessory()): ?>
                <span class="breadcrumb-separator">></span>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=accessories" class="breadcrumb-link">
                    Accesorios
                </a>
            <?php endif; ?>

            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current"><?php echo htmlspecialchars($product->getName()); ?></span>
        </nav>

        <!-- Main Product Detail -->
        <div class="product-detail-main">

            <!-- Product Image Section -->
            <div class="product-image-section">
                <div class="main-image-container">
                    <?php if (!empty($product->getImage())): ?>
                        <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($product->getImage()); ?>"
                            alt="<?php echo htmlspecialchars($product->getName()); ?>"
                            class="main-product-image"
                            id="mainProductImage">
                    <?php else: ?>
                        <img src="/dashboard/TFG/assets/img/placeholder-product.jpg"
                            alt="<?php echo htmlspecialchars($product->getName()); ?>"
                            class="main-product-image"
                            id="mainProductImage">
                    <?php endif; ?>

                    <!-- Stock Badge -->
                    <?php if ($product->getStock() > 0): ?>
                        <div class="stock-badge <?php echo ($product->getStock() <= 5) ? 'low-stock' : ''; ?>">
                            <?php if ($product->getStock() <= 5): ?>
                                <i class="fas fa-exclamation-triangle"></i>
                                ¡Últimas <?php echo $product->getStock(); ?> unidades!
                            <?php else: ?>
                                <i class="fas fa-check"></i>
                                En stock (<?php echo $product->getStock(); ?> disponibles)
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="stock-badge out-of-stock">
                            <i class="fas fa-times"></i>
                            Agotado
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="product-info-section">

                <!-- Product Header -->
                <div class="product-header">
                    <h1 class="product-title"><?php echo htmlspecialchars($product->getName()); ?></h1>

                    <!-- Category and Type Info -->
                    <div class="product-meta">
                        <?php if ($product->getCategory()): ?>
                            <span class="product-category">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($product->getCategory()); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($product->getAccessoryTypeName()): ?>
                            <span class="product-type">
                                <i class="fas fa-layer-group"></i>
                                <?php echo htmlspecialchars($product->getAccessoryTypeName()); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Description -->
                <?php if (!empty($product->getDescription())): ?>
                    <div class="product-description">
                        <h3 class="description-title">Descripción</h3>
                        <p class="description-text"><?php echo nl2br(htmlspecialchars($product->getDescription())); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Price Section -->
                <div class="price-section">
                    <div class="price-container">
                        <span class="current-price"><?php echo $product->formatPrice(); ?></span>
                    </div>
                </div>

                <!-- Add to Cart Section  -->
                <div class="add-to-cart-section">
                    <?php if ($product->getStock() > 0): ?>
                        <?php if (isset($_SESSION['user'])): ?>
                            <form class="add-to-cart-form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=cart&action=addToCart">
                                <input type="hidden" name="product_id" value="<?php echo $product->getId(); ?>">

                                <div class="quantity-selector">
                                    <label for="quantity" class="quantity-label">Cantidad:</label>
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn quantity-minus">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number"
                                            id="quantity"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="<?php echo $product->getStock(); ?>"
                                            class="quantity-input">
                                        <button type="button" class="quantity-btn quantity-plus">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                    Añadir al carrito
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="login-message">
                                <p class="message-text">Inicia sesión para añadir productos al carrito</p>
                                <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=showLogin" class="login-link">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Iniciar sesión
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="out-of-stock-message">
                            <i class="fas fa-times-circle"></i>
                            Este producto está agotado
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>

    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/productDetail.js"></script>