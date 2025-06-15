<?php
session_start();

// Load configuration and autoload
require_once '../config/config.php';
require_once '../vendor/autoload.php';

// Load our Router
require_once '../Core/Router.php';

// Check for remember me cookie and restore session
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $user_id = $_COOKIE['remember_me'];

    // Create user instance and get user data
    $user = new \Models\User();
    $user_data = $user->getUserById($user_id);

    if ($user_data) {
        // Restore session
        $_SESSION['user'] = [
            'id' => $user_data->getId(),
            'name' => $user_data->getName(),
            'surnames' => $user_data->getSurnames(),
            'email' => $user_data->getEmail(),
            'role' => $user_data->getRole()
        ];

        $_SESSION['login'] = true;

        // Renew cookie for another 7 days
        setcookie('remember_me', $user_id, time() + (7 * 24 * 60 * 60), '/');
    }
}

// Create router instance and dispatch request
$router = new \Core\Router();

try {
    $router->dispatch();
} catch (Exception $e) {
    error_log("Router Error: " . $e->getMessage());
}
