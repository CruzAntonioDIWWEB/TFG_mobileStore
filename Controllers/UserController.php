<?php

namespace Controllers;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/UserModel.php';

/**
 * UserController
 * Handles user authentication, registration, and profile management
 */
class UserController extends BaseController
{

    // ========================================
    // REGISTRATION METHODS
    // ========================================

    /**
     * Show registration form
     */
    public function register(){
        // If user is already logged in, redirect to home
        if ($this->checkUserSession()) {
            $this->redirect('index.php');
            return;
        }

        $this->loadView('user/register');
    }

    /**
     * Load view with layout
     * @param string $view View file path
     * @param array $data Data to pass to view
     */
    private function loadView($view, $data = []){
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