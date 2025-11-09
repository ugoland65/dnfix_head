<?php

namespace App\Providers\Onadb;

use App\Core\View;
use App\Services\ProductCommentService;

/**
 * Onadb View Service Provider
 * 
 * Onadb 사이트 전용 View Composer를 중앙에서 관리합니다.
 */
class ViewServiceProvider
{

    /**
     * View Composer 등록
     */
    public static function register(): void
    {
        // Onadb 전용 Composer
        self::registerOnadbComposers();
    }
    
    /**
     * Onadb 뷰 Composer 등록
     */
    private static function registerOnadbComposers(): void
    {
        View::composer('onadb.*', function($view) {
            // 세션 시작
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // 세션 데이터
            $view->with([
                '_sess_id' => $_SESSION['sess_id'] ?? null,
                '_sess_key' => $_SESSION['sess_key'] ?? null,
                '_user_nick' => $_SESSION['user_nick'] ?? '',
                '_user_point' => $_SESSION['user_point'] ?? 0,
            ]);
            
            // 레이아웃 설정
            $view->with('_side_layout_show', 'on');
            
            // 메타 데이터
            $view->with([
                'meta_title' => '오나디비',
                'meta_site_name' => '오나디비',
                'meta_description' => '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.',
                'meta_keywords' => '오나홀, 추천, 평점, 순위, 리뷰',
                'meta_url' => 'https://onadb.net',
            ]);
            
            // DB 데이터 - 최근 댓글
            self::loadRecentComments($view);
        });
    }
    
    /**
     * 최근 댓글 로드 (Service 사용)
     */
    private static function loadRecentComments($view): void
    {
        try {
            
            $commentService = new ProductCommentService();
            $recent_comments = $commentService->getRecentComments();
            
            $view->with('recent_comments', $recent_comments);
            
        } catch (\Exception $e) {
            $view->with('recent_comments', []);
        }
    }
}

