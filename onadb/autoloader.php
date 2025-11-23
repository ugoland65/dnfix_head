<?php

// Composer autoload - 외부 패키지 로드 (가장 먼저!)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../application/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Providers\Onadb\ViewServiceProvider;

Autoloader::register();

require_once __DIR__ . '/../application/helpers.php';

// 세션 초기화 (경로 설정 포함) - ViewServiceProvider보다 먼저 실행
if (function_exists('init_session') && session_status() === PHP_SESSION_NONE) {
    init_session();
}

// Onadb View Composer 부트스트랩 (Service Provider 패턴)
ViewServiceProvider::boot();
