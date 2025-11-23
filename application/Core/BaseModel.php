<?php
namespace App\Core;

use App\Classes\Database;
use App\Classes\QueryBuilder;

use Exception;
use BadMethodCallException;

class BaseModel {

    use HasRelationships;

    protected $db;
	protected $queryBuilder;

	protected $table = '';
    protected $primaryKey = 'idx';
    protected static $instances = [];

    /**
     * 테이블명 → 모델 인스턴스 매핑 (ModelObject에서 fillable/casts 접근용)
     */
    protected static $tableModelMap = [];

    /**
     * 라라벨 스타일
     */
    protected $fillable = []; // 허용 컬럼(우선순위 높음)
    protected $guarded  = ['*']; // 차단 컬럼(* = 모두 차단)
    protected $casts = []; //casts

    /**
     * 생성자
     * - DI 컨테이너에서 DB/QueryBuilder를 주입받고
     * - 테이블명 기준으로 현재 모델 인스턴스를 전역 매핑에 등록한다.
     * @throws Exception 초기화 실패 시
     */
    public function __construct() {
        
		try {
            // app() 함수를 사용하여 의존성 가져오기
            $this->db = app('db');
			
            // QueryBuilder 초기화
            $this->queryBuilder = app('queryBuilder');

            if (empty($this->table)) {
                //throw new Exception('Table name must be defined in ' . static::class);
            }

            // 테이블명으로 현재 모델 인스턴스 등록
            static::$tableModelMap[$this->table] = $this;

        } catch (Exception $e) {
            throw new Exception("BaseModel Initialization Error: " . $e->getMessage());
        }
    }


    /**
     * 싱글톤 인스턴스
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


    /**
     * 정적 호출 매직 메서드
     * - with()는 EagerLoadBuilder를 반환
     * - 그 외 등록된 QueryBuilder 메서드를 위임
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters) {
        // with() 지원: 간단 eager loading 래퍼 반환
        if ($method === 'with') {
            $instance = static::getInstance();
            $relations = isset($parameters[0]) ? (array)$parameters[0] : [];
            return new \App\Core\EagerLoadBuilder($instance, $relations);
        }
        
        // updateOrCreate 지원
        if ($method === 'updateOrCreate') {
            return call_user_func_array([static::class, 'updateOrCreate'], $parameters);
        }
        
        // 싱글톤 인스턴스 사용
        $instance = static::getInstance();

        // QueryBuilder 메서드들을 직접 처리
        $queryBuilderMethods = [
            'select', 'where', 'orWhere', 'whereIn', 'whereNotIn', 'whereBetween', 
            'whereNotBetween', 'whereNull', 'whereNotNull', 'whereDate', 'whereRaw', 'orWhereRaw', 'when', 'orderBy', 'orderByRaw',
            'groupBy', 'limit', 'join', 'leftJoin', 'rightJoin', 'innerJoin', 'joinRaw',
            'get', 'first', 'paginate', 'count', 'exists', 'value', 'keyBy', 'toArray',
            'pluck', 'filter', 'values', 'all'
        ];

        if (in_array($method, $queryBuilderMethods)) {
            // QueryBuilder 인스턴스 생성
            $builder = $instance->queryBuilder();
            
            // 메서드 호출
            return call_user_func_array([$builder, $method], $parameters);
        }

        // 인스턴스 메서드 호출 (존재하지 않는 메서드는 __call에서 처리)
        return call_user_func_array([$instance, $method], $parameters);
    }


    /**
     * 테이블명으로 매핑된 모델 인스턴스 반환
     * @param string $table
     * @return static|null
     */    
    public static function forTable($table)
    {
        return isset(static::$tableModelMap[$table]) ? static::$tableModelMap[$table] : null;
    }


    /**
     * 인스턴스 호출 매직 메서드
     * - with()는 EagerLoadBuilder 반환
     * - QueryBuilder 메서드가 존재하면 위임
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        
        // 메서드가 인스턴스에 존재하는지 확인
        // @deprecated 항상 false 이므로 삭제예정
        if (method_exists($this, $method)) {
            //return $this->$method(...$parameters);
            return call_user_func_array([$this, $method], $parameters);
        }
        
        // 쿼리 빌더 초기화
        $builder = $this->queryBuilder();
        
        // with() 지원: EagerLoadBuilder 반환 (인스턴스 컨텍스트)
        if ($method === 'with') {
            $relations = isset($parameters[0]) ? (array)$parameters[0] : [];
            return new \App\Core\EagerLoadBuilder($this, $relations);
        }

        // 쿼리 빌더에 메서드가 존재하는지 확인
        if (method_exists($builder, $method)) {
            if ($method === 'find' && !empty($parameters)) {
                $model = $builder->find($parameters[0], $this->primaryKey);
                return $model ? $this->applyCastsToModel($model) : null;
            }
            return call_user_func_array([$builder, $method], $parameters);
        }
        
        throw new BadMethodCallException("Method $method does not exist in " . static::class . " or QueryBuilder");
    }


    /**
     * QueryBuilder 인스턴스 반환
     * @return \App\Classes\QueryBuilder
     * @throws Exception 테이블명 미지정 시
     */
    public function queryBuilder() {
        if (empty($this->table)) {
            throw new Exception("Table name is not defined in " . static::class);
        }
        return app('queryBuilder')->table($this->table);
    }


