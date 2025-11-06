<?php
namespace App\Core;

use App\Classes\Database;
use App\Classes\QueryBuilder;

use Exception;
use BadMethodCallException;

class BaseModel {

    protected $db;
	protected $queryBuilder;
	protected $table;
    protected $primaryKey = 'idx';
    protected static $instances = [];

    public function __construct() {
        
		try {
            // app() 함수를 사용하여 의존성 가져오기
            $this->db = app('db');
			
            // QueryBuilder 초기화
            $this->queryBuilder = app('queryBuilder');

        } catch (Exception $e) {
            throw new Exception("BaseClass Initialization Error: " . $e->getMessage());
        }
    }

    /**
     * 정적 메소드 호출을 위한 인스턴스 반환
     * @return static
     */
    public static function getInstance() {
        $class = static::class;
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new static();
        }
        return static::$instances[$class];
    }

    // 정적(static) 메서드 처리
    public static function __callStatic($method, $parameters) {
        // 싱글톤 인스턴스 사용
        $instance = static::getInstance();

        // 인스턴스 메서드 호출 (존재하지 않는 메서드는 __call에서 처리)
        return $instance->$method(...$parameters);
    }

    /**
     * 존재하지 않는 메서드 호출 처리
     * QueryBuilder로 메서드 호출을 전달
     */
    public function __call($method, $parameters) {
        // 메서드가 인스턴스에 존재하는지 확인
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }
        
        // 쿼리 빌더 초기화
        $queryBuilder = $this->queryBuilder();
        
        // 쿼리 빌더에 메서드가 존재하는지 확인
        if (method_exists($queryBuilder, $method)) {
            // primaryKey 설정이 필요한 메서드인 경우 (예: find)
            if ($method === 'find' && !empty($parameters)) {
                return $queryBuilder->find($parameters[0], $this->primaryKey);
            }
            
            // 체이닝을 위해 QueryBuilderWrapper 반환
            $chainableMethods = ['where', 'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween', 'whereRaw', 'orderBy', 'groupBy', 'limit', 'offset'];
            if (in_array($method, $chainableMethods)) {
                return new QueryBuilderWrapper($queryBuilder->$method(...$parameters), $this);
            }
            
            // 일반 메서드 호출
            return $queryBuilder->$method(...$parameters);
        }
        
        throw new BadMethodCallException("Method $method does not exist in " . static::class . " or QueryBuilder");
    }

    /**
     * 쿼리 빌더 반환
     * @return \App\Classes\QueryBuilder
     */
    public function queryBuilder() {
        if (empty($this->table)) {
            throw new Exception("Table name is not defined in " . static::class);
        }
        return app('queryBuilder')->table($this->table);
    }

    /**
     * 정적 메소드로 쿼리 빌더 사용
     * @return \App\Classes\QueryBuilder
     */
    public static function query() {
        return static::getInstance()->queryBuilder();
    }

    /**
     * 기본 키로 모델 찾기
     * @param mixed $id 찾을 레코드의 ID
     * @return mixed 찾은 레코드 또는 null
     */
    public static function find($id) {
        return static::query()->find($id, static::getInstance()->primaryKey);
    }

    /**
     * 기본 키로 모델을 찾거나 예외 발생
     * @param mixed $id 찾을 레코드의 ID
     * @return mixed 찾은 레코드
     * @throws \Exception 레코드를 찾지 못한 경우
     */
    public static function findOrFail($id) {
        $result = static::find($id);
        if ($result === null) {
            $class = static::class;
            throw new \Exception("No query results for model [{$class}] {$id}");
        }
        return $result;
    }

    // 테이블 이름 반환
    public function getTable()
    {
        return $this->table;
    }
}

/**
 * QueryBuilder 체이닝을 위한 래퍼 클래스
 */
class QueryBuilderWrapper {
    private $queryBuilder;
    private $model;
    
    public function __construct($queryBuilder, $model) {
        $this->queryBuilder = $queryBuilder;
        $this->model = $model;
    }
    
    public function __call($method, $parameters) {
        // 체이닝 메서드들
        $chainableMethods = ['where', 'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween', 'whereRaw', 'orderBy', 'groupBy', 'limit', 'offset'];
        
        if (in_array($method, $chainableMethods)) {
            $result = $this->queryBuilder->$method(...$parameters);
            return new self($result, $this->model);
        }
        
        // 종료 메서드들 (실제 실행)
        return $this->queryBuilder->$method(...$parameters);
    }
}
