<?php

require_once __DIR__ . '/../src/Core/autoload.php';

use Models\User;
use Core\Auth;
use Core\Session;
use Core\Router;

$auth = new Auth(new User(), new Session());
$router = new Router($auth);

$router->loadRoutesFromCache(__DIR__ . '/../cache/routes.json');

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

if (($pos = strpos($path, '?')) !== false) {
    $path = substr($path, 0, $pos);
}


$router->resolve($method, $path);
