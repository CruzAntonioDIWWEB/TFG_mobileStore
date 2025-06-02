<?php
session_start();

// Load configuration
require_once '../config/config.php';
require_once '../vendor/autoload.php';

echo "HTTP Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "POST data: <pre>" . print_r($_POST, true) . "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "This is a POST request!<br>";
    
    // Test the actual registration
    require_once '../Models/User.php';
    
    $postData = $_POST;
    
    if (!empty($postData['name']) && !empty($postData['email']) && !empty($postData['password'])) {
        $user = new \Models\User();
        
        $user->setName($postData['name']);
        $user->setSurnames($postData['surnames']);
        $user->setEmail($postData['email']);
        $user->setPassword($postData['password']);
        
        echo "About to save user...<br>";
        $saved = $user->saveDB();
        echo "Save result: " . ($saved ? 'SUCCESS' : 'FAILED') . "<br>";
        
        if ($saved) {
            echo "User registered successfully!";
        } else {
            echo "Failed to save user to database.";
        }
    } else {
        echo "Missing required fields.";
    }
} else {
    echo "This is NOT a POST request.";
}
?>