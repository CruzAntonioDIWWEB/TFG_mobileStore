<?php

namespace Controllers;

require_once __DIR__ . '/BaseController.php';

class ContactController extends BaseController
{
    /**
     * Display the contact form
     */
    public function index()
    {
        // Load the contact form view
        $this->loadView('contact/index');
    }

    /**
     * Process the contact form submission
     */
    public function processContact()
    {
        // Only allow POST requests
        if (!$this->isPost()) {
            $this->setErrorMessage('Método de solicitud inválido');
            $this->redirect('contact', 'index');
            return;
        }

        // Get form data
        $name = $this->getPostData('name');
        $email = $this->getPostData('email');
        $message = $this->getPostData('message');

        // Validate required fields
        if (empty($name) || empty($email) || empty($message)) {
            $this->setErrorMessage('Por favor, completa todos los campos obligatorios.');
            $this->redirect('contact', 'index');
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorMessage('Por favor, introduce un email válido.');
            $this->redirect('contact', 'index');
            return;
        }

        // Validate message length
        if (strlen($message) < 10) {
            $this->setErrorMessage('El mensaje debe tener al menos 10 caracteres.');
            $this->redirect('contact', 'index');
            return;
        }

        if (strlen($message) > 2000) {
            $this->setErrorMessage('El mensaje no puede superar los 2000 caracteres.');
            $this->redirect('contact', 'index');
            return;
        }

        // Process the contact form (for now, we'll simulate sending)
        $result = $this->sendContactMessage($name, $email, $message);

        if ($result) {
            $this->setSuccessMessage('¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos en breve.');
        } else {
            $this->setErrorMessage('Ha ocurrido un error al enviar tu mensaje. Por favor, inténtalo de nuevo.');
        }

        $this->redirect('contact', 'index');
    }

    /**
     * Send the contact message
     * This is a placeholder method - you can implement actual email sending here
     * 
     * @param string $name
     * @param string $email
     * @param string $message
     * @return bool
     */
    private function sendContactMessage($name, $email, $message)
    {
        // For now, we'll simulate successful sending
        // In a real implementation, you would:
        // 1. Send an email to the admin/support team
        // 2. Optionally save to database for tracking
        // 3. Send a confirmation email to the user

        // Simulate processing time
        usleep(100000); // 0.1 seconds

        // For demonstration, let's log the message
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'name' => $name,
            'email' => $email,
            'message' => $message
        ];

        // You could save this to a database or send via email
        error_log('Contact form submission: ' . json_encode($logData));

        // Return true to simulate successful sending
        return true;

    }

}

?>