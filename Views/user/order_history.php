<?php
// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ' . BASE_URL . 'index.php?controller=user&action=login');
    exit;
}

// Check if user is admin
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';
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
                        <?php echo $isAdmin ? 'Gestión de Pedidos' : 'Historial de Pedidos'; ?>
                    </h1>
                    <p class="order-history-subtitle">
                        <?php echo $isAdmin ? 'Administra todos los pedidos del sistema' : 'Consulta el estado y detalles de tus pedidos'; ?>
                    </p>
                </div>

                <div class="header-actions">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Perfil
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
                <p>Aún no <?php echo $isAdmin ? 'hay' : 'has realizado ningún'; ?> pedido<?php echo $isAdmin ? 's en el sistema' : ''; ?>.</p>
                <?php if (!$isAdmin): ?>
                    <p>¡Explora nuestro catálogo y haz tu primera compra!</p>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="shop-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Ir a Comprar
                    </a>
                <?php endif; ?>
            </div>

            <!-- Orders Table -->
            <div class="orders-table-wrapper" id="ordersTableWrapper" style="display: none;">
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <?php if ($isAdmin): ?>
                                    <th>Usuario</th>
                                <?php endif; ?>
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
                <p id="errorMessage">Ha ocurrido un error al cargar los pedidos. Por favor, inténtalo de nuevo.</p>
                <button class="retry-btn" onclick="cargarPedidos()">
                    <i class="fas fa-redo"></i>
                    Reintentar
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const IS_ADMIN = <?php echo $isAdmin ? 'true' : 'false'; ?>;
</script>
<script src="<?php echo ASSETS_URL; ?>js/user/orderHistory.js"></script>