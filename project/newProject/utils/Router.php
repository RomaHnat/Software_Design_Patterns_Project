<?php

class Router {

    private array $routes = [];
    public function get(string $path, string $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $uri = strtok($uri, '?');

        $scriptName = $_SERVER['SCRIPT_NAME']; // /project/newProject/public/index.php
        $scriptDir = dirname($scriptName); // /project/newProject/public

        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        elseif ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = trim($uri, '/');

        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = $this->convertPathToRegex($route['path']);

                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches);

                    [$controllerName, $methodName] = explode('@', $route['handler']);

                    $this->callController($controllerName, $methodName, $matches);
                    return;
                }
            }
        }

        throw new NotFoundException("Route not found: $method $uri");
    }

    private function convertPathToRegex(string $path): string {
        $path = trim($path, '/');
        $path = preg_replace('/\{\w+\}/', '([^\/]+)', $path);
        return '/^' . str_replace('/', '\/', $path) . '$/';
    }

    private function callController(string $controllerName, string $methodName, array $params): void {
        if (!class_exists($controllerName)) {
            throw new NotFoundException("Controller not found: $controllerName");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            throw new NotFoundException("Method not found: $controllerName::$methodName");
        }

        call_user_func_array([$controller, $methodName], $params);
    }
}
