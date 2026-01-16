<?php
namespace App\Auth;

use App\Core\BaseAuth;

class AdminAuth extends BaseAuth 
{


    /**
     * 세션 시작
     * 
     * @return void
     */
    protected static function startSession()
    {

        $sessionStatus = session_status();
        
        // 세션이 이미 시작된 경우
        if ($sessionStatus === PHP_SESSION_ACTIVE) {

            // 세션 저장 경로 확인
            $currentPath = session_save_path();
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $parentDir = dirname($docRoot);
            $expectedPath = $parentDir . '/admin2/session';
            
            // 경로가 비어있거나 잘못된 경우
            // 세션이 이미 시작되었으므로 경로를 변경할 수 없음
            // 하지만 이미 올바른 경로의 세션이라면 그대로 사용
            if (empty($currentPath) || $currentPath !== $expectedPath) {
                // 경로가 잘못되었지만 이미 시작된 세션은 재시작할 수 없음
                // 현재 세션을 그대로 사용 (데이터가 있을 수도 있음)
            }
            return;
        }
        
        // 세션이 시작되지 않은 경우
        // init_session() 함수가 로드되지 않았을 수 있으므로 직접 호출
        //if (!function_exists('init_session')) {

            // helpers.php가 로드되지 않았다면 직접 구현
            if ($sessionStatus === PHP_SESSION_NONE) {
                //dump('startSession333');
                $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
                //dump($docRoot);
                $parentDir = dirname($docRoot);
                //dump($parentDir);
                $sessionPath = $parentDir . '/www/admin2/session';
                //dump($sessionPath);
                
                if (!is_dir($sessionPath)) {
                    mkdir($sessionPath, 0755, true);
                }
                
                // 세션이 시작되기 전에만 경로 설정 가능
                session_save_path($sessionPath);
                session_start();
            }
            /*
        } else {
            init_session();

        }
                    */
    }

    /**
     * 현재 로그인된 유저 정보 가져오기
     * 
     * @return array|null 유저 정보 배열 또는 null
     */
    public static function user()
    {
        try {
            self::startSession();
            
            // 디버깅: 세션 데이터 확인
            if (!isset($_SESSION)) {
                return null;
            }
            
            return $_SESSION;
            

        } catch (\Exception $e) {
            // 세션 오류 발생 시 null 반환
            return null;
        }
    }

}
?>