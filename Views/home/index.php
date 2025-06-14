<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">Crusertel</h1>
            <p class="hero-subtitle">Tu tienda de confianza para móviles y accesorios</p>
            <div class="hero-buttons">
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="hero-btn btn-primary">Ver Móviles</a>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=accessories" class="hero-btn btn-secondary">Ver Accesorios</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?php echo ASSETS_URL; ?>img/example.jpg" class="hero-img">
        </div>
    </div>
</section>

<!-- Zigzag Section -->
<section class="zigzag-section">
    <article class="zigzag-container">
        <!-- Row 1: Image Left, Text Right -->
        <div class="zigzag-row">
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Lorem ipsum dolor</span>
                </div>
            </div>
            <div class="zigzag-content">
                <p class="zigzag-text">Lorem ipsum dolor sit amet et delectus accommodare his consul copiosae legendos at vix ad putent delectus delicata usu. Vidit dissentiet eos cu eum an brute</p>
            </div>
        </div>

        <!-- Row 2: Text Left, Image Right -->
        <div class="zigzag-row zigzag-row-reverse">
            <div class="zigzag-content">
                <p class="zigzag-text">Lorem ipsum dolor sit amet et delectus accommodare his consul copiosae legendos at vix ad putent delectus delicata usu. Vidit dissentiet eos cu eum an brute</p>
            </div>
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Lorem ipsum dolor</span>
                </div>
            </div>
        </div>

        <!-- Row 3: Image Left, Text Right -->
        <div class="zigzag-row">
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Lorem ipsum dolor</span>
                </div>
            </div>
            <div class="zigzag-content">
                <p class="zigzag-text">Lorem ipsum dolor sit amet et delectus accommodare his consul copiosae legendos at vix ad putent delectus delicata usu. Vidit dissentiet eos cu eum an brute</p>
            </div>
        </div>

        <!-- Row 4: Text Left, Image Right -->
        <div class="zigzag-row zigzag-row-reverse">
            <div class="zigzag-content">
                <p class="zigzag-text">Lorem ipsum dolor sit amet et delectus accommodare his consul copiosae legendos at vix ad putent delectus delicata usu. Vidit dissentiet eos cu eum an brute</p>
            </div>
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Lorem ipsum dolor</span>
                </div>
            </div>
        </div>
    </article>
</section>

<!-- Featured Phones Section -->
<?php if (!empty($featuredPhones)): ?>
    <h2 class="home-featured-title">Teléfonos destacados</h2>

    <section class="featured-section">
        <div class="section-container">
            <div class="featured-grid">
                <?php foreach ($featuredPhones as $phone): ?>
                    <article class="product-card">
                        <div class="product-image">
                            <?php if (!empty($phone['image'])): ?>
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($phone['image']); ?>"
                                    alt="<?php echo htmlspecialchars($phone['name']); ?>">
                            <?php else: ?>
                                <img src="/dashboard/TFG/assets/img/placeholder-product.jpg"
                                    alt="<?php echo htmlspecialchars($phone['name']); ?>">
                            <?php endif; ?>

                        </div>

                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($phone['name']); ?></h3>

                            <?php if (!empty($phone['description'])): ?>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($phone['description'], 0, 100)) . (strlen($phone['description']) > 100 ? '...' : ''); ?>
                                </p>
                            <?php endif; ?>

                            <div class="product-footer">
                                <span class="product-price"><?php echo number_format($phone['price'], 2, ',', '.'); ?> €</span>
                                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $phone['id']; ?>"
                                    class="product-btn">Ver más</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="section-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="view-all-link">Catálogo de móviles →</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Featured Accessories Section -->
<?php if (!empty($featuredAccessories)): ?>
    <h2 class="home-featured-title">Accesorios destacados</h2>
    <p class="home-description-text">Lorem ipsum dolor sit amet et delectus accommodare his consul copiosae legendos at vix ad putent delectus delicata usu. Vidit dissentiet eos cu eum an brute</p>

    <section class="accessories-section">
        <div class="section-container">
            <div class="accessories-grid">
                <?php foreach ($featuredAccessories as $accessory): ?>
                    <article class="accessory-card">
                        <div class="accessory-image">
                            <?php if (!empty($accessory['image'])): ?>
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($accessory['image']); ?>"
                                    alt="<?php echo htmlspecialchars($accessory['name']); ?>">
                            <?php else: ?>
                                <img src="/dashboard/TFG/assets/img/placeholder-product.jpg"
                                    alt="<?php echo htmlspecialchars($accessory['name']); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="accessory-info">
                            <h3 class="accessory-name"><?php echo htmlspecialchars($accessory['name']); ?></h3>
                            <p class="accessory-price"><?php echo number_format($accessory['price'], 2, ',', '.'); ?> €</p>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $accessory['id']; ?>"
                                class="accessory-btn">Ver más</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="section-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=accessories" class="view-all-link">Catálogo de accesorios →</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- No Products Message -->
<?php if (empty($featuredPhones) && empty($featuredAccessories)): ?>
    <div class="no-products-currently">
        <p>No hay productos destacados disponibles en este momento.</p>
    </div>
<?php endif; ?>