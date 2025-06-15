<?php

namespace Controllers;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Order.php';
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
    public function register()
    {
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
    public function processRegistration()
    {
        error_log("processRegistration method called");
        file_put_contents(__DIR__ . '/debug.log', "processRegistration called at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        if (!$this->isPost()) {
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

        if ($user->checkUserExists($email)) {
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

    /**
     * Show registration success page
     */
    public function registrationSuccess()
    {
        // Check if user just registered (security measure)
        if (!isset($_SESSION['just_registered'])) {
            $this->redirect('user', 'register');
            return;
        }

        // Clear the flag so user can't access this page again by direct URL
        unset($_SESSION['just_registered']);

        $this->loadView('user/registration_success');
    }

    // ========================================
    // AUTHENTICATION METHODS
    // ========================================

    /**
     * Show login form
     */
    public function login()
    {
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
    public function processLogin()
    {
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

        if ($loginResult) {
            $this->setSuccessMessage('Welcome back!');

            // Redirect to home page
            $redirectURL = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);

            $this->redirect($redirectURL);
        } else {
            $this->setErrorMessage('Invalid email or password');
            // CHANGED: Stay on login page instead of redirecting to homepage
            $this->redirect('user', 'login');  // CHANGE: Was $this->redirect($redirectURL);
        }
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
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
    public function profile()
    {
        $this->requireLogin();

        $currentUser = $this->getCurrentUser();

        $viewData = [
            'currentUser' => $currentUser
        ];

        $this->loadView('user/settings', $viewData);
    }

    /**
     * Show user profile edit form
     */
    public function editProfile()
    {
        $this->requireLogin();

        $currentUser = $this->getCurrentUser();
        $user = new \Models\User();
        $userData = $user->getUserById($currentUser['id']);

        if (!$userData) {
            $this->setErrorMessage('Error loading user data');
            $this->redirect('user', 'profile');
            return;
        }

        // Convert User object to array for the view
        $userArray = [
            'id' => $userData->getId(),
            'name' => $userData->getName(),
            'surnames' => $userData->getSurnames(),
            'email' => $userData->getEmail(),
            'role' => $userData->getRole()
        ];

        $this->loadView('user/edit_profile', ['user' => $userArray]);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
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
        $password = $postData['password'] ?? null;

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Invalid email format');
            $this->redirect('user', 'profile');
            return;
        }

        // Validate password length if provided
        if ($password && strlen($password) < 4) {
            $this->setErrorMessage('Password must be at least 4 characters');
            $this->redirect('user', 'profile');
            return;
        }

        // Update user
        $user = new \Models\User();
        $user->setId($currentUser['id']);
        $user->setName($name);
        $user->setSurnames($surnames);
        $user->setEmail($email);

        if ($password) {
            $user->setPassword($password);
        }

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

    /**
     * Display user's order history
     */
    public function orderHistory()
    {
        $this->requireLogin();
        $this->loadView('user/order_history');
    }

    /**
     * Get user orders as JSON
     */
    public function getOrders()
    {
        // Clean any output buffer to prevent HTML errors -_-
        ob_clean();

        $this->requireLogin();

        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser || !isset($currentUser['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Usuario no válido']);
                exit;
            }

            $userId = intval($currentUser['id']);

            // Create order model instance
            $orderModel = new \Models\Order();
            $orders = $orderModel->getOrderByUser($userId);

            // Return response
            if ($orders !== false) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $orders ?: []
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            error_log("Error in getOrders: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al cargar pedidos'
            ]);
        }

        exit;
    }

    // ========================================
    // ADMIN USER MANAGEMENT METHODS 
    // ========================================

    /**
     * Display all users (admin only)
     */
    public function index()
    {
        $this->requireAdmin();

        $userModel = new \Models\User();
        $users = $userModel->getAll();

        $this->loadView('admin/users/index', ['users' => $users]);
    }

    /**
     * Show form to create a new user (admin only)
     */
    public function create()
    {
        $this->requireAdmin();
        $this->loadView('admin/users/create');
    }

    /**
     * Show form to edit an existing user (admin only)
     */
    public function edit()
    {
        $this->requireAdmin();

        $userId = $this->getGetData('id');

        if (!$userId || !is_numeric($userId)) {
            $this->setErrorMessage('ID de usuario inválido');
            $this->redirect('user', 'index');
            return;
        }

        $userModel = new \Models\User();
        $user = $userModel->getUserById($userId);

        if (!$user) {
            $this->setErrorMessage('Usuario no encontrado');
            $this->redirect('user', 'index');
            return;
        }

        $this->loadView('admin/users/edit', ['user' => $user]);
    }

    // ========================================
    // ADMIN USER CRUD OPERATIONS 
    // ========================================

    /**
     * Save a new user (admin only)
     */
    public function save()
    {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('user', 'index');
            return;
        }

        $postData = $this->getPostData();
        $name = trim($postData['name'] ?? '');
        $surnames = trim($postData['surnames'] ?? '');
        $email = trim($postData['email'] ?? '');
        $password = trim($postData['password'] ?? '');
        $role = trim($postData['role'] ?? 'user');

        // Validate required fields
        if (empty($name) || empty($surnames) || empty($email) || empty($password)) {
            $this->setErrorMessage('Todos los campos son obligatorios');
            $this->redirect('user', 'create');
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Formato de email inválido');
            $this->redirect('user', 'create');
            return;
        }

        // Validate password length
        if (strlen($password) < 4) {
            $this->setErrorMessage('La contraseña debe tener al menos 4 caracteres');
            $this->redirect('user', 'create');
            return;
        }

        // Check if email already exists
        $userModel = new \Models\User();
        if ($userModel->checkUserExists($email)) {
            $this->setErrorMessage('Este email ya está registrado');
            $this->redirect('user', 'create');
            return;
        }

        // Create new user
        $user = new \Models\User();
        $user->setName($name);
        $user->setSurnames($surnames);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setRole($role);

        $saved = $user->saveDB();

        if ($saved) {
            $this->setSuccessMessage('Usuario creado exitosamente');
            $this->redirect('user', 'index');
        } else {
            $this->setErrorMessage('Error al crear el usuario');
            $this->redirect('user', 'create');
        }
    }

    /**
     * Update an existing user (admin only)
     */
    public function update()
    {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('user', 'index');
            return;
        }

        $postData = $this->getPostData();
        $userId = intval($postData['id'] ?? 0);
        $name = trim($postData['name'] ?? '');
        $surnames = trim($postData['surnames'] ?? '');
        $email = trim($postData['email'] ?? '');
        $password = trim($postData['password'] ?? '');
        $role = trim($postData['role'] ?? 'cliente');

        // Validate user ID
        if (!$userId) {
            $this->setErrorMessage('ID de usuario inválido');
            $this->redirect('user', 'index');
            return;
        }

        // Validate required fields (password is optional for updates)
        if (empty($name) || empty($surnames) || empty($email)) {
            $this->setErrorMessage('Nombre, apellidos y email son obligatorios');
            $this->redirect('user', 'edit', ['id' => $userId]);
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Formato de email inválido');
            $this->redirect('user', 'edit', ['id' => $userId]);
            return;
        }

        // Validate password if provided
        if (!empty($password) && strlen($password) < 4) {
            $this->setErrorMessage('La contraseña debe tener al menos 4 caracteres');
            $this->redirect('user', 'edit', ['id' => $userId]);
            return;
        }

        // Get user and update
        $userModel = new \Models\User();
        $user = $userModel->getUserById($userId);

        if (!$user) {
            $this->setErrorMessage('Usuario no encontrado');
            $this->redirect('user', 'index');
            return;
        }

        // Update user data
        $user->setName($name);
        $user->setSurnames($surnames);
        $user->setEmail($email);

        // Only update password if provided
        if (!empty($password)) {
            $user->setPassword($password);
        }

        // FIX: ADD THIS LINE - Set the role on the user object
        $user->setRole($role);

        $updated = $user->updateDB();

        if ($updated) {
            $this->setSuccessMessage('Usuario actualizado exitosamente');
            $this->redirect('user', 'index');
        } else {
            $this->setErrorMessage('Error al actualizar el usuario');
            $this->redirect('user', 'edit', ['id' => $userId]);
        }
    }

    /**
     * Delete user (admin only)
     */
    public function delete()
    {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('user', 'index');
            return;
        }

        $userId = intval($this->getPostData('id') ?? 0);

        if (!$userId) {
            $this->setErrorMessage('ID de usuario inválido');
            $this->redirect('user', 'index');
            return;
        }

        $currentUser = $this->getCurrentUser();

        // Prevent admin from deleting themselves
        if ($userId == $currentUser['id']) {
            $this->setErrorMessage('No puedes eliminar tu propia cuenta');
            $this->redirect('user', 'index');
            return;
        }

        $userModel = new \Models\User();
        $user = $userModel->getUserById($userId);

        if (!$user) {
            $this->setErrorMessage('Usuario no encontrado');
            $this->redirect('user', 'index');
            return;
        }

        // Delete user
        $user->setId($userId);
        $deleted = $user->delete();

        if ($deleted) {
            $this->setSuccessMessage('Usuario eliminado exitosamente');
        } else {
            $this->setErrorMessage('Error al eliminar el usuario');
        }

        $this->redirect('user', 'index');
    }

    /**
     * Update order status (admin only)
     */
    public function updateOrderStatus()
    {
        // Require admin access
        $this->requireLogin();

        if (!$this->checkAdminRole()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        try {
            $postData = $this->getPostData();
            $orderId = intval($postData['order_id'] ?? 0);
            $newStatus = trim($postData['status'] ?? '');

            // Validate input
            if (!$orderId || !$newStatus) {
                throw new \Exception('Missing order ID or status');
            }

            // Validate status value
            $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'canceled'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new \Exception('Invalid status value');
            }

            // Load order and update status
            $order = new \Models\Order();
            if ($order->getOrderById($orderId)) {
                $order->setStatus($newStatus);

                if ($order->updateDB()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Order status updated successfully',
                        'orderId' => $orderId,
                        'newStatus' => $newStatus
                    ]);
                } else {
                    throw new \Exception('Failed to update order status in database');
                }
            } else {
                throw new \Exception('Order not found');
            }
        } catch (\Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Get all orders for admin users 
     */
    public function getAllOrders()
    {
        // Clean any output buffer to prevent HTML errors -_-
        ob_clean();

        $this->requireLogin();

        if (!$this->checkAdminRole()) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit;
        }

        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            // Create order model instance
            $orderModel = new \Models\Order();
            $orders = $orderModel->getAllOrders();

            // Return response
            if ($orders !== false) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $orders ?: []
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            error_log("Error in getAllOrders: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al cargar pedidos'
            ]);
        }

        exit;
    }

    // ========================================
    // LEGACY ADMIN METHODS
    // ========================================

    /**
     * List all users (admin only) - Legacy method, redirects to index
     */
    public function listUsers()
    {
        $this->redirect('user', 'index');
    }

    /**
     * Delete user (admin only) - Legacy method, redirects to delete
     */
    public function deleteUser()
    {
        $this->delete();
    }
}
