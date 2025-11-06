<?php
namespace App\Core;

/*
user():

현재 로그인된 유저 정보를 반환합니다.
로그인되지 않은 경우 null 반환.
check():

로그인 상태를 확인합니다. 유저가 로그인되었으면 true, 그렇지 않으면 false 반환.
login(array $user):

유저 정보를 세션에 저장합니다. 로그인 시 호출.
logout():

유저 정보를 세션에서 제거합니다. 로그아웃 시 호출.
startSession():

PHP 세션이 시작되지 않은 경우 세션을 시작합니다.
*/

class AuthUser 
{
    // 세션 키 이름 (접속 정보 저장)
    protected static $sessionKey = 'user';

    /**
     * 현재 로그인된 유저 정보 가져오기
     * 
     * @return array|null 유저 정보 배열 또는 null
     */
    public static function user()
    {
        self::startSession();
        return $_SESSION[self::$sessionKey] ?? null;
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
     * 유저 정보 설정 (로그인)
     * 
     * @param array $user 유저 정보 배열 (예: ['id' => 1, 'name' => 'John Doe'])
     * @return void
     */
    public static function login(array $user)
    {
        self::startSession();
        $_SESSION[self::$sessionKey] = $user;
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
     * 세션 시작
     * 
     * @return void
     */
    protected static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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

    public static function getDeviceType()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $mobilePatterns = [
            'iPhone', 'iPad', 'Android', 'BlackBerry', 'Windows Phone',
            'webOS', 'Opera Mini', 'IEMobile', 'Mobile'
        ];

        foreach ($mobilePatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return 'Mobile';
            }
        }

        return 'PC';
    }

    /**
     * 접속자 정보를 배열로 반환
     *
     * @return array [ip, domain, deviceType]
     */
    public static function getConnectionInfo()
    {
        return [
            'ip' => self::getIp(),
            'domain' => self::getDomain(),
            'deviceType' => self::getDeviceType(),
        ];
    }

}
