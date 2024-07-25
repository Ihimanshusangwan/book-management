<?php

require_once __DIR__ . '/../src/core/Router.php';
require_once __DIR__ . '/../src/core/Route.php';
require_once __DIR__ . '/../src/core/Auth.php';
require_once __DIR__ . '/../src/core/Session.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/core/Middleware.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Book.php';
require_once __DIR__ . '/../src/controllers/UserController.php';
require_once __DIR__ . '/../src/controllers/BookController.php';

$auth = new Auth(new User(), new Session());
$router = new Router($auth);

$router->loadRoutesFromCache(__DIR__ . '/../cache/routes.json');

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

if (($pos = strpos($path, '?')) !== false) {
    $path = substr($path, 0, $pos);
}


$router->resolve($method, $path);
