<?php 
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';

    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    } else {
        echo "File does not exist: $file\n"; 
        exit();
    }
});
