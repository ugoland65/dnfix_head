<?php
namespace App\Auth;

use App\Core\BaseAuth;

class OnadbAuth extends BaseAuth 
{

    // 세션 키 
    protected static $sessionKey = 'onadb';

    /**
     * 로그인 세션 저장 
     * 
     * @param array $user 유저 정보 배열 (예: ['id' => 1, 'name' => 'John Doe'])
     */
    public static function login(array $user)
    {
        self::startSession();
        $_SESSION[self::$sessionKey] = $user;
    }

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
            $expectedPath = $parentDir . '/session';
            
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
        if (!function_exists('init_session')) {
            // helpers.php가 로드되지 않았다면 직접 구현
            if ($sessionStatus === PHP_SESSION_NONE) {
                $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
                $parentDir = dirname($docRoot);
                $sessionPath = $parentDir . '/session';
                
                if (!is_dir($sessionPath)) {
                    mkdir($sessionPath, 0755, true);
                }
                
                // 세션이 시작되기 전에만 경로 설정 가능
                session_save_path($sessionPath);
                session_start();
            }
        } else {
            init_session();
        }
    }

    /**
     * 유저 정보 제거 (로그아웃)
     * 
     * @return void
     */
    public static function logout()
    {
        self::startSession();
        unset($_SESSION[self::$sessionKey]);
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
            
            // 세션 키 확인
            if (isset($_SESSION[self::$sessionKey])) {
                return $_SESSION[self::$sessionKey];
            }
            
            // 전체 세션 데이터 확인 (디버깅용)
            // 다른 키에 데이터가 있을 수 있음
            return null;
        } catch (\Exception $e) {
            // 세션 오류 발생 시 null 반환
            return null;
        }
    }

    /**
     * 유저가 로그인 상태인지 확인
     * 
     * @return bool 로그인 상태(true: 로그인됨, false: 로그인되지 않음)
     */
    public static function check()
    {
        self::startSession();
        return isset($_SESSION[self::$sessionKey]);
    }

    /**
     * 세션 데이터 업데이트 (Laravel 스타일)
     * 
     * @param array $data 업데이트할 데이터
     * @return bool 성공 여부
     */
    public static function update(array $data)
    {
        self::startSession();
        
        if (!isset($_SESSION[self::$sessionKey])) {
            return false;
        }
        
        // 기존 세션 데이터와 병합
        $_SESSION[self::$sessionKey] = array_merge($_SESSION[self::$sessionKey], $data);
        
        return true;
    }

    public static function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }

    public static function getDomain()
    {
        return $_SERVER['HTTP_HOST'] ?? 'UNKNOWN';
    }

}
