<?php

namespace App\Auth;

final class AdmobileSession
{
    private const SESSION_DIRECTORY = '/admin2/session';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $projectRoot = dirname(__DIR__, 2);
        $sessionPath = $projectRoot . self::SESSION_DIRECTORY;
        if (!is_dir($sessionPath)) {
            mkdir($sessionPath, 0755, true);
        }

        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        session_save_path($sessionPath);
        session_start();
    }

    public static function isAuthenticated(): bool
    {
        self::start();

        return !empty($_SESSION['sess_id']) && !empty($_SESSION['sess_idx']);
    }

    public static function login(array $admin): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['sess_id'] = (string)($admin['ad_id'] ?? '');
        $_SESSION['sess_idx'] = (int)($admin['idx'] ?? 0);
        $_SESSION['sess_name'] = (string)($admin['ad_name'] ?? '');
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}
