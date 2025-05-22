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
            $this->setErrorMessage('Debes iniciar sesión para acceder a esta página.');
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
     * Generic redirect function
     * @param string $url URL to redirect to
     * @param bool $permanent Whether redirect is permanent (301) or temporary (302)
     * @return void
     */
    protected function redirect($url, $permanent = false){
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        
        header('Location: ' . $url);
        exit();
    }

    /**
     * TODO
     * Redirect to login page
     * @return void
     */
    protected function redirectToLogin(){
    
    }

    /**
     * Go back to previous page or specified fallback
     * @param string $fallback Fallback URL if no referrer available
     * @return void
     */
    protected function goBack($fallback = '/'){
        $referrer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($referrer);
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
                $errors[$field] = "El campo {$field} es obligatorio.";
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
     * TODO
     * Require admin access, redirect if not admin
     * @return void
     */
    protected function requireAdmin(){
        $this->requireLogin();
        
        if (!$this->checkAdminRole()) {
            $this->setErrorMessage('No tienes permisos para acceder a esta página.');
            $this->redirect('/'); // Redirect to home
        }
    }
}

?>