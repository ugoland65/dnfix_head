<?php

namespace App\Providers\Onadb;

use Exception;
use App\Core\View;
use App\Services\ProductCommentService;
use App\Auth\OnadbAuth;
use App\Models\UserModel;

/**
 * Onadb View Service Provider
 * 
 * Onadb 사이트 전용 View Composer를 중앙에서 관리합니다.
 */
class ViewServiceProvider
{

    /**
     * View Composer 부트스트랩
     * Laravel 컨벤션에 따라 boot 메서드 사용
     */
    public static function boot(): void
    {
        // Onadb 전용 Composer
        self::bootViewComposers();
    }
    
    /**
     * Onadb 뷰 Composer 등록
     */
    private static function bootViewComposers(): void
    {

        $defaults = config('onadb.view_defaults');

        View::composer('onadb.*', function($view) use ($defaults) {
            
            // OnadbAuth::user() 내부에서 init_session()을 호출하므로 여기서는 호출하지 않음
            try {
                $sessionData = OnadbAuth::user();
                
                // 디버깅: 세션 상태 확인
                if (!$sessionData && session_status() === PHP_SESSION_ACTIVE && isset($_SESSION)) {
                    // 세션이 활성화되어 있지만 데이터가 없는 경우
                    // 전체 세션 키 확인
                    $allSessionKeys = array_keys($_SESSION ?? []);
                }
                
                if ($sessionData && is_array($sessionData)) {
                    // user_idx가 있으면 UserModel로 user_point 조회
                    $userPoint = 0;
                    if (isset($sessionData['user_idx']) && !empty($sessionData['user_idx'])) {
                        try {
                            $user = UserModel::find($sessionData['user_idx']);
                            if ($user) {
                                $userPoint = (int)$user->user_point;
                                $userScore = (int)$user->user_score;
                                $userLevel = (int)$user->user_level;
                            }
                        } catch (Exception $e) {
                            // 조회 실패 시 기본값 0 사용
                            $userPoint = 0;
                            $userScore = 0;
                            $userLevel = 0;
                        }
                    }
                    
                    $view->with('auth', [
                        'is_logged_in' => true,
                        'id' => $sessionData['user_id'] ?? null,
                        'nick' => $sessionData['user_nick'] ?? null,
                        'email' => $sessionData['user_email'] ?? null,
                        'point' => $userPoint,
                        'score' => $userScore,
                        'level' => $userLevel,
                    ]);
                } else {
                    // 디버깅: 세션 정보 확인
                    $debugInfo = [];
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        $debugInfo['session_status'] = 'active';
                        $debugInfo['session_id'] = session_id();
                        $debugInfo['session_save_path'] = session_save_path();
                        $debugInfo['session_keys'] = isset($_SESSION) ? array_keys($_SESSION) : [];
                    } else {
                        $debugInfo['session_status'] = session_status();
                    }
                    
                    $view->with('auth', [
                        'is_logged_in' => false,
                        'id' => null,
                        'nick' => null,
                        'error' => null,
                        'debug' => $debugInfo, // 디버깅 정보
                    ]);
                }
            } catch (Exception $e) {
                // 에러 발생 시 기본값 설정
                $view->with('auth', [
                    'is_logged_in' => false,
                    'id' => null,
                    'nick' => null,
                    'point' => 0,
                    'error' => $e->getMessage(),
                ]);
            }
            
            // 뷰에 이미 전달된 데이터 가져오기
            $data = $view->getData();
            
           // 1) meta: 평탄화 주입 (기존 `$meta_title` 등 유지)
           foreach (($defaults['meta'] ?? []) as $k => $v) {
                if (!array_key_exists($k, $data) || self::isEmpty($data[$k])) {
                    $view->with($k, $v);
                }
            }

            // 2) 기타 그룹: 배열 통째로 주입 (충돌 방지)
            foreach ($defaults as $group => $values) {
                if ($group === 'meta') continue;

                // 이미 동일 이름의 배열이 있으면 "기존 > 기본" 규칙으로 얕은 병합
                if (array_key_exists($group, $data) && is_array($data[$group])) {
                    $merged = $values;
                    foreach ($data[$group] as $k => $v) {
                        if (self::isEmpty($v)) continue;
                        $merged[$k] = $v;
                    }
                    $view->with($group, $merged);
                } else {
                    // 해당 키가 없으면 통째로 주입
                    if (!array_key_exists($group, $data) || self::isEmpty($data[$group])) {
                        $view->with($group, $values);
                    }
                }
            }
            
            /*
            // 레이아웃 설정
            $view->with('_side_layout_show', 'on');

            // DB 데이터 - 최근 댓글
            self::loadRecentComments($view);
            */
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
            
        } catch (Exception $e) {
            $view->with('recent_comments', []);
        }
    }

    /**
     * 값이 비어있는지 확인
     */
    private static function isEmpty($val): bool
    {
        // null, 빈문자열은 비어있다고 간주. 숫자 0, false는 유효값으로 인정.
        return $val === null || (is_string($val) && trim($val) === '');
    }


}

