<?php
// Include the BrandHelper
require_once __DIR__ . '/../../Helpers/BrandHelper.php';
use Helpers\BrandHelper;

// Get messages for display
$messages = $messages ?? [];

// Get available brands from phones
$availableBrands = [];
if (!empty($phones) && is_array($phones)) {
    $availableBrands = BrandHelper::getAvailableBrands($phones);
}
$brandLabels = BrandHelper::getBrandLabels();
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
        <h1 class="catalog-title">Móviles</h1>
        <p class="catalog-description">Descubre nuestra amplia selección de teléfonos móviles de las mejores marcas</p>
    </div>
</section>

<!-- Brand Filters -->
<section class="brand-filters">
    <div class="filters-container">
        <h2 class="filters-title">Filtrar por marca</h2>
        <div class="brand-buttons">
            <!-- Always show "All" button -->
            <button class="brand-btn active" data-brand="all">
                <span class="brand-text">Todos</span>
            </button>
            
            <!-- Dynamic brand buttons based on available products -->
            <?php foreach ($availableBrands as $brandKey): ?>
                <button class="brand-btn" data-brand="<?php echo htmlspecialchars($brandKey); ?>">
                    <span class="brand-text"><?php echo htmlspecialchars($brandLabels[$brandKey] ?? ucfirst($brandKey)); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="phones-catalog">
    <div class="catalog-container">
        <?php if (!empty($phones) && is_array($phones)): ?>
            <div class="phones-grid" id="phones-grid">
                <?php foreach ($phones as $phone): ?>
                    <?php $brandKey = BrandHelper::detectBrand($phone['name']); ?>
                    <article class="phone-card" data-brand="<?php echo htmlspecialchars($brandKey); ?>">
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
                            <?php if ($phone['stock'] <= 0): ?>
                                <div class="stock-badge out-of-stock">
                                    <i class="fas fa-times"></i>
                                    Sin Stock
                                </div>
                            <?php elseif ($phone['stock'] <= 5): ?>
                                <div class="stock-badge low-stock">
                                    <i class="fas fa-exclamation"></i>
                                    Últimas unidades
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="phone-info">
                            <h3 class="phone-name">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=show&id=<?php echo $phone['id']; ?>">
                                    <?php echo htmlspecialchars($phone['name']); ?>
                                </a>
                            </h3>
                            
                            <p class="phone-description">
                                <?php echo htmlspecialchars(substr($phone['description'] ?? '', 0, 100)); ?>
                                <?php if (strlen($phone['description'] ?? '') > 100): ?>...<?php endif; ?>
                            </p>
                            
                            <div class="phone-price">
                                <span class="current-price"><?php echo number_format($phone['price'], 2, ',', '.'); ?> €</span>
                            </div>
                            
                            <div class="phone-actions">
                                <?php if ($phone['stock'] > 0): ?>
                                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=show&id=<?php echo $phone['id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        Ver detalles
                                    </a>
                                    <button class="btn btn-secondary add-to-cart-btn" 
                                            data-product-id="<?php echo $phone['id']; ?>"
                                            data-product-name="<?php echo htmlspecialchars($phone['name']); ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                        Añadir al carrito
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-disabled" disabled>
                                        <i class="fas fa-times"></i>
                                        Sin stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                <div class="no-products-content">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>No hay móviles disponibles</h3>
                    <p>Actualmente no tenemos móviles en nuestro catálogo.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Brand Filtering JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandButtons = document.querySelectorAll('.brand-btn');
    const phoneCards = document.querySelectorAll('.phone-card');
    
    brandButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            brandButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get selected brand
            const selectedBrand = this.getAttribute('data-brand');
            
            // Filter phones
            phoneCards.forEach(card => {
                const cardBrand = card.getAttribute('data-brand');
                
                if (selectedBrand === 'all' || cardBrand === selectedBrand) {
                    card.style.display = 'block';
                    // Add animation
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.opacity = '1';
                    }, 50);
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update URL without page reload (optional)
            const url = new URL(window.location);
            if (selectedBrand === 'all') {
                url.searchParams.delete('brand');
            } else {
                url.searchParams.set('brand', selectedBrand);
            }
            window.history.replaceState({}, '', url);
        });
    });
    
    // Check for brand parameter in URL on page load
    const urlParams = new URLSearchParams(window.location.search);
    const brandParam = urlParams.get('brand');
    
    if (brandParam) {
        const targetButton = document.querySelector(`[data-brand="${brandParam}"]`);
        if (targetButton) {
            targetButton.click();
        }
    }
});

// Add to cart functionality
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            // Add loading state
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Añadiendo...';
            this.disabled = true;
            
            // Send AJAX request to add to cart
            fetch('<?php echo BASE_URL; ?>index.php?controller=cart&action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    this.innerHTML = '<i class="fas fa-check"></i> ¡Añadido!';
                    this.classList.remove('btn-secondary');
                    this.classList.add('btn-success');
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-secondary');
                        this.disabled = false;
                    }, 2000);
                } else {
                    // Show error message
                    this.innerHTML = '<i class="fas fa-exclamation"></i> Error';
                    this.classList.add('btn-error');
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.classList.remove('btn-error');
                        this.disabled = false;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = originalContent;
                this.disabled = false;
            });
        });
    });
});
</script>