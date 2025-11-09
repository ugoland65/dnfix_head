<?php

namespace App\Providers\Admin;

use App\Core\View;

/**
 * Admin View Service Provider
 * 
 * Admin 사이트 전용 View Composer를 중앙에서 관리합니다.
 */
class ViewServiceProvider
{
    /**
     * View Composer 등록
     */
    public static function register(): void
    {
        // Admin 전용 Composer
        View::composer('admin.*', function($view) {
            self::bindSessionData($view);
            self::bindAdminData($view);
        });
    }
    
    /**
     * 세션 데이터 바인딩
     */
    private static function bindSessionData($view): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $view->with([
            '_sess_id' => $_SESSION['sess_id'] ?? null,
            '_ad_name' => $_SESSION['ad_name'] ?? '',
            '_ad_nick' => $_SESSION['ad_nick'] ?? '',
            '_ad_level' => $_SESSION['ad_level'] ?? 0,
        ]);
    }
    
    /**
     * Admin 공통 데이터 바인딩
     */
    private static function bindAdminData($view): void
    {
        // TODO: Admin 공통 데이터 추가
        // 예: 메뉴 데이터, 권한 정보 등
    }
}