    /**
     * 정적 컨텍스트에서 QueryBuilder 반환
     * @return \App\Classes\QueryBuilder
     */
    public static function query() {
        return static::getInstance()->queryBuilder();
    }


    /**
     * 기본키로 단건 조회
     * - 캐스트를 적용하여 반환
     * @param mixed $id 찾을 레코드의 ID
     * @return mixed 찾은 레코드 또는 null
     */
    public static function find($id)
    {
        $instance = static::getInstance();
        $model = $instance->query()->find($id, $instance->primaryKey);
        return $model ? $instance->applyCastsToModel($model) : null;
    }


    /**
     * 기본키로 단건 조회(없으면 예외)
     * @param mixed $id 찾을 레코드의 ID
     * @return mixed 찾은 레코드
     * @throws Exception 레코드를 찾지 못한 경우
     */
    public static function findOrFail($id)
    {
        $model = static::find($id);
        if ($model === null) {
            throw new Exception("No query results for model [" . static::class . "] {$id}");
        }
        return $model;
    }


    /**
     * 전체 조회
     * - get() → toArray() 후 각 행에 캐스트 적용
     * @return array
     */
    public static function all()
    {
        $instance = static::getInstance();
        $rows = static::query()->get()->toArray();
        return $instance->applyCastsToRows($rows);
    }


    /**
     * 특정 컬럼으로 단건 조회
     * @param string $field
     * @param mixed $value
     * @return ModelObject|null
     */
    public static function findBy($field, $value)
    {
        return static::query()->where($field, '=', $value)->first();
    }


    /**
     * 테이블명 반환
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }


    /**
     * 레코드 생성
     * - fillable/guarded 필터 및 inbound 캐스트 적용 후 insert
     * - lastInsertId()로 재조회하여 캐스트 적용된 모델 반환
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public static function create(array $data)
    {
        $instance = static::getInstance();

        $filtered = $instance->filterFillable($data);
        $payload  = $instance->applyCastsForSet($filtered);

        $result = $instance->queryBuilder()->insert($payload);

        if ($result) {
            $lastId = $instance->db->lastInsertId();
            $model = static::find($lastId);
            if ($model) {
                return $model;
            }
            throw new Exception('Failed to retrieve created record');
        }

        throw new Exception('Failed to create record');
    }


    /**
     * 조건 업데이트
     * - fillable/guarded 필터 및 inbound 캐스트 적용 후 update
     * @param array $conditions where 조건
     * @param array $data       갱신 데이터
     * @return bool
     */
    public static function update(array $conditions, array $data): bool
    {
        $instance = static::getInstance();

        $filtered = $instance->filterFillable($data);
        if (empty($filtered)) {
            return true;
        }

        $payload = $instance->applyCastsForSet($filtered);

        return $instance
            ->queryBuilder()
            ->update($payload, $conditions);
    }


    /**
     * 조건에 맞는 레코드가 있으면 업데이트, 없으면 생성 (Laravel의 updateOrCreate)
     * 
     * @param array $attributes 검색 조건 (where 절에 사용)
     * @param array $values 업데이트/생성할 값
     * @return mixed 생성/업데이트된 모델 인스턴스
     * @throws Exception
     */
    public static function updateOrCreate(array $attributes, array $values = [])
    {
        $instance = static::getInstance();
        
        // null 값을 제거 (라라벨 동작 방식: idx가 null이면 조건에서 제외)
        $attributes = array_filter($attributes, function($value) {
            return $value !== null && $value !== '';
        });
        
        // attributes가 빈 배열이면 무조건 새로 생성
        if (empty($attributes)) {
            return static::create($values);
        }
        
        // 조건에 맞는 레코드 찾기
        $query = $instance->queryBuilder();
        foreach ($attributes as $key => $value) {
            $query->where($key, $value);
        }
        
        $existing = $query->first();
        
        if ($existing) {
            // 존재하면 업데이트
            $updateData = array_merge($attributes, $values);
            $updated = static::update($attributes, $updateData);
            
            if ($updated) {
                // 업데이트된 레코드 다시 조회
                $query = $instance->queryBuilder();
                foreach ($attributes as $key => $value) {
                    $query->where($key, $value);
                }
                return $query->first();
            }
            throw new Exception('Failed to update record');
        } else {
            // 없으면 생성
            $createData = array_merge($attributes, $values);
            return static::create($createData);
        }
    }


