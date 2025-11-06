<?php

require_once '../application/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\Config;

Autoloader::register(); // Autoloader 등록

// 헬퍼 함수 로드
require_once '../application/helpers.php';

// application/config 디렉터리의 모든 설정 파일 자동 로드
$configDir = '../application/config/';
$configs = [];

if (is_dir($configDir)) {
    $files = glob($configDir . '*.php');
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        $configs[$filename] = require $file;
    }
}

// application을 app으로 단축키 설정
if (isset($configs['config'])) {
    $configs['app'] = $configs['config'];
}

Config::load($configs);

