<?php

class Router
{
    protected $routes = [];

    /*
    |--------------------------------------------------------------------------
    | Register Routes
    |--------------------------------------------------------------------------
    */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    private function addRoute($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'action' => $action
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dispatch Request
    |--------------------------------------------------------------------------
    */
    public function dispatch($method, $uri)
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        // Support both rewritten URLs (/public/products) and explicit front-controller
        // URLs (/public/index.php/products) across local Apache setups.
        $uri = str_replace('/pet-food-store/backend/public', '', $uri);
        $uri = trim($uri, '/');
        if (strpos($uri, 'index.php/') === 0) {
            $uri = substr($uri, strlen('index.php/'));
        } elseif ($uri === 'index.php') {
            $uri = '';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $uri) {
                return $this->callAction($route['action']);
            }
        }

        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
    }

    /*
    |--------------------------------------------------------------------------
    | Call Controller Action
    |--------------------------------------------------------------------------
    */
    private function callAction($action)
    {
        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);

            if (!class_exists($controller)) {
                throw new Exception("Controller $controller not found");
            }

            $controllerInstance = new $controller();

            if (!method_exists($controllerInstance, $method)) {
                throw new Exception("Method $method not found in $controller");
            }

            return call_user_func([$controllerInstance, $method]);
        }
    }
}