<?php
session_start();

// Load configuration and classes
require_once '../config/config.php';
require_once '../vendor/autoload.php';
require_once '../Models/User.php';
require_once '../Controllers/BaseController.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?controller=user&action=register');
    exit;
}

// Create a base controller instance for using helper methods
class RegistrationProcessor extends \Controllers\BaseController {
    
    public function processRegistration() {
        $postData = $this->getPostData();

        // Validate input data
        $requiredFields = ['name', 'surnames', 'email', 'password'];
        $errors = $this->validateRequired($postData, $requiredFields);

        if (!empty($errors)) {
            $this->setErrorMessage('Todos los campos son obligatorios');
            $this->redirectToRegister(); 
            return;
        }

        $name = $postData['name'];
        $surnames = $postData['surnames'];
        $email = $postData['email'];
        $password = $postData['password'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Formato de email inválido');
            $this->redirectToRegister(); 
            return;
        }

        // Validate password length
        if (strlen($password) < 4) { 
            $this->setErrorMessage('La contraseña debe tener al menos 4 caracteres');
            $this->redirectToRegister(); 
            return;
        }

        // Create user instance and check if email already exists
        $user = new \Models\User();

        if($user->checkUserExists($email)){
            $this->setErrorMessage('Este email ya está registrado');
            $this->redirectToRegister(); 
            return;
        }

        $user->setName($name);
        $user->setSurnames($surnames);
        $user->setEmail($email);
        $user->setPassword($password);

        // Save user to database
        $saved = $user->saveDB();

        if ($saved) {
            $this->setSuccessMessage('¡Registro completado con éxito! Tu cuenta ha sido creada.');
            $this->redirectToRegister(); // Stay on register page to show success message
        } else {
            $this->setErrorMessage('Error en el registro. Por favor, inténtalo de nuevo.');
            $this->redirectToRegister(); 
        }
    }
    
    // Custom redirect method to always go back to register page
    private function redirectToRegister() {
        header('Location: index.php?controller=user&action=register');
        exit;
    }
}

// Process the registration
$processor = new RegistrationProcessor();
$processor->processRegistration();
?>