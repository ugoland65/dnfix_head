<?php

namespace App\Classes;

use App\Classes\Database;
use Exception;

/**
 * Laravel과 유사한 DB 트랜잭션 헬퍼 클래스
 */
class DB
{
    /**
     * 트랜잭션 실행 (Laravel DB::transaction과 동일)
     * 
     * @param callable $callback
     * @param int $attempts 재시도 횟수
     * @return mixed
     * @throws Exception
     */
    public static function transaction(callable $callback, int $attempts = 1)
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();
        
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            try {
                $db->beginTransaction();
                
                $result = $callback();
                
                $db->commit();
                
                return $result;
                
            } catch (Exception $e) {
                $db->rollBack();
                
                // 마지막 시도가 아니면 재시도
                if ($currentAttempt < $attempts) {
                    continue;
                }
                
                // 마지막 시도에서도 실패하면 예외 던지기
                throw $e;
            }
        }
    }
    
    /**
     * 트랜잭션 시작
     */
    public static function beginTransaction()
    {
        Database::getInstance()->beginTransaction();
    }
    
    /**
     * 트랜잭션 커밋
     */
    public static function commit()
    {
        Database::getInstance()->commit();
    }
    
    /**
     * 트랜잭션 롤백
     */
    public static function rollback()
    {
        Database::getInstance()->rollBack();
    }
    
    /**
     * 연결 인스턴스 반환
     */
    public static function connection()
    {
        return Database::getInstance()->getConnection();
    }
}
