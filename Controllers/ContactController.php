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

        // Process the contact message
        $result = $this->processContactMessage($name, $email, $message);
        
        if ($result) {
            $this->setSuccessMessage('¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos en breve.');
        } else {
            $this->setErrorMessage('Ha ocurrido un error al enviar tu mensaje. Por favor, inténtalo de nuevo.');
        }
        
        $this->redirect('contact', 'index');
    }

    /**
     * Process contact message - works in both development and production
     */
    private function processContactMessage($name, $email, $message)
    {
        // Try to send email first
        $emailSent = $this->sendContactEmail($name, $email, $message);
        
        // Always log the message (backup and for development)
        $logged = $this->logContactMessage($name, $email, $message);
        
        // Send confirmation to user if main email worked
        if ($emailSent) {
            $this->sendConfirmationEmail($email, $name);
        }
        
        // Return true if either email sent OR logged successfully
        return $emailSent || $logged;
    }

    /**
     * Send email using PHP's mail() function (works in production)
     */
    private function sendContactEmail($name, $email, $message)
    {
        // *** CHANGE THESE EMAIL ADDRESSES FOR YOUR PROJECT ***
        $toEmail = 'acg.purullena@gmail.com';          
        $fromEmail = 'cruchiniptoamo@gmail.com';       // ← Your domain email
        
        $subject = 'Nuevo mensaje de contacto - ' . $name;
        
        $emailBody = "
Has recibido un nuevo mensaje desde el formulario de contacto:

==================================================
DATOS DEL CONTACTO:
==================================================
Nombre: {$name}
Email: {$email}
Fecha: " . date('d/m/Y H:i:s') . "

==================================================
MENSAJE:
==================================================
{$message}

==================================================
Para responder, simplemente contesta a este email.
        ";
        
        $headers = [
            'From: TelefoniaPlus <' . $fromEmail . '>',
            'Reply-To: ' . $email,
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8',
            'X-Priority: 3'
        ];
        
        $formspreeUrl = 'https://formspree.io/f/mgvykool'; //formspree.io
    
    $postData = [
        'name' => $name,
        'email' => $email,
        'message' => $message,
        '_replyto' => $email,
        '_subject' => 'Nuevo mensaje de contacto - ' . $name
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($postData)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($formspreeUrl, false, $context);
    
    if ($result !== false) {
        error_log("✅ Email sent via Formspree successfully");
        return true;
    } else {
        error_log("❌ Failed to send email via Formspree");
        return false;
    }
    
    }

    /**
     * Log contact message to file (works everywhere, great for development)
     */
    private function logContactMessage($name, $email, $message)
    {
        try {
            $logFile = __DIR__ . '/../logs/contact_messages.log';
            
            // Create logs directory if it doesn't exist
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            $logEntry = "\n" . str_repeat('=', 60) . "\n";
            $logEntry .= "📧 NUEVO MENSAJE DE CONTACTO\n";
            $logEntry .= str_repeat('=', 60) . "\n";
            $logEntry .= "📅 Fecha: {$timestamp}\n";
            $logEntry .= "👤 Nombre: {$name}\n";
            $logEntry .= "📮 Email: {$email}\n";
            $logEntry .= "🌐 IP: {$ip}\n";
            $logEntry .= str_repeat('-', 60) . "\n";
            $logEntry .= "💬 Mensaje:\n{$message}\n";
            $logEntry .= str_repeat('=', 60) . "\n";
            
            $result = file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            
            if ($result !== false) {
                error_log("📝 Contact message logged to: {$logFile}");
                return true;
            }
            
        } catch (\Exception $e) {
            error_log("Failed to log contact message: " . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Send confirmation email to user
     */
    private function sendConfirmationEmail($userEmail, $userName)
    {
        $fromEmail = 'cruchiniptoamo@gmail.com';       // ← Your domain email
        
        $subject = '✅ Confirmación - Hemos recibido tu mensaje';
        
        $emailBody = "
Hola {$userName},

¡Gracias por contactarnos! 

Hemos recibido tu mensaje correctamente y nuestro equipo te responderá en un plazo de 24-48 horas.

==================================================
INFORMACIÓN DE CONTACTO:
==================================================
📞 Teléfono: +34 912 345 678
📧 Email: acg.purullena@gmail.com
🕒 Horario: Lunes a Viernes, 9:00 - 18:00

==================================================

Si tu consulta es urgente, no dudes en llamarnos.

Saludos cordiales,
El equipo de TelefoniaPlus
        ";
        
        $headers = [
            'From: TelefoniaPlus <' . $fromEmail . '>',
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8'
        ];
        
        $sent = @mail($userEmail, $subject, $emailBody, implode("\r\n", $headers));
        
        if ($sent) {
            error_log("✅ Confirmation sent to: {$userEmail}");
        } else {
            error_log("❌ Failed to send confirmation to: {$userEmail}");
        }
        
        return $sent;
    }
}

?>