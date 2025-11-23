<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Auth\OnadbAuth;

/**
 * Onadb 전용 인증 미들웨어
 */
class OnadbAuthMiddleware implements Middleware
{
    public function handle(): bool
    {
        // OnadbAuth::check()를 사용하여 로그인 상태 확인
        if (!OnadbAuth::check()) {
            // 로그인 페이지로 리다이렉트
            $basePath = $this->getBasePath();
            header('Location: ' . $basePath . '/login');
            exit;
        }

        return true;
    }

    /**
     * 베이스 경로 가져오기
     */
    private function getBasePath(): string
    {
        $check_domain = $_SERVER['HTTP_HOST'] ?? '';
        $check_domain = str_replace("www.", "", $check_domain);
        
        if ($check_domain == "dnfixhead.mycafe24.com") {
            return '';
        }
        
        return '/onadb';
    }
}