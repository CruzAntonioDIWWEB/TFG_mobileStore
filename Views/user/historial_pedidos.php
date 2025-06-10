<?php
// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ' . BASE_URL . 'index.php?controller=user&action=login');
    exit;
}
?>

<!-- Order History Section -->
<section class="order-history-section">
    <div class="order-history-container">
        
        <!-- Header -->
        <div class="order-history-header">
            <div class="header-content">
                <div class="title-section">
                    <h1 class="order-history-title">
                        <i class="fas fa-history"></i>
                        Historial de Pedidos
                    </h1>
                    <p class="order-history-subtitle">Consulta el estado y detalles de tus pedidos</p>
                </div>
                
                <div class="header-actions">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Menú
                    </a>
                </div>
            </div>
        </div>

        <!-- Orders Content -->
        <div class="orders-content">
            <!-- Loading State -->
            <div class="loading-state" id="loadingState">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p>Cargando pedidos...</p>
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3>No hay pedidos que mostrar</h3>
                <p>Aún no has realizado ningún pedido. ¡Explora nuestro catálogo y haz tu primera compra!</p>
                <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="shop-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Ir a Comprar
                </a>
            </div>

            <!-- Orders Table -->
            <div class="orders-table-wrapper" id="ordersTableWrapper" style="display: none;">
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Ubicación</th>
                                <th>Dirección</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Error State -->
            <div class="error-state" id="errorState" style="display: none;">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Error al cargar pedidos</h3>
                <p id="errorMessage">Ha ocurrido un error al cargar tus pedidos. Por favor, inténtalo de nuevo.</p>
                <button class="retry-btn" onclick="cargarPedidos()">
                    <i class="fas fa-redo"></i>
                    Reintentar
                </button>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/user/orderHistory.js"></script>