    /* ======================================================
     * fillable / guarded
     * ====================================================== */

    /**
     * 대량할당 필터
     * - fillable이 있으면 화이트리스트 적용
     * - guarded가 * 이면 전부 차단
     * - guarded 일부 지정 시 해당 키 제외
     * @param array $data
     * @return array
     */
    protected function filterFillable(array $data)
    {
        if (!empty($this->fillable)) {
            return array_intersect_key($data, array_flip($this->fillable));
        }

        if ($this->isGuardingAll()) {
            return [];
        }

        if (!empty($this->guarded)) {
            return array_diff_key($data, array_flip($this->guarded));
        }

        return $data;
    }

    /**
     * 전체 가드 상태인지 확인
     * @return bool
     */
    protected function isGuardingAll()
    {
        return count($this->guarded) === 1 && $this->guarded[0] === '*';
    }


    /* ======================================================
     * casts (get/set)
     * ====================================================== */

    /**
     * 여러 행에 outbound 캐스트 적용
     * @param array $rows
     * @return array
     */
    public function applyCastsToRows(array $rows)
    {
        if (empty($this->casts) || empty($rows)) {
            return $rows;
        }
        foreach ($rows as &$row) {
            if (is_array($row)) {
                $row = $this->applyCastsForGet($row);
            }
        }
        return $rows;
    }

    /**
     * 단일 행 배열에 outbound 캐스트 적용
     * @param array $row
     * @return array
     */
    protected function applyCastsForGet(array $row)
    {
        if (empty($this->casts)) {
            return $row;
        }
        foreach ($this->casts as $key => $rule) {
            if (!array_key_exists($key, $row)) continue;
            $row[$key] = $this->castOutbound($rule, $row[$key]);
        }
        return $row;
    }

    /**
     * 모델 객체에 outbound 캐스트 적용
     * - $model->toArray() 결과를 기반으로 속성에 값 할당
     * @param mixed $model
     * @return mixed
     */
    protected function applyCastsToModel($model)
    {
        if (empty($this->casts)) {
            return $model;
        }
        $attrs = $model->toArray();
        foreach ($this->casts as $key => $rule) {
            if (!array_key_exists($key, $attrs)) continue;
            $model->$key = $this->castOutbound($rule, $attrs[$key]);
        }
        return $model;
    }

    /**
     * inbound 캐스트 적용(저장 전 변환)
     * @param array $data
     * @return array
     */
    protected function applyCastsForSet(array $data)
    {
        if (empty($this->casts) || empty($data)) {
            return $data;
        }
        foreach ($data as $key => $val) {
            if (!isset($this->casts[$key])) continue;
            $data[$key] = $this->castInbound($this->casts[$key], $val);
        }
        return $data;
    }

    /**
     * 저장 전 데이터 준비(필터 + inbound 캐스트)
     * @param array $data
     * @return array
     */
    public function prepareForSet(array $data)
    {
        $filtered = $this->filterFillable($data);
        return $this->applyCastsForSet($filtered);
    }

    /**
     * 조회 모델에 outbound 캐스트 적용(외부에서 호출용)
     * @param mixed $model
     * @return mixed
     */
    public function castModelForGet($model)
    {
        return $this->applyCastsToModel($model);
    }

    /* ======================================================
     * 캐스트 변환기
     * ====================================================== */

    /**
     * outbound 캐스트(읽기용 변환)
     * @param string $rule 캐스트 규칙
     * @param mixed $value 원본 값
     * @return mixed 변환된 값
     */
    protected function castOutbound($rule, $value)
    {
        if ($value === null) return null;
        list($type, $arg) = $this->parseCastRule($rule);

        switch ($type) {
            case 'int':
            case 'integer':
                return is_numeric($value) ? (int)$value : 0;
            case 'float':
            case 'double':
            case 'real':
                return is_numeric($value) ? (float)$value : 0.0;
            case 'bool':
            case 'boolean':
                return $this->toBool($value);
            case 'string':
                return (string)$value;
            case 'decimal':
                return round((float)$value, $arg !== null ? $arg : 2);
            case 'array':
            case 'json':
                if (is_array($value)) return $value;
                if ($value === '' || $value === null) return [];
                $decoded = json_decode((string)$value, true);
                return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
            case 'object':
                if (is_object($value)) return $value;
                if ($value === '' || $value === null) return (object)[];
                $decoded = json_decode((string)$value);
                return (json_last_error() === JSON_ERROR_NONE && is_object($decoded)) ? $decoded : (object)[];
            case 'datetime':
                try {
                    return new \DateTimeImmutable((string)$value);
                } catch (\Exception $e) {
                    return null;
                }
            case 'date':
                try {
                    $dt = new \DateTimeImmutable((string)$value);
                    return $dt->setTime(0,0,0);
                } catch (\Exception $e) {
                    return null;
                }
            case 'timestamp':
                return is_numeric($value) ? (int)$value : strtotime((string)$value);
            default:
                return $value;
        }
    }

