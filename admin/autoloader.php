<?php

require_once __DIR__ . '/../application/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Providers\Admin\ViewServiceProvider;

Autoloader::register();

require_once __DIR__ . '/../application/helpers.php';

// Admin View Composer 등록 (Service Provider 패턴)
ViewServiceProvider::register();
