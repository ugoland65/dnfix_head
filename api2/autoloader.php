<?php

// Composer autoload - 외부 패키지 로드 (가장 먼저!)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../application/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Providers\Admin\ViewServiceProvider;

Autoloader::register();

require_once __DIR__ . '/../application/helpers.php';

// Admin View Composer 부트스트랩 (Service Provider 패턴)
ViewServiceProvider::boot();
