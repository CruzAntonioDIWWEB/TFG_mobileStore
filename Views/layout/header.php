<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Store - Tienda de Móviles y Accesorios</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/main.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@400;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-container">
                <!-- Logo -->
                <div class="nav-logo">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=home&action=index">
                        <img src="<?php echo ASSETS_URL; ?>img/logo.jpg" alt="Mobile Store Logo" class="logo-img">
                    </a>
                </div>

                <!-- Navigation Menu -->
                <ul class="nav-menu" id="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=phones" class="nav-link">Móviles</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=product&action=accessoriesCatalog" class="nav-link">Accesorios</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=contact&action=index" class="nav-link">Contacto</a>
                    </li>
                    
                    <!-- Mobile User Actions (only visible in mobile menu) -->
                    <div class="mobile-user-actions">
                        <?php if (isset($_SESSION['user'])): ?>
                            <!-- User is logged in - Show settings and cart -->
                            <li class="nav-item mobile-user-item">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="nav-link mobile-user-link">
                                    <i class="fas fa-user"></i>
                                    Mi Perfil
                                </a>
                            </li>
                            <li class="nav-item mobile-user-item">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index" class="nav-link mobile-user-link">
                                    <i class="fas fa-shopping-cart"></i>
                                    Carrito
                                    <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                                        <span class="mobile-cart-count"><?php echo $_SESSION['cart_count']; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- User is not logged in - Show login and register buttons -->
                            <li class="nav-item mobile-user-item">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=login" class="nav-link mobile-user-link">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Iniciar Sesión
                                </a>
                            </li>
                            <li class="nav-item mobile-user-item">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=register" class="nav-link mobile-user-link">
                                    <i class="fas fa-user-plus"></i>
                                    Registrarse
                                </a>
                            </li>
                        <?php endif; ?>
                    </div>
                </ul>

                <!-- Desktop User Actions -->
                <div class="nav-actions">
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- User is logged in - Show settings and cart -->
                        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=profile" class="nav-icon" title="Mi Perfil">
                            <i class="fas fa-user"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=cart&action=index" class="nav-icon cart-icon" title="Carrito">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                                <span class="cart-count"><?php echo $_SESSION['cart_count']; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <!-- User is not logged in - Show login and register buttons -->
                        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=login" class="nav-button btn-login">Iniciar Sesión</a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=user&action=register" class="nav-button btn-register">Registrarse</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle -->
                <div class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Wrapper -->
    <main class="main-content">

    <script src="<?php echo ASSETS_URL; ?>js/navMenuMobile.js"></script>