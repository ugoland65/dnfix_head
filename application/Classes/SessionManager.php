<?php

namespace App\Classes;

class SessionManager
{
    // 세션 시작
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
			session_save_path($_SERVER['DOCUMENT_ROOT']."/user/session");
            session_start();
        }
    }

    // 세션 값 설정
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    // 세션 값 가져오기
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    // 세션 값 제거
    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    // 세션 초기화 (모든 값 제거)
    public static function clear()
    {
        self::start();
        session_unset();
        session_destroy();
    }
}
