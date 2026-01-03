<?php

namespace App\Providers\Admin;

use App\Core\View;
use App\Models\AdminModel;

/**
 * Admin View Service Provider
 * 
 * Admin 사이트 전용 View Composer를 중앙에서 관리합니다.
 */
class ViewServiceProvider
{
    /**
     * View Composer 부트스트랩
     * Laravel 컨벤션에 따라 boot 메서드 사용
     */
    public static function boot(): void
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
        
        $auth = [];

        if( !empty($_SESSION['sess_idx']) ){
            $admin = AdminModel::find($_SESSION['sess_idx']);
            if( $admin ){
                $auth['ad_id'] = $admin->ad_id;
                $auth['ad_name'] = $admin->ad_name;
                $auth['ad_nick'] = $admin->ad_nick;
                $auth['ad_level'] = $admin->ad_level;
            }
        }

        $view->with([
            'auth' => $auth,
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

