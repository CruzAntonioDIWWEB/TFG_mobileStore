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
        // First check if we have query parameters
        if (isset($_GET['controller'])) {
            $this->controller = ucfirst(strtolower($_GET['controller']));
            $this->action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';

            // Get any additional parameters
            $this->params = $_GET;
            unset($this->params['controller']);
            unset($this->params['action']);

            return;
        }

        // Otherwise, parse clean URL
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

        // Remove 'dashboard/TFG/Public' from segments if present 
        if (isset($segments[0]) && $segments[0] === 'dashboard') {
            if (isset($segments[1]) && $segments[1] === 'TFG') {
                if (isset($segments[2]) && $segments[2] === 'Public') {
                    $segments = array_slice($segments, 3);
                }
            }
        }

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
        // Add debugging
        error_log("Router dispatch - Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Router dispatch - Controller: " . $this->controller);
        error_log("Router dispatch - Action: " . $this->action);

        // Build controller class name
        $controllerName = $this->controller . 'Controller';
        $controllerClass = "Controllers\\{$controllerName}";

        // Check if controller file exists
        $controllerFile = __DIR__ . "/../Controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            // Fallback to HomeController if controller doesn't exist
            $controllerName = 'HomeController';
            $controllerClass = "Controllers\\HomeController";
            $controllerFile = __DIR__ . "/../Controllers/HomeController.php";
            $this->controller = 'Home';
            $this->action = 'index';
        }

        // Include the controller file
        require_once $controllerFile;

        // Check if controller class exists
        if (!class_exists($controllerClass)) {
            $this->show404();
            return;
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
            if (is_array($this->params)) {
                // If params is associative array from $_GET
                foreach ($this->params as $key => $value) {
                    $_GET[$key] = $value;
                }
            } else {
                // If params is indexed array from clean URL
                $_GET['id'] = $this->params[0] ?? null;

                // Add more parameters if needed
                for ($i = 1; $i < count($this->params); $i++) {
                    $_GET["param{$i}"] = $this->params[$i];
                }
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
        echo "<a href='/dashboard/TFG/Public/'>Go to Home</a>";
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
        // For now, use query parameters for compatibility
        $url = 'index.php?controller=' . strtolower($controller) . '&action=' . strtolower($action);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }

        return $url;
    }

    /**
     * Redirect to a URL
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
