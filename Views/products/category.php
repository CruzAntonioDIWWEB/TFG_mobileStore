<?php
// Get data passed from controller
$products = $products ?? [];
$categoryName = $categoryName ?? 'Productos';
$categoryId = $categoryId ?? null;
?>

<!-- Category Products Section -->
<section class="catalog-section">
    <div class="catalog-container">
        
        <!-- Header -->
        <div class="catalog-header">
            <div class="catalog-title-section">
                <h1 class="catalog-title"><?php echo htmlspecialchars($categoryName); ?></h1>
                <p class="catalog-subtitle">Descubre todos los productos de esta categoría</p>
            </div>
            <div class="catalog-info">
                <span class="product-count"><?php echo count($products); ?> productos disponibles</span>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="breadcrumb-link">
                <i class="fas fa-home"></i>
                Inicio
            </a>
            <i class="fas fa-chevron-right"></i>
            <span class="breadcrumb-current"><?php echo htmlspecialchars($categoryName); ?></span>
        </div>

        <!-- Products Display -->
        <div class="products-content">
            <?php if (!empty($products) && is_array($products)): ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo ASSETS_URL; ?>img/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($product['stock'] <= 0): ?>
                                    <div class="stock-overlay">
                                        <span class="out-of-stock">Agotado</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                
                                <?php if (!empty($product['description'])): ?>
                                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <?php endif; ?>

                                <div class="product-details">
                                    <div class="product-price">
                                        <span class="price"><?php echo number_format($product['price'], 2); ?>€</span>
                                    </div>
                                    
                                    <div class="product-stock">
                                        <?php if ($product['stock'] > 0): ?>
                                            <span class="stock in-stock">
                                                <i class="fas fa-check-circle"></i>
                                                <?php echo $product['stock']; ?> disponibles
                                            </span>
                                        <?php else: ?>
                                            <span class="stock out-of-stock">
                                                <i class="fas fa-times-circle"></i>
                                                Sin stock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="product-actions">
                                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=detailProd&id=<?php echo $product['id']; ?>" 
                                       class="product-btn btn-view">
                                        <i class="fas fa-eye"></i>
                                        Ver detalles
                                    </a>
                                    
                                    <?php if ($product['stock'] > 0): ?>
                                        <button type="button" 
                                                class="product-btn btn-cart" 
                                                onclick="addToCart(<?php echo $product['id']; ?>)">
                                            <i class="fas fa-cart-plus"></i>
                                            Añadir al carrito
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="product-btn btn-disabled" disabled>
                                            <i class="fas fa-ban"></i>
                                            No disponible
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-products">
                    <div class="no-products-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>No hay productos disponibles</h3>
                    <p>Actualmente no hay productos en la categoría "<?php echo htmlspecialchars($categoryName); ?>".</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="back-home-btn">
                        <i class="fas fa-arrow-left"></i>
                        Volver al inicio
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Add to cart functionality
function addToCart(productId) {
    // Check if user is logged in
    <?php if (!isset($_SESSION['user'])): ?>
        alert('Debes iniciar sesión para añadir productos al carrito');
        window.location.href = '<?php echo BASE_URL; ?>index.php?controller=user&action=login';
        return;
    <?php endif; ?>

    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 1);

    // Send AJAX request
    fetch('<?php echo BASE_URL; ?>index.php?controller=cart&action=addToCart', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Simple feedback
        alert('Producto añadido al carrito');
        // Refresh page to update cart count
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al añadir el producto al carrito');
    });
}
</script>