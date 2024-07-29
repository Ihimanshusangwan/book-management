<?php
require_once __DIR__ . '/../src/Core/autoload.php';

use Core\Route;

$controllersDir = __DIR__ . '/../src/Controllers/';
$controllerFiles = scandir($controllersDir);

$routes = [];

echo "Starting route cache generation...\n";
echo "Scanning directory: $controllersDir\n";

foreach ($controllerFiles as $file) {
    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $controllerClass = pathinfo($file, PATHINFO_FILENAME);
        $controllerClassFullName = "Controllers\\$controllerClass";

        echo "Processing file: $file\n";
        echo "Attempting to load class: $controllerClassFullName\n";

        if (class_exists($controllerClassFullName)) {
            echo "Class $controllerClassFullName exists. Creating instance...\n";
            $controller = new $controllerClassFullName();

            $reflector = new \ReflectionClass($controller);
            $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                echo "Inspecting method: " . $method->getName() . "\n";
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    echo "Found route attribute for method: " . $method->getName() . "\n";
                    $instance = $attribute->newInstance();

                    $routes[$instance->method][$instance->path] = [
                        'controller' => $controllerClassFullName,
                        'method' => $method->getName(),
                        'role' => $instance->role
                    ];
                    echo "Added route: [" . $instance->method . "] " . $instance->path . "\n";
                }
            }
        } else {
            echo "Class $controllerClassFullName does not exist.\n";
        }
    }
}

file_put_contents(__DIR__ . '/../cache/routes.json', json_encode($routes, JSON_PRETTY_PRINT));
echo "Route cache generated successfully.\n";
