<?php

use EK\Bootstrap;

$autoloaderPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloaderPath)) {
    throw new RuntimeException('You must run `composer install` to install the dependencies');
}

$autoloader = require $autoloaderPath;

// Add a global var to tell where the base dir is
define('BASE_DIR', dirname(__DIR__, 1));

// Ensure the cache folder exists
if (!file_exists(BASE_DIR . '/cache')) {
    mkdir(BASE_DIR . '/cache');
}

// Bootstrap the system
return [
    new Bootstrap($autoloader),
    $autoloader
];
