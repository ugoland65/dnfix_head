<?php

namespace App\Auth;

use Exception;
use App\Classes\Database;

/**
 * 인증 서비스
 */
class AuthService {

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

		} catch (Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}

	}

}

