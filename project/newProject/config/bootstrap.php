<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('UTC');

spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../repositories/',
        __DIR__ . '/../services/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../utils/',
        __DIR__ . '/../config/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/../utils/Exceptions.php';
