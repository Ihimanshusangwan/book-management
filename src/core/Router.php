<?php

class Router
{
    private $routes = [];
    private $middleware;
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->middleware = new Middleware($auth);
    }

    public function loadRoutesFromCache($cacheFile)
    {
        $routes = json_decode(file_get_contents($cacheFile), true);

        foreach ($routes as $method => $paths) {
            foreach ($paths as $path => $route) {
                $controllerClass = $route['controller'];
                $controller = new $controllerClass();
                $callback = [$controller, $route['method']];
                $this->routes[$method][$path] = [
                    'callback' => $callback,
                    'role' => $route['role']
                ];
            }
        }
    }

    public function resolve($method, $path)
    {
        foreach ($this->routes[$method] as $routePath => $route) {
            $pattern = preg_replace('/{(\w+)}/', '(\w+)', $routePath);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $params = $matches;

                $this->middleware->handle(function() use ($route, $params) {
                    call_user_func_array($route['callback'], $params);
                }, $route['role']);

                return;
            }
        }

        $this->sendResponse(404, ['success' => false, 'message' => 'Not Found']);
    }

    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
