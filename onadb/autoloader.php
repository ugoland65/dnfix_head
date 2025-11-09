<?php

require_once __DIR__ . '/../application/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Providers\Onadb\ViewServiceProvider;

Autoloader::register();

require_once __DIR__ . '/../application/helpers.php';

// Onadb View Composer 등록 (Service Provider 패턴)
ViewServiceProvider::register();