    /**
     * inbound 캐스트(저장용 변환)
     * @param string $rule 캐스트 규칙
     * @param mixed $value 원본 값
     * @return mixed 변환된 값
     */
    protected function castInbound($rule, $value)
    {
        if ($value === null) return null;
        list($type, $arg) = $this->parseCastRule($rule);

        switch ($type) {
            case 'int':
            case 'integer':
                return is_numeric($value) ? (int)$value : 0;
            case 'float':
            case 'double':
            case 'real':
                return is_numeric($value) ? (float)$value : 0.0;
            case 'bool':
            case 'boolean':
                return $this->toBool($value) ? 1 : 0;
            case 'string':
                return (string)$value;
            case 'decimal':
                $precision = $arg !== null ? $arg : 2;
                return number_format((float)$value, $precision, '.', '');
            case 'array':
            case 'json':
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            case 'object':
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            case 'datetime':
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d H:i:s');
                }
                try {
                    $dt = new \DateTimeImmutable((string)$value);
                    return $dt->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    return null;
                }
            case 'date':
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                try {
                    $dt = new \DateTimeImmutable((string)$value);
                    return $dt->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            case 'timestamp':
                if (is_numeric($value)) return (int)$value;
                return strtotime((string)$value);
            default:
                return $value;
        }
    }

    /**
     * 캐스트 규칙 파싱
     * - 'decimal:2' → ['decimal', 2]
     * @param string $rule
     * @return array{0:string,1:int|null}
     */
    protected function parseCastRule($rule)
    {
        $type = $rule;
        $arg  = null;
        if (strpos($rule, ':') !== false) {
            list($type, $argStr) = explode(':', $rule, 2);
            $type = trim($type);
            $arg  = is_numeric($argStr) ? (int)$argStr : null;
        }
        return [strtolower($type), $arg];
    }

    /**
     * 불리언 변환
     * - 다양한 입력값을 엄격한 bool로 변환
     * @param mixed $v
     * @return bool
     */
    protected function toBool($v)
    {
        if (is_bool($v)) return $v;
        if (is_numeric($v)) return ((int)$v) !== 0;
        $val = strtolower(trim((string)$v));
        return in_array($val, ['1','true','on','yes','y','t'], true);
    }

}


/* ======================================================
 * 릴레이션 클래스
 * - hasMany, hasOne, belongsTo 관계를 위한 경량 래퍼 클래스 
 * - 라라벨 Eloquent 스타일로 체이닝으로 QueryBuilder 메서드를 위임하며, 소유자 키를 지정(for)할 수 있다.
 * ====================================================== */

/**
 * HasManyRelation
 * - hasMany 관계를 위한 경량 래퍼 클래스
 */
class HasManyRelation
{
    protected $relatedInstance; // BaseModel 파생 클래스 인스턴스
    protected $foreignKey; // 자식 FK
    protected $localKey; // 부모 PK
    protected $ownerKeyValue = null; //mixed 소유자 키 값
    protected $selectColumns = null; // 선택 컬럼

