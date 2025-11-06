<?php
namespace App\Core;

use App\Classes\Database;
use App\Classes\QueryBuilder;

class Application
{
    private static $instance = null;
    private $container = [];

    /**
     * 싱글톤 패턴으로 인스턴스 반환
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자 - 기본 서비스 등록
     */
    private function __construct()
    {
        // 데이터베이스 연결
        $this->container['db'] = function () {
            return Database::getInstance()->getConnection();
        };

        // 쿼리 빌더
        $this->container['queryBuilder'] = function () {
            return new QueryBuilder($this->get('db'));
        };

        // 필요한 다른 서비스들 등록...
    }

    /**
     * 서비스 등록
     */
    public function register($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * 서비스 가져오기
     */
    public function get($key)
    {
        if (!isset($this->container[$key])) {
            return null;
        }

        // 클로저인 경우 실행하여 인스턴스 반환
        if ($this->container[$key] instanceof \Closure) {
            return $this->container[$key]();
        }

        return $this->container[$key];
    }

    /**
     * 서비스 존재 여부 확인
     */
    public function has($key)
    {
        return isset($this->container[$key]);
    }
} 