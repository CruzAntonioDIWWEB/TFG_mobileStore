<?php

namespace Controllers;

/**
 * Base Controller
 * Provides common functionality for all controllers
 */
class BaseController
{
    
    /**
     * Constructor - Initialize session if not started
     */
    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ========================================
    // 1. SESSION HANDLING AND USER AUTHENTICATION
    // ========================================

 /**
     * Check if user is logged in
     * @return bool true if user is logged in, false otherwise
     */
    protected function checkUserSession(){
        return isset($_SESSION['login']) && $_SESSION['login'] === true && isset($_SESSION['user']);
    }

    /**
     * Require user to be logged in, redirect to login if not
     * @param string $redirectAfterLogin URL to redirect after successful login
     * @return void
     */
    protected function requireLogin($redirectAfterLogin = null){
        if (!$this->checkUserSession()) {
            if ($redirectAfterLogin) {
                $_SESSION['redirect_after_login'] = $redirectAfterLogin;
            }
            $this->setErrorMessage('You must log in first.');
            $this->redirectToLogin();
        }
    }

    /**
     * Check if current user has admin role
     * @return bool true if user is admin, false otherwise
     */
    protected function checkAdminRole(){
        if (!$this->checkUserSession()) {
            return false;
        }
        
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    /**
     * Get current logged in user data
     * @return array|null user data if logged in, null otherwise
     */
    protected function getCurrentUser(){
        if ($this->checkUserSession()) {
            return $_SESSION['user'];
        }
        
        return null;
    }

    // ========================================
    // 2. REDIRECTS AND URL HANDLING
    // ========================================

     /**
     * Redirect to controller/action (clean URLs)
     * @param string $controller Controller name
     * @param string $action Action name
     * @param array $params Additional parameters
     * @param bool $permanent Whether redirect is permanent (301) or temporary (302)
     * @return void
     */
    protected function redirect($controller, $action = 'index', $params = [], $permanent = false){
        require_once __DIR__ . '/../Core/Router.php';
        
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        
        $url = \Core\Router::url($controller, $action, $params);
        header('Location: ' . $url);
        exit();
    }

    /**
     * Redirect to raw URL (for external links or specific cases)
     * @param string $url Full URL to redirect to
     * @param bool $permanent Whether redirect is permanent (301) or temporary (302)
     * @return void
     */
    protected function redirectToUrl($url, $permanent = false){
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        
        header('Location: ' . $url);
        exit();
    }

    /**
     * Redirect to login page
     * @return void
     */
    protected function redirectToLogin(){
        $this->redirect('user', 'login');
    }

    /**
     * Go back to previous page or specified fallback
     * @param string $fallbackController Fallback controller if no referrer
     * @param string $fallbackAction Fallback action if no referrer
     * @return void
     */
    protected function goBack($fallbackController = 'home', $fallbackAction = 'index'){
        $referrer = $_SERVER['HTTP_REFERER'] ?? null;
        
        if ($referrer) {
            $this->redirectToUrl($referrer);
        } else {
            $this->redirect($fallbackController, $fallbackAction);
        }
    }

    // ========================================
    // 3. NOTIFICATIONS
    // ========================================

    /**
     * Set success message
     * @param string $message Success message to display
     * @return void
     */
    protected function setSuccessMessage($message){
        $_SESSION['success_message'] = $message;
    }

    /**
     * Set error message
     * @param string $message Error message to display
     * @return void
     */
    protected function setErrorMessage($message){
        $_SESSION['error_message'] = $message;
    }

    /**
     * Set warning message
     * @param string $message Warning message to display
     * @return void
     */
    protected function setWarningMessage($message){
        $_SESSION['warning_message'] = $message;
    }

    /**
     * Get all messages and clear them from session
     * @return array Array containing success, error, and warning messages
     */
    protected function getMessages(){
        $messages = [
            'success' => $_SESSION['success_message'] ?? null,
            'error' => $_SESSION['error_message'] ?? null,
            'warning' => $_SESSION['warning_message'] ?? null
        ];

        // Clear messages after retrieving them
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
        unset($_SESSION['warning_message']);

        return $messages;
    }

    // ========================================
    // 4. VALIDATION AND SANITIZATION
    // ========================================

    /**
     * Sanitize input data
     * @param mixed $data Data to sanitize (string, array)
     * @return mixed Sanitized data
     */
    protected function sanitizeInput($data){
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        if (is_string($data)) {
            // Remove whitespace, convert special characters to HTML entities
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Validate required fields
     * @param array $data Data to validate
     * @param array $requiredFields Array of required field names
     * @return array Array of validation errors (empty if all valid)
     */
    protected function validateRequired($data, $requiredFields){
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "The field {$field} is required.";
            }
        }
        
        return $errors;
    }

    // ========================================
    // AUXILIARY FUNCTIONS
    // ========================================

     /**
     * Check if request method is POST
     * @return bool
     */
    protected function isPost(){
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request method is GET
     * @return bool
     */
    protected function isGet(){
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Get POST data with optional sanitization
     * @param string|null $key Specific key to get, or null for all data
     * @param bool $sanitize Whether to sanitize the data
     * @return mixed
     */
    protected function getPostData($key = null, $sanitize = true){
        $data = $key ? ($_POST[$key] ?? null) : $_POST;
        
        return $sanitize ? $this->sanitizeInput($data) : $data;
    }

    /**
     * Get GET data with optional sanitization
     * @param string|null $key Specific key to get, or null for all data
     * @param bool $sanitize Whether to sanitize the data
     * @return mixed
     */
    protected function getGetData($key = null, $sanitize = true){
        $data = $key ? ($_GET[$key] ?? null) : $_GET;
        
        return $sanitize ? $this->sanitizeInput($data) : $data;
    }

    /**
     * Require admin access, redirect if not admin
     * @return void
     */
    protected function requireAdmin(){
        $this->requireLogin();
        
        if (!$this->checkAdminRole()) {
            $this->setErrorMessage("You don't have permission to access this page.");
            $this->redirect('/'); // Redirect to home
        }
    }

    /**
     * Load view with layout
     * @param string $view View file path
     * @param array $data Data to pass to view
     */
    protected function loadView($view, $data = [])
    {
        // Extract data for use in view
        extract($data);
        
        // Get messages for display
        $messages = $this->getMessages();
        
        // Load layout (header -> content -> footer)
        require_once __DIR__ . '/../Views/layout/header.php';
        require_once __DIR__ . "/../Views/{$view}.php";
        require_once __DIR__ . '/../Views/layout/footer.php';
    }
}

?>