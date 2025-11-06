<?php

namespace App\Core;

use App\Classes\Database;

class AuthAdmin
{
    // 세션 키 이름 (접속 정보 저장)
    protected static $sessionKey = 'admin';

    /**
     * 세션 시작
     * 
     * @return void
     */
    protected static function startSession()
    {

		$customPath = $_SERVER['DOCUMENT_ROOT'] . "/admin2/session";

		if (session_status() === PHP_SESSION_NONE) {
		//if (session_status() !== PHP_SESSION_ACTIVE) {
			ini_set("session.use_trans_sid", 0);
			ini_set("url_rewriter.tags", "");
			session_save_path($customPath);
			session_start();
		}

    }


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


    public static function getSession($key)
    {
		self::startSession(); // 세션이 시작되지 않았다면 시작
		return $_SESSION[$key];
    }

	public static function getSessionId()
	{
		self::startSession(); // 세션이 시작되지 않았다면 시작
		return session_id()."||".session_save_path()."||".$_SERVER['DOCUMENT_ROOT']."/user/session";  // 현재 세션 ID 반환
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
		self::startSession(); // 세션이 시작되지 않았다면 시작

		$action_time = date('Y-m-d H:i:s');
		$_sess_id = $_SESSION["sess_id"] ?? null;
		$_sess_name = $_SESSION["sess_name"] ?? null;

        return [
			"date" => $action_time,
			"id" => $_sess_id,
			"name" => $_sess_name,
            'ip' => self::getIp(),
            'domain' => self::getDomain(),
            'deviceType' => self::getDeviceType(),
        ];
    }


    /**
     * 구버전 비밀번호 암호화 결과 반환
     *
     * @param string $password - 입력된 비밀번호
     * @param string $salt - 비밀번호 암호화에 사용할 salt 값
     * @return string|false - MySQL PASSWORD() 함수 결과 또는 false
     */
    public static function getLegacyPassword($password, $salt = "sjqksk")
    {

        // 입력된 비밀번호와 salt 결합
        $value = $password . $salt;

        // MySQL PASSWORD() 함수 호출 쿼리
        $query = "SELECT PASSWORD('$value') AS pass";

        try {

			$db = Database::getInstance()->getConnection(); // 정확한 메서드 호출
			if (!$db) {
				throw new Exception("데이터베이스 연결이 설정되지 않았습니다.");
			}

			$result = $db->query($query)->fetch();

			return $result['pass'] ?? false;

		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}
		/*
        try {

            // 데이터베이스 연결 및 쿼리 실행
            $db = Database::getConnection(); // Database 클래스 사용
            $result = $db->query($query)->fetch();

            // 결과 반환
            return $result['pass'] ?? false;

        } catch (\PDOException $e) {
            // 오류 발생 시 false 반환
            error_log("Database error: " . $e->getMessage());
            return false;
        }
		*/

	}

}
