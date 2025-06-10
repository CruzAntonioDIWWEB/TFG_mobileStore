<?php
session_start();

// Include the project's database configuration
require_once __DIR__ . '/../config/config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Check if user is logged in
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['user'])) {
        throw new Exception('Usuario no autenticado');
    }

    // Get user ID from session instead of GET parameter
    $user_id = $_SESSION['user']['id'];

    // Use the project's database connection (the $pdo variable from config.php)
    $query = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $pedidos = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $pedidos]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>