<?php

require_once __DIR__ . '/../src/core/Router.php';
require_once __DIR__ . '/../src/core/Route.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/core/Session.php';
require_once __DIR__ . '/../src/core/Auth.php';
require_once __DIR__ . '/../src/core/Response.php';
require_once __DIR__ . '/../src/models/Book.php';
require_once __DIR__ . '/../src/models/User.php';

// Manually require all controller files
$controllersDir = __DIR__ . '/../src/controllers/';
$controllerFiles = scandir($controllersDir);

foreach ($controllerFiles as $file) {
    if ($file !== '.' && $file !== '..') {
        require_once $controllersDir . $file;
    }
}

$routes = [];

foreach ($controllerFiles as $file) {
    if ($file !== '.' && $file !== '..') {
        $controllerClass = pathinfo($file, PATHINFO_FILENAME);
        $controller = new $controllerClass();
        
        $reflector = new ReflectionClass($controller);
        $methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Route::class);
            
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                
                $routes[$instance->method][$instance->path] = [
                    'controller' => $controllerClass,
                    'method' => $method->getName(),
                    'role' => $instance->role
                ];
            }
        }
    }
}

file_put_contents(__DIR__ . '/../cache/routes.json', json_encode($routes, JSON_PRETTY_PRINT));
echo "Route cache generated successfully.";
