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
        <!-- Introduction Title -->
        <div class="zigzag-intro">
            <h2 class="zigzag-main-title">¿Por qué elegir Crusertel?</h2>
            <p class="zigzag-main-subtitle">Descubre lo que nos hace diferentes en el mundo de la tecnología móvil</p>
        </div>

        <!-- Row 1: Image Left, Text Right -->
        <div class="zigzag-row">
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Tecnología de Vanguardia</span>
                </div>
            </div>
            <div class="zigzag-content">
                <h3 class="zigzag-title">Los Últimos Móviles del Mercado</h3>
                <p class="zigzag-text">En Crusertel, nos mantenemos a la vanguardia de la tecnología móvil. Ofrecemos los smartphones más innovadores del mercado, desde los últimos iPhone hasta los Android más avanzados.</p>
            </div>
        </div>

        <!-- Row 2: Text Left, Image Right -->
        <div class="zigzag-row zigzag-row-reverse">
            <div class="zigzag-content">
                <h3 class="zigzag-title">Accesorios que Complementan tu Estilo</h3>
                <p class="zigzag-text">Más que solo móviles, somos tu destino completo para accesorios que potencian tu experiencia digital. Desde fundas elegantes que protegen tu inversión, hasta cargadores rápidos y auriculares de alta calidad.</p>
            </div>
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Accesorios Premium</span>
                </div>
            </div>
        </div>

        <!-- Row 3: Image Left, Text Right -->
        <div class="zigzag-row">
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Experiencia de Compra</span>
                </div>
            </div>
            <div class="zigzag-content">
                <h3 class="zigzag-title">Compra Segura y Sin Complicaciones</h3>
                <p class="zigzag-text">Tu tranquilidad es nuestra prioridad. Por eso hemos diseñado un proceso de compra intuitivo y completamente seguro. Con integración de PayPal para pagos protegidos y un sistema de seguimiento de pedidos transparente. Desde la navegación hasta la entrega, cada paso está pensado para brindarte la mejor experiencia de compra online.</p>
            </div>
        </div>

        <!-- Row 4: Text Left, Image Right -->
        <div class="zigzag-row zigzag-row-reverse">
            <div class="zigzag-content">
                <h3 class="zigzag-title">Soporte Personalizado para Ti</h3>
                <p class="zigzag-text">Cuando se trata de nosotros, no termina todo con la venta. Nuestro equipo de atención al cliente está siempre disponible para resolver tus dudas o ayudarte con la configuración de tu nuevo dispositivo. Ofrecemos soporte personalizado porque entendemos que cada cliente tiene necesidades únicas.</p>
            </div>
            <div class="zigzag-image">
                <div class="image-placeholder">
                    <span>Atención Especializada</span>
                </div>
            </div>
        </div>
    </article>
</section>

<!-- Featured Phones Section -->
<?php if (!empty($featuredPhones)): ?>
    <h2 class="home-featured-title">Teléfonos destacados</h2>
    <p class="home-description-text">Presentamos nuestras nuevas adquisiciones a nuestro catálogo de teléfonos. </p>

    <section class="accessories-section">
        <div class="section-container">
            <div class="accessories-grid">
                <?php foreach ($featuredPhones as $phone): ?>
                    <article class="accessory-card">
                        <div class="accessory-image">
                            <?php if (!empty($phone['image'])): ?>
                                <img src="/dashboard/TFG/assets/img/products/<?php echo htmlspecialchars($phone['image']); ?>"
                                    alt="<?php echo htmlspecialchars($phone['name']); ?>">
                            <?php else: ?>
                                <img src="/dashboard/TFG/assets/img/placeholder-product.jpg"
                                    alt="<?php echo htmlspecialchars($phone['name']); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="accessory-info">
                            <h3 class="accessory-name"><?php echo htmlspecialchars($phone['name']); ?></h3>
                            <p class="accessory-price"><?php echo number_format($phone['price'], 2, ',', '.'); ?> €</p>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $phone['id']; ?>"
                                class="accessory-btn">Ver más</a>
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
    <p class="home-description-text">Presentamos nuestras nuevas adquisiciones a nuestro catálogo de accesorios.</p>

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