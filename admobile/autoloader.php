<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../application/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Providers\Admin\ViewServiceProvider;

Autoloader::register();

require_once __DIR__ . '/../application/helpers.php';

ViewServiceProvider::boot();