    /**
     * @param string $relatedClass 관련 모델 클래스명
     * @param string $foreignKey 자식 FK
     * @param string $localKey 부모 PK
     */
    public function __construct(string $relatedClass, string $foreignKey, string $localKey = 'idx')
    {
        // 관련 모델 인스턴스 생성
        $this->relatedInstance = new $relatedClass();
        if (!($this->relatedInstance instanceof BaseModel)) {
            throw new \InvalidArgumentException('Related class must extend BaseModel');
        }
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    /**
     * 소유자 키 값 지정 (예: hotel_idx = 10)
     * @param mixed $ownerKeyValue
     * @return $this
     */
    public function for($ownerKeyValue): self
    {
        $this->ownerKeyValue = $ownerKeyValue;
        return $this;
    }

    /**
     * 내부 QueryBuilder 생성
     * - for()로 지정된 값이 있으면 where FK = 값 조건 추가
     * @return \App\Classes\QueryBuilder
     */
    protected function builder(): \App\Classes\QueryBuilder
    {
        $qb = $this->relatedInstance->queryBuilder();
        if ($this->selectColumns) {
            $cols = is_array($this->selectColumns) ? $this->selectColumns : [$this->selectColumns];
            // 매핑을 위해 FK는 반드시 포함
            if (!in_array($this->foreignKey, $cols, true)) {
                $cols[] = $this->foreignKey;
            }
            $qb->select($cols);
        }
        if ($this->ownerKeyValue !== null) {
            $qb->where($this->foreignKey, '=', $this->ownerKeyValue);
        }
        return $qb;
    }

    /**
     * 동적 위임
     * - get, where, orderBy 등 QueryBuilder 메서드를 그대로 체이닝
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $qb = $this->builder();
        if (!method_exists($qb, $method)) {
            throw new \BadMethodCallException("Method {$method} does not exist on relation builder");
        }
        return call_user_func_array([$qb, $method], $parameters);
    }

    /**
     * 관계에서 선택할 컬럼 지정
     * @param string|array $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->selectColumns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
}


/**
 * HasOneRelation
 * - hasOne 관계를 위한 경량 래퍼
 * - first()로 단건을 반환
 */
class HasOneRelation
{
    protected $relatedInstance;
    protected $foreignKey;
    protected $localKey;
    protected $ownerKeyValue = null;
    protected $selectColumns = null; // 선택 컬럼

    /**
     * @param string $relatedClass
     * @param string $foreignKey
     * @param string $localKey
     */
    public function __construct(string $relatedClass, string $foreignKey, string $localKey = 'idx')
    {
        $this->relatedInstance = new $relatedClass();
        if (!($this->relatedInstance instanceof BaseModel)) {
            throw new \InvalidArgumentException('Related class must extend BaseModel');
        }
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    /**
     * 소유자 키 값 지정
     * @param mixed $ownerKeyValue
     * @return $this
     */
    public function for($ownerKeyValue): self
    {
        $this->ownerKeyValue = $ownerKeyValue;
        return $this;
    }

    /**
     * 내부 QueryBuilder 생성(단건 제한 포함)
     * @return \App\Classes\QueryBuilder
     */
    protected function builder(): \App\Classes\QueryBuilder
    {
        $qb = $this->relatedInstance->queryBuilder();
        if ($this->selectColumns) {
            $cols = is_array($this->selectColumns) ? $this->selectColumns : [$this->selectColumns];
            // 매핑을 위해 FK는 반드시 포함
            if (!in_array($this->foreignKey, $cols, true)) {
                $cols[] = $this->foreignKey;
            }
            $qb->select($cols);
        }
        if ($this->ownerKeyValue !== null) {
            $qb->where($this->foreignKey, '=', $this->ownerKeyValue)->limit(1);
        }
        return $qb;
    }

    /**
     * 단건 조회 (배열로 반환 - relation 용도)
     * @return array|null
     */
    public function first()
    {
        $qb = $this->builder();
        $result = $qb->get();
        $data = $result->toArray();
        $row = $data[0] ?? null;

        // 캐스트 적용
        if ($row) {
            $model = \App\Core\BaseModel::forTable($qb->getTable());
            if ($model) {
                $row = $model->applyCastsForGet($row);
            }
        }

        return $row;
    }

    /**
     * 동적 위임
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $qb = $this->builder();
        if (!method_exists($qb, $method)) {
            throw new \BadMethodCallException("Method {$method} does not exist on relation builder");
        }
        return call_user_func_array([$qb, $method], $parameters);
    }

    /**
     * 관계에서 선택할 컬럼 지정
     * @param string|array $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->selectColumns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
}

/**
 * BelongsToRelation
 * - 현재 모델이 FK를 들고 있고, 부모의 PK를 참조하는 관계
 * - first()로 부모 단건을 반환
 */
class BelongsToRelation
{
    protected $parentInstance;
    protected $ownerKey; // 부모의 PK (기본 idx)
    protected $foreignKey; // 현재 모델이 들고 있는 FK
    protected $foreignKeyValue = null;
    protected $selectColumns = null; // 선택 컬럼

    /**
     * @param string $parentClass 부모 모델 클래스명
     * @param string $foreignKey 현재 모델 FK
     * @param string $ownerKey 부모 PK
     */
    public function __construct(string $parentClass, string $foreignKey, string $ownerKey = 'idx')
    {
        $this->parentInstance = new $parentClass();
        if (!($this->parentInstance instanceof BaseModel)) {
            throw new \InvalidArgumentException('Parent class must extend BaseModel');
        }
        $this->ownerKey = $ownerKey;
        $this->foreignKey = $foreignKey;
    }

    /**
     * FK 값 지정(부모 조회 기준)
     * @param mixed $foreignKeyValue
     * @return $this
     */
    public function for($foreignKeyValue): self
    {
        $this->foreignKeyValue = $foreignKeyValue;
        return $this;
    }

    /**
     * 내부 QueryBuilder 생성(단건 제한 포함)
     * @return \App\Classes\QueryBuilder
     */
    protected function builder(): \App\Classes\QueryBuilder
    {
        $qb = $this->parentInstance->queryBuilder();
        if ($this->selectColumns) {
            $cols = is_array($this->selectColumns) ? $this->selectColumns : [$this->selectColumns];
            // 매핑을 위해 부모의 ownerKey는 반드시 포함
            if (!in_array($this->ownerKey, $cols, true)) {
                $cols[] = $this->ownerKey;
            }
            $qb->select($cols);
        }
        if ($this->foreignKeyValue !== null) {
            $qb->where($this->ownerKey, '=', $this->foreignKeyValue)->limit(1);
        }
        return $qb;
    }

    /**
     * 부모 단건 조회 (배열로 반환 - relation 용도)
     * @return array|null
     */
    public function first()
    {
        $qb = $this->builder();
        $result = $qb->get();
        $data = $result->toArray();
        $row = $data[0] ?? null;

        // 캐스트 적용
        if ($row) {
            $model = \App\Core\BaseModel::forTable($qb->getTable());
            if ($model) {
                $row = $model->applyCastsForGet($row);
            }
        }

        return $row;
    }

    /**
     * 관계에서 선택할 컬럼 지정
     * @param string|array $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->selectColumns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * 동적 위임
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $qb = $this->builder();
        if (!method_exists($qb, $method)) {
            throw new \BadMethodCallException("Method {$method} does not exist on relation builder");
        }
        return call_user_func_array([$qb, $method], $parameters);
    }
}


/**
 * HasRelationships
 * - BaseModel 내부에서 관계 인스턴스를 생성하는 헬퍼 모음
 * - hasMany / hasOne / belongsTo 관계를 위한 헬퍼 메서드
 */
trait HasRelationships
{
    /**
     * hasMany 관계 인스턴스 생성
     * @param string $relatedClass
     * @param string $foreignKey
     * @param string $localKey
     * @return \App\Core\HasManyRelation
     */
    public function hasMany(string $relatedClass, string $foreignKey, string $localKey = 'idx')
    {
        return new \App\Core\HasManyRelation($relatedClass, $foreignKey, $localKey);
    }

    /**
     * hasMany 관계 인스턴스 생성
     * @param string $relatedClass
     * @param string $foreignKey
     * @param string $localKey
     * @return \App\Core\HasManyRelation
     */
    public function hasOne(string $relatedClass, string $foreignKey, string $localKey = 'idx')
    {
        return new \App\Core\HasOneRelation($relatedClass, $foreignKey, $localKey);
    }

   /**
     * belongsTo 관계 인스턴스 생성
     * @param string $parentClass
     * @param string $foreignKey
     * @param string $ownerKey
     * @return \App\Core\BelongsToRelation
     */
    public function belongsTo(string $parentClass, string $foreignKey, string $ownerKey = 'idx')
    {
        return new \App\Core\BelongsToRelation($parentClass, $foreignKey, $ownerKey);
    }
}


/**
 * EagerLoadBuilder
 * - with() 호출 시 반환되는 래퍼
 * - 원본 QueryBuilder에 체이닝을 전달하면서, get/first/paginate 시 지정된 릴레이션을 한 번에 로드한다.
 */
class EagerLoadBuilder
{
    protected $modelInstance; // BaseModel 파생 인스턴스
    protected $qb; // QueryBuilder (원본 또는 기존 빌더)
    protected $relations = [];

    // 모델 인스턴스 혹은 쿼리빌더를 받아 처리
    public function __construct($origin, array $relations = [])
    {
        if ($origin instanceof \App\Core\BaseModel) {
            $this->modelInstance = $origin;
            $this->qb = $origin->queryBuilder();
        } else if ($origin instanceof \App\Classes\QueryBuilder) {
            $this->qb = $origin;
            $table = $origin->getTable();
            $this->modelInstance = \App\Core\BaseModel::forTable($table);
        } else {
            throw new \InvalidArgumentException('EagerLoadBuilder expects BaseModel or QueryBuilder');
        }
        $this->relations = $relations;
    }

    /**
     * 관계 추가 (중복 병합)
     * @param string|array $relations
     * @return $this
     */
    public function with($relations)
    {
        $new = is_array($relations) ? $relations : [$relations];
        // 문자열 csv 지원 (예: 'rel1,rel2')
        if (count($new) === 1 && is_string($new[0]) && strpos($new[0], ',') !== false) {
            $new = array_map('trim', explode(',', $new[0]));
        }
        // 기존 관계 배열에 새 관계 병합 (중복 제거)
        $this->relations = array_values(array_unique(array_merge($this->relations, $new)));
        return $this;
    }

    /**
     * 동적 위임
     * - orderBy/where 등 QueryBuilder 메서드를 체이닝
     * @param string $method
     * @param array $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (!method_exists($this->qb, $method)) {
            throw new \BadMethodCallException("Method {$method} does not exist on QueryBuilder");
        }
        call_user_func_array([$this->qb, $method], $parameters);
        return $this; // 체이닝 유지
    }

    /**
     * 결과 전체 조회 + 지정 릴레이션 이저 로딩 + 캐스트 적용
     * @return \App\Classes\QueryBuilder
     */
    public function get()
    {
        $result = $this->qb->get(); // QueryBuilder 객체
        $rows = $result->toArray();
        
        // 캐스트 적용
        if (!empty($rows) && $this->modelInstance) {
            $rows = $this->modelInstance->applyCastsToRows($rows);
        }
        
        // eager loading 적용
        if (!empty($this->relations) && !empty($rows)) {
            $rows = $this->eagerLoad($rows);
        }
        
        // 수정된 데이터를 QueryBuilder에 다시 저장하고 반환
        $result->setData($rows);
        return $result;
    }

    /**
     * 첫 행 조회 + 지정 릴레이션 이저 로딩
     * @return array|null
     */
    public function first()
    {
        $this->qb->limit(1);
        $result = $this->get(); // QueryBuilder 객체
        $rows = $result->toArray();
        return $rows[0] ?? null;
    }

    /**
     * 페이지네이션 + 캐스트 적용 + 지정 릴레이션 이저 로딩
     * @param int $perPage
     * @param int $page
     * @return array{data:array, ...}
     */
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $result = $this->qb->paginate($perPage, $page);
        
        // 캐스트 적용
        if (!empty($result['data']) && $this->modelInstance) {
            $result['data'] = $this->modelInstance->applyCastsToRows($result['data']);
        }
        
        // eager loading 적용
        if (!empty($this->relations) && !empty($result['data'])) {
            $result['data'] = $this->eagerLoad($result['data']);
        }
        
        return $result;
    }

    /**
     * 실제 이저 로딩 구현
     * - hasMany/hasOne: 부모키 목록으로 관련 행을 한 번에 조회 후 그룹핑하여 병합
     * - belongsTo: FK 목록으로 부모를 한 번에 조회해 병합
     * @param array $rows
     * @return array
     */
    protected function eagerLoad(array $rows): array
    {

        if (!$this->modelInstance) {
            return $rows; // 테이블→모델 매핑이 없으면 그냥 통과
        }

        foreach ($this->relations as $relationName) {
            if (!method_exists($this->modelInstance, $relationName)) continue;

            $relationObj = call_user_func([$this->modelInstance, $relationName]);

            // hasMany / hasOne
            if ($relationObj instanceof \App\Core\HasManyRelation || $relationObj instanceof \App\Core\HasOneRelation) {
                $meta = $this->extractRelationMeta($relationObj);
                $localKey = $meta['localKey'];
                $foreignKey = $meta['foreignKey'];
                $relatedInstance = $meta['relatedInstance'];

                $ownerIds = [];
                foreach ($rows as $r) {
                    if (array_key_exists($localKey, $r)) {
                        $ownerIds[] = $r[$localKey];
                    }
                }
                $ownerIds = array_values(array_unique($ownerIds));
                if (empty($ownerIds)) {
                    // 빈 배열 세팅
                    $rows = $this->attachEmpty($rows, $relationName, ($relationObj instanceof \App\Core\HasManyRelation));
                    continue;
                }

                $relatedQb = $relatedInstance->queryBuilder();
                // 선택 컬럼 지원: relation 객체에 selectColumns 존재 시 반영
                if (method_exists($relationObj, 'select') && property_exists($relationObj, 'selectColumns')) {
                    $ref2 = new \ReflectionObject($relationObj);
                    $selectCols2 = $this->getPrivate($ref2, $relationObj, 'selectColumns');
                    if (!empty($selectCols2)) {
                        // 매핑을 위해 FK 포함 보장
                        if (!in_array($foreignKey, $selectCols2, true)) {
                            $selectCols2[] = $foreignKey;
                        }
                        $relatedQb->select($selectCols2);
                    }
                }
                $relatedRows = $relatedQb
                    ->whereIn($foreignKey, $ownerIds)
                    ->get()
                    ->toArray();

                // 그룹핑 (FK 기준)
                $grouped = [];
                foreach ($relatedRows as $rr) {
                    $key = (string)($rr[$foreignKey] ?? '');
                    if (!isset($grouped[$key])) $grouped[$key] = [];
                    $grouped[$key][] = $rr;
                }

                // 부착
                foreach ($rows as &$r) {
                    $key = (string)($r[$localKey] ?? '');
                    if ($relationObj instanceof \App\Core\HasManyRelation) {
                        $r[$relationName] = $grouped[$key] ?? [];
                    } else { // hasOne
                        $bucket = $grouped[$key] ?? [];
                        $r[$relationName] = $bucket[0] ?? null;
                    }
                }
                unset($r);
            }

            // belongsTo
            else if ($relationObj instanceof \App\Core\BelongsToRelation) {
                $meta = $this->extractBelongsToMeta($relationObj);
                $ownerKey = $meta['ownerKey'];
                $foreignKey = $meta['foreignKey'];
                $parentInstance = $meta['parentInstance'];

                $foreignIds = [];
                foreach ($rows as $r) {
                    if (array_key_exists($foreignKey, $r)) {
                        $foreignIds[] = $r[$foreignKey];
                    }
                }
                $foreignIds = array_values(array_unique($foreignIds));
                if (empty($foreignIds)) {
                    foreach ($rows as &$r) { $r[$relationName] = null; } unset($r);
                    continue;
                }

                $parentQb = $parentInstance->queryBuilder();
                // 선택 컬럼 지원: relation 객체에 selectColumns 존재 시 반영
                if (method_exists($relationObj, 'select') && property_exists($relationObj, 'selectColumns')) {
                    $ref = new \ReflectionObject($relationObj);
                    $selectCols = $this->getPrivate($ref, $relationObj, 'selectColumns');
                    if (!empty($selectCols)) {
                        // 매핑을 위해 부모의 ownerKey 포함 보장
                        if (!in_array($ownerKey, $selectCols, true)) {
                            $selectCols[] = $ownerKey;
                        }
                        $parentQb->select($selectCols);
                    }
                }
                $parents = $parentQb
                    ->whereIn($ownerKey, $foreignIds)
                    ->get()
                    ->toArray();

                $indexed = [];
                foreach ($parents as $p) {
                    $indexed[(string)($p[$ownerKey] ?? '')] = $p;
                }

                foreach ($rows as &$r) {
                    $key = (string)($r[$foreignKey] ?? '');
                    $r[$relationName] = $indexed[$key] ?? null;
                }
                unset($r);
            }
        }
        return $rows;
    }

    /**
     * 빈 관계 기본값을 부착
     * - hasMany: []
     * - hasOne/belongsTo: null
     * @param array $rows
     * @param string $relationName
     * @param bool $isMany
     * @return array
     */
    protected function attachEmpty(array $rows, string $relationName, bool $isMany): array
    {
        foreach ($rows as &$r) {
            $r[$relationName] = $isMany ? [] : null;
        }
        unset($r);
        return $rows;
    }

    /**
     * hasMany/hasOne 메타데이터 추출(리플렉션)
     * @param object $relationObj
     * @return array{relatedInstance:\App\Core\BaseModel,foreignKey:string,localKey:string}
     */
    protected function extractRelationMeta($relationObj): array
    {
        // 접근 가능한 프로퍼티가 없으므로 리플렉션으로 내부 값을 조회
        $ref = new \ReflectionObject($relationObj);
        $relatedInstance = $this->getPrivate($ref, $relationObj, 'relatedInstance');
        $foreignKey = $this->getPrivate($ref, $relationObj, 'foreignKey');
        $localKey = $this->getPrivate($ref, $relationObj, 'localKey');
        return [
            'relatedInstance' => $relatedInstance,
            'foreignKey' => $foreignKey,
            'localKey' => $localKey,
        ];
    }

    /**
     * belongsTo 메타데이터 추출(리플렉션)
     * @param object $relationObj
     * @return array{parentInstance:\App\Core\BaseModel,ownerKey:string,foreignKey:string}
     */
    protected function extractBelongsToMeta($relationObj): array
    {
        $ref = new \ReflectionObject($relationObj);
        $parentInstance = $this->getPrivate($ref, $relationObj, 'parentInstance');
        $ownerKey = $this->getPrivate($ref, $relationObj, 'ownerKey');
        $foreignKey = $this->getPrivate($ref, $relationObj, 'foreignKey');
        return [
            'parentInstance' => $parentInstance,
            'ownerKey' => $ownerKey,
            'foreignKey' => $foreignKey,
        ];
    }

    /**
     * 리플렉션으로 비공개 프로퍼티 값 읽기
     * @param \ReflectionObject $ref
     * @param object $obj
     * @param string $name
     * @return mixed
     */
    protected function getPrivate(\ReflectionObject $ref, $obj, string $name)
    {
        $prop = $ref->getProperty($name);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }
}
