<?php

use App\Core\Application;
use App\Core\Config;

/**
 * 애플리케이션 컨테이너에 접근하는 헬퍼 함수
 * 
 * @param string|null $key 가져올 서비스 키
 * @return mixed 서비스 키가 제공되면 해당 서비스, 아니면 애플리케이션 인스턴스
 */
function app($key = null)
{
    $app = Application::getInstance();
    
    if ($key === null) {
        return $app;
    }
    
    return $app->get($key);
} 

/**
 * 설정값 접근 헬퍼 함수
 */
if (!function_exists('config')) {
    function config($key = null, $default = null) {
        if ($key === null) {
            return Config::all();
        }
        return Config::get($key, $default);
    }
}

if (!function_exists('dump')) {
    function dump(...$vars)
    {
        echo "<pre style='background:#222;color:#eee;padding:10px;border-radius:5px;'>";
        foreach ($vars as $var) {
            print_r($var);
            echo "\n";
        }
        echo "</pre>";
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        dump(...$vars);
        exit;
    }
}
