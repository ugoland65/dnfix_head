<?php

namespace App\Core;

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // 네임스페이스를 기준으로 클래스 경로를 설정
            $prefix = 'App\\';
            $baseDir = __DIR__ . '/../'; // application 폴더 기준으로 설정

            // 네임스페이스 접두사를 확인
            if (strpos($class, $prefix) === 0) {
                $relativeClass = str_replace($prefix, '', $class);
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                } else {
                    throw new \Exception("File not found: $file");
                }
            }
        });
    }
}
