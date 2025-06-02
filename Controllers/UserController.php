<?php

namespace Controllers;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/BaseController.php';

/**
 * UserController
 * Handles user authentication, registration, and profile management
 */
class UserController extends BaseController
{

    // ========================================
    // REGISTRATION & LOGIN METHODS
    // ========================================

    /**
     * Show registration form
     */
    public function register(){
        // If user is already logged in, redirect to home
        if ($this->checkUserSession()) {
            $this->redirect('home', 'index'); 
            return;
        }

        $this->loadView('user/register');
    }

    /**
     * Handle user registration
     */
public function processRegistration(){
    // Add this line first to see if method is called
    error_log("processRegistration method called");
    file_put_contents(__DIR__ . '/debug.log', "processRegistration called at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    
    if(!$this->isPost()){
        error_log("Not a POST request");
        $this->redirect('user', 'register'); 
        return;
    }

    $postData = $this->getPostData();
    error_log("POST data received: " . print_r($postData, true));

    // Validate input data
    $requiredFields = ['name', 'surnames', 'email', 'password'];
    $errors = $this->validateRequired($postData, $requiredFields);

    if (!empty($errors)) {
        $this->setErrorMessage('Todos los campos son obligatorios');
        $this->redirect('user', 'register'); 
        return;
    }

    $name = $postData['name'];
    $surnames = $postData['surnames'];
    $email = $postData['email'];
    $password = $postData['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->setErrorMessage('Formato de email inválido');
        $this->redirect('user', 'register'); 
        return;
    }

    // Validate password length
    if (strlen($password) < 4) { 
        $this->setErrorMessage('La contraseña debe tener al menos 4 caracteres');
        $this->redirect('user', 'register'); 
        return;
    }

    // Create user instance and check if email already exists
    $user = new \Models\User();

    if($user->checkUserExists($email)){
        $this->setErrorMessage('Este email ya está registrado');
        $this->redirect('user', 'register'); 
        return;
    }

    $user->setName($name);
    $user->setSurnames($surnames);
    $user->setEmail($email);
    $user->setPassword($password);

    // Save user to database
    $saved = $user->saveDB();

    if ($saved) {
        $this->setSuccessMessage('Registro completado con éxito. Ya puedes iniciar sesión.');
        $this->redirect('user', 'login'); 
    } else {
        $this->setErrorMessage('Error en el registro. Por favor, inténtalo de nuevo.');
        $this->redirect('user', 'register'); 
    }
}

    // ========================================
    // AUTHENTICATION METHODS
    // ========================================

    /**
     * Show login form
     */
    public function login(){
        // If user is already logged in, redirect to home
        if ($this->checkUserSession()) {
            $this->redirect('index.php');
            return;
        }

        $this->loadView('user/login');
    }

    /**
     * Handle user login
     */
    public function processLogin(){
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=user&action=login');
            return;
        }

        $postData = $this->getPostData();

        // Validate required fields
        $requiredFields = ['email', 'password'];
        $errors = $this->validateRequired($postData, $requiredFields);

        if (!empty($errors)) {
            $this->setErrorMessage('Email and password are required');
            $this->redirect('index.php?controller=user&action=login');
            return;
        }

        $email = $postData['email'];
        $password = $postData['password'];
        $remember = isset($postData['remember']) ? true : false;

        // Attempt to log in the user
        $user = new \Models\User();
        $loginResult = $user->login($email, $password, $remember);

        if($loginResult){
            $this->setSuccessMessage('Welcome back!');

            // Redirect to home page
            $redirectURL = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);

            $this->redirect($redirectURL);
        } else {
            $this->setErrorMessage('Invalid email or password');
            $this->redirect('index.php?controller=user&action=login');
        }
    }

    /**
     * Handle user logout
     */
    public function logout(){
        $user = new \Models\User();
        $user->logout();

        $this->setSuccessMessage('You have been logged out successfully');
        $this->redirect('index.php');
    }

    // ========================================
    // PROFILE MANAGEMENT METHODS
    // ========================================

     /**
     * Show user profile/edit form
     */
    public function profile(){
        $this->requireLogin();

        $currentUser = $this->getCurrentUser();
        $user = new \Models\User();
        $userData = $user->getUserById($currentUser['id']);

        if (!$userData) {
            $this->setErrorMessage('Error loading user profile');
            $this->redirect('home', 'index');
            return;
        }

        $this->loadView('user/profile', ['user' => $userData]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(){
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('user', 'profile');
            return;
        }

        $postData = $this->getPostData();
        $currentUser = $this->getCurrentUser();

        // Validate required fields
        $requiredFields = ['name', 'surnames', 'email'];
        $errors = $this->validateRequired($postData, $requiredFields);

        if (!empty($errors)) {
            $this->setErrorMessage('Name, surnames and email are required');
            $this->redirect('user', 'profile');
            return;
        }

        $name = $postData['name'];
        $surnames = $postData['surnames'];
        $email = $postData['email'];
        $password = $postData['password'] ?? '';

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Invalid email format');
            $this->redirect('user', 'profile');
            return;
        }

        // Create user instance and update data
        $user = new \Models\User();
        $user->setId($currentUser['id']);
        $user->setName($name);
        $user->setSurnames($surnames);
        $user->setEmail($email);

        // Update password if provided
        if (!empty($password)) {
            if (strlen($password) < 4) {
                $this->setErrorMessage('Password must be at least 4 characters long');
                $this->redirect('user', 'profile');
                return;
            }
            $user->setPassword($password);
        }

        // Update in database
        $updated = $user->updateDB();

        if ($updated) {
            // Update session data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['surnames'] = $surnames;
            $_SESSION['user']['email'] = $email;

            $this->setSuccessMessage('Profile updated successfully');
        } else {
            $this->setErrorMessage('Error updating profile. Please try again.');
        }

        $this->redirect('user', 'profile');
    }

    // ========================================
    // ADMIN METHODS
    // ========================================

    /**
     * List all users (admin only)
     */
    public function listUsers(){
        $this->requireAdmin();

        $user = new \Models\User();
        $users = $user->getAll();

        $this->loadView('admin/users', ['users' => $users]);
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser(){
        $this->requireAdmin();

        // Only allow POST requests for delete operations
        if (!$this->isPost()) {
            $this->setErrorMessage('Invalid request method');
            $this->redirect('user', 'listUsers');
            return;
        }

        $userId = $this->getPostData('id');
        
        if (!$userId || !is_numeric($userId)) {
            $this->setErrorMessage('Invalid user ID');
            $this->redirect('user', 'listUsers');
            return;
        }

        $currentUser = $this->getCurrentUser();
        
        // Prevent admin from deleting themselves
        if ($userId == $currentUser['id']) {
            $this->setErrorMessage('You cannot delete your own account');
            $this->redirect('user', 'listUsers');
            return;
        }

        $user = new \Models\User();
        $user->setId($userId);
        $deleted = $user->delete();

        if ($deleted) {
            $this->setSuccessMessage('User deleted successfully');
        } else {
            $this->setErrorMessage('Error deleting user');
        }

        $this->redirect('user', 'listUsers');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Load view with layout
     * @param string $view View file path
     * @param array $data Data to pass to view
     */
    protected function loadView($view, $data = []){
        // Extract data for use in view
        extract($data);
        
        // Get messages for display
        $messages = $this->getMessages();
        
        require_once __DIR__ . '/../Views/layout/header.php';
        require_once __DIR__ . "/../Views/{$view}.php";
        require_once __DIR__ . '/../Views/layout/footer.php';
    }
}

?>