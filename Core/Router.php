<?php

namespace Core;

/**
 * Router Class
 * Converts clean URLs to controller/action calls
 */
class Router
{
    private $controller = 'Home';
    private $action = 'index';
    private $params = [];

    public function __construct()
    {
        $this->parseUrl();
    }

    /**
     * Parse the URL and extract controller, action, and parameters
     */
    private function parseUrl()
    {
        // Get the URL path after the domain
        $url = $_SERVER['REQUEST_URI'];
        
        // Remove query string if present
        $url = strtok($url, '?');
        
        // Remove leading and trailing slashes
        $url = trim($url, '/');
        
        // If empty URL, use defaults (home page)
        if (empty($url)) {
            return;
        }

        // Split URL into segments
        $segments = explode('/', $url);
        
        // Remove empty segments
        $segments = array_filter($segments);
        
        // Reset array keys
        $segments = array_values($segments);

        // Extract controller (first segment)
        if (isset($segments[0]) && !empty($segments[0])) {
            $this->controller = ucfirst(strtolower($segments[0]));
        }

        // Extract action (second segment)
        if (isset($segments[1]) && !empty($segments[1])) {
            $this->action = strtolower($segments[1]);
        }

        // Extract parameters (remaining segments)
        if (count($segments) > 2) {
            $this->params = array_slice($segments, 2);
        }
    }

    /**
     * Get the controller name
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get the action name
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get parameters array
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a specific parameter by index
     * @param int $index Parameter index (0-based)
     * @param mixed $default Default value if parameter doesn't exist
     * @return mixed
     */
    public function getParam($index, $default = null)
    {
        return $this->params[$index] ?? $default;
    }

    /**
     * Dispatch the request to the appropriate controller and action
     */
    public function dispatch()
    {
        // Build controller class name
        $controllerClass = "Controllers\\{$this->controller}Controller";
        
        // Check if controller class exists
        if (!class_exists($controllerClass)) {
            // Fallback to HomeController if controller doesn't exist
            $controllerClass = "Controllers\\HomeController";
            $this->controller = 'Home';
            $this->action = 'index';
        }

        // Create controller instance
        $controller = new $controllerClass();

        // Check if action method exists
        if (!method_exists($controller, $this->action)) {
            // Fallback to index action if method doesn't exist
            $this->action = 'index';
            
            // If index doesn't exist either, show error
            if (!method_exists($controller, 'index')) {
                $this->show404();
                return;
            }
        }

        // Make parameters available in $_GET for backward compatibility
        if (!empty($this->params)) {
            $_GET['id'] = $this->params[0] ?? null;
            
            // Add more parameters if needed
            for ($i = 1; $i < count($this->params); $i++) {
                $_GET["param{$i}"] = $this->params[$i];
            }
        }

        // Call the controller action
        call_user_func([$controller, $this->action]);
    }

    /**
     * Show 404 error page
     */
    private function show404()
    {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The requested page could not be found.</p>";
        echo "<a href='/'>Go to Home</a>";
    }

    /**
     * Generate URL for a controller/action combination
     * @param string $controller Controller name
     * @param string $action Action name
     * @param array $params Additional parameters
     * @return string Generated URL
     */
    public static function url($controller, $action = 'index', $params = [])
    {
        $url = '/' . strtolower($controller) . '/' . strtolower($action);
        
        if (!empty($params)) {
            $url .= '/' . implode('/', $params);
        }
        
        return $url;
    }

    /**
     * Redirect to a clean URL
     * @param string $controller Controller name
     * @param string $action Action name
     * @param array $params Additional parameters
     */
    public static function redirect($controller, $action = 'index', $params = [])
    {
        $url = self::url($controller, $action, $params);
        header("Location: $url");
        exit();
    }
}

?>