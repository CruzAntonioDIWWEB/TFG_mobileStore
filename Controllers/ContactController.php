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
        $this->loadView('contact/index');
    }

    /**
     * Process the contact form submission
     */
    public function processContact()
    {
        if (!$this->isPost()) {
            $this->setErrorMessage('Método de solicitud inválido');
            $this->redirect('contact', 'index');
            return;
        }

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

        // Send contact message via Formspree
        $result = $this->sendContactMessage($name, $email, $message);

        if ($result) {
            $this->setSuccessMessage('¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos en breve.');
        } else {
            $this->setErrorMessage('Ha ocurrido un error al enviar tu mensaje. Por favor, inténtalo de nuevo.');
        }

        $this->redirect('contact', 'index');
    }

    /**
     * Send contact message via Formspree 
     */
    private function sendContactMessage($name, $email, $message)
    {
        // Generate formatted message content
        $formattedMessage = $this->generateContactMessage($name, $email, $message);

        // Your Formspree endpoint
        $formspreeUrl = 'https://formspree.io/f/mgvykool';

        // Prepare data for Formspree
        $postData = [
            'email' => $email,
            '_replyto' => $email,
            '_subject' => 'Nuevo mensaje de contacto - Crusertel - ' . $name,
            'message' => $formattedMessage,
            'customer_name' => $name,
            'contact_email' => $email
        ];

        // Send via Formspree using cURL for better reliability
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $formspreeUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result !== false && $httpCode >= 200 && $httpCode < 300) {
            error_log("✅ Contact email sent via Formspree successfully");
            return true;
        } else {
            error_log("❌ Failed to send contact email via Formspree. HTTP Code: $httpCode");
            return false;
        }
    }

    /**
     * Generate formatted contact message content
     */
    private function generateContactMessage($name, $email, $message)
    {
        $timestamp = date('d/m/Y H:i:s');

        return "
📞 NUEVO MENSAJE
=====================================

📅 Fecha: {$timestamp}
👤 Nombre: {$name}
📮 Email: {$email}

💬 MENSAJE:
=====================================
{$message}

=====================================

---
Este mensaje fue enviado desde el formulario de contacto de Crusertel.
        ";
    }

/**
 * Display license page
 */
public function license()
{
    $this->loadView('contact/license');
}

/**
 * Display privacy policy page
 */
public function privacy()
{
    $this->loadView('contact/privacy');
}


}
