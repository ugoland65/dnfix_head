<?php

namespace App\Classes;

use PDO;

class QueryBuilder
{
    private $db;
    private $table;
    private $columns = '*';
    private $wheres = [];
    private $joins = [];
	private $groupBy = [];
    private $orderBy = [];
    private $limit = '';
	private $bindings = [];
    private $data = [];
    private $withCounts = [];

    /** 
	 * 생성자: PDO 주입 
	 * @param PDO $db
	 */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * 쿼리 상태 초기화
     */
    public function clearCache() {
        $this->table = '';    // 테이블 초기화
        $this->columns = '*'; // 기본 select 컬럼 값
        $this->wheres = [];
        $this->joins = [];
		$this->groupBy = [];
        $this->orderBy = [];
        $this->limit = '';
		$this->bindings = [];
        $this->data = [];
        $this->withCounts = [];
        return $this;
    }

	/**
	 * 테이블 설정
	 * @param string $table
	 * @return $this
	 */
    public function table($table)
    {
		$this->clearCache(); // 자동으로 캐시 제거
        $this->table = $table;
        return $this;
    }

	/**
	 * 현재 테이블명 반환 (eager loader에서 모델 매핑용)
	 * @return string
	 */
    public function getTable()
    {
        return $this->table;
    }

	/**
	 * PDO의 exec 메서드 호출
	 * 원시 SQL 실행 (DDL/DML)
	 * @param string $query
	 * @return int
	 */
    public function exec($query) {
        return $this->db->exec($query); 
    }

	/**
	 * Raw SQL 표현식 생성
	 * @param string $expression
	 * @return RawExpression
	 */
    public function raw($expression) {
        return new RawExpression($expression);
    }

	/* ==========================================
     * Eager Loading / 부가 기능
     * ========================================== */
    /**
     * with: 간단 eager loading 래퍼 반환 (ex: Model::query()->with('roomTypes')->where(...)->paginate())
	 * @param string|array $relations
	 * @return EagerLoadBuilder
     */
    public function with($relations)
    {
        $rels = is_array($relations) ? $relations : [$relations];
        return new \App\Core\EagerLoadBuilder($this, $rels);
    }

    /**
     * withCount: 간단 릴레이션 카운트 지원 (예: withCount(['roomTypes as room_count']))
     * 현재 테이블에 대응되는 BaseModel 인스턴스의 릴레이션 메서드를 찾아 메모이제이션 후
     * get() 시점에 일괄 카운팅하여 결과 배열에 alias로 주입
	 * @param string|array $relations
	 * @return $this
     */
    public function withCount($relations)
    {
        $items = is_array($relations) ? $relations : [$relations];
        foreach ($items as $rel) {
            $alias = null;
            $name = $rel;
            if (stripos($rel, ' as ') !== false) {
                list($name, $alias) = preg_split('/\s+as\s+/i', $rel);
                $name = trim($name);
                $alias = trim($alias);
            }
            if (!$alias) {
                $alias = $name . '_count';
            }
            $this->withCounts[] = ['name' => $name, 'alias' => $alias];
        }
        return $this;
    }

	/* ==========================================
     * SELECT 빌더
     * ========================================== */
	/**
	 * SELECT 컬럼 설정
	 * @param string|array $columns
	 * @return $this
	 */
	public function select($columns = '*')
	{
		/*
		func_num_args()를 사용하여 전달된 인자의 개수를 확인
		만약 2개 이상의 인자가 전달되었다면, func_get_args()를 사용하여 배열로 변환합니다.
		*/
		if (func_num_args() > 1) {
			$columns = func_get_args();
		}
	
		// 배열인 경우 각 컬럼명을 백틱으로 보호
		if (is_array($columns)) {
			$escapedColumns = array_map(function($col) {
				return $this->escapeColumnName(trim($col));
			}, $columns);
			$this->columns = implode(', ', $escapedColumns);
		} else {
			// * 이면 그대로 반환
			if ($columns === '*') {
				$this->columns = '*';
				return $this;
			}
			
			// 쉼표로 구분된 문자열인 경우 배열로 변환 (예: "code, name")
			if (strpos($columns, ',') !== false) {
				$columnArray = array_map('trim', explode(',', $columns));
				$escapedColumns = array_map(function($col) {
					return $this->escapeColumnName($col);
				}, $columnArray);
				$this->columns = implode(', ', $escapedColumns);
			} else {
				// 단일 컬럼명이면 백틱 처리
				$this->columns = $this->escapeColumnName($columns);
			}
		}
		return $this;
	}

	/**
	 * SELECT Raw SQL 표현식 추가
	 * @param string $expression
	 * @return $this
	 */
	public function selectRaw($expression)
	{
		// 기본값 '*'일 때는 단순히 새로운 선택 열로 대체
		if ($this->columns === '*') {
			$this->columns = $expression;
		} else {
			// 기존 선택 열에 추가
			$this->columns .= ", $expression";
		}

		return $this;
	}

	/**
	 * JOIN 구문 추가
	 * @param string $table 조인할 테이블
	 * @param string $column1 첫 번째 컬럼
	 * @param string $operator 연산자
	 * @param string $column2 두 번째 컬럼
	 * @param string $type 조인 타입
	 * @param string|null $additionalConditions 추가 조건
	 * @return $this
	 */
	public function join($table, $column1, $operator, $column2, $type = 'INNER', $additionalConditions = null)
	{
		/* join 메서드는 기본 ON 조건만 처리하므로, 추가적인 조건(예: AND) 가능하도록 수정 */
		/* 25.01.02 Lion */
		
		// 컬럼명에 점(.)이 포함되어 있으면 테이블명.컬럼명 형태이므로 각각 백틱 처리
		$col1 = $this->escapeColumnName($column1);
		$col2 = $this->escapeColumnName($column2);
		
		// 테이블명에 별칭이 있는 경우 처리 (예: "v2_booking_group AS C")
		$escapedTable = $this->escapeTableName($table);
		
		$joinClause = strtoupper($type) . " JOIN $escapedTable ON $col1 $operator $col2";

		if ($additionalConditions) {
			$joinClause .= ' AND ' . $additionalConditions;
		}

		$this->joins[] = $joinClause;
		return $this;
	}

	/**
	 * LEFT JOIN 구문 추가
	 * @param string $table 조인할 테이블
	 * @param string $column1 첫 번째 컬럼
	 * @param string $operator 연산자
	 * @param string $column2 두 번째 컬럼
	 * @param string|null $additionalConditions 추가 조건
	 * @return $this
	 */
	public function leftJoin($table, $column1, $operator, $column2, $additionalConditions = null)
	{
		return $this->join($table, $column1, $operator, $column2, 'LEFT', $additionalConditions);
	}

	/**
	 * RIGHT JOIN 구문 추가
	 * @param string $table 조인할 테이블
	 * @param string $column1 첫 번째 컬럼
	 * @param string $operator 연산자
	 * @param string $column2 두 번째 컬럼
	 * @param string|null $additionalConditions 추가 조건
	 * @return $this
	 */
	public function rightJoin($table, $column1, $operator, $column2, $additionalConditions = null)
	{
		return $this->join($table, $column1, $operator, $column2, 'RIGHT', $additionalConditions);
	}

	/**
	 * INNER JOIN 구문 추가
	 * @param string $table 조인할 테이블
	 * @param string $column1 첫 번째 컬럼
	 * @param string $operator 연산자
	 * @param string $column2 두 번째 컬럼
	 * @param string|null $additionalConditions 추가 조건
	 * @return $this
	 */
	public function innerJoin($table, $column1, $operator, $column2, $additionalConditions = null)
	{
		return $this->join($table, $column1, $operator, $column2, 'INNER', $additionalConditions);
	}

	/**
	 * JOIN Raw SQL 표현식 추가
	 * @param string $sql
	 * @param array $bindings
	 * @return $this
	 */
	public function joinRaw($sql, array $bindings = [])
	{
		// SQL이 배열인 경우 문자열로 변환
		if (is_array($sql)) {
			$sql = implode(' ', $sql);
		}

		$this->joins[] = [
			'type' => 'raw',
			'sql' => $sql,
			'bindings' => $bindings
		];
		
		// 바인딩 값들을 전체 바인딩 배열에 추가
		if (!empty($bindings)) {
			$this->bindings = array_merge($this->bindings, $bindings);
		}
		
		return $this;
	}

	/**
	 * JOIN 서브쿼리 추가
	 * @param QueryBuilder $queryBuilder 서브쿼리 빌더
	 * @param string $alias 서브쿼리 별칭
	 * @param callable $callback 서브쿼리 조건 콜백
	 * @param string $type 조인 타입
	 * @return $this
	 */
	public function joinSub($queryBuilder, $alias, callable $callback, $type = 'INNER')
	{
		if ($queryBuilder instanceof self) {
			$subQuery = "({$queryBuilder->toSql()})";
			$this->bindings = array_merge($this->bindings, $queryBuilder->getBindings());
		} else {
			throw new \InvalidArgumentException("joinSub expects a QueryBuilder instance as the first argument.");
		}

		$joinCondition = new self($this->db); // 새로운 인스턴스 생성
		$callback($joinCondition);

		$onConditions = implode(' AND ', array_map(function ($where) {
			if ($where['type'] === 'basic') {
				return "{$where['column']} {$where['operator']} {$where['value']}";
			}
			return '';
		}, $joinCondition->wheres));

		$this->joins[] = strtoupper($type) . " JOIN {$subQuery} AS {$alias} ON {$onConditions}";

		return $this;
	}


	/**
	 * JOIN 조건 추가
	 * @param string $column1 첫 번째 컬럼
	 * @param string $operator 연산자
	 * @param string $column2 두 번째 컬럼
	 * @return $this
	 */
	public function on($column1, $operator, $column2)
	{
		$this->wheres[] = [
			'type' => 'basic',
			'column' => $column1,
			'operator' => $operator,
			'value' => $column2, // 열 이름으로 간주
		];
		return $this;
	}

	/**
	 * JOIN 조건 빌드
	 * @return string
	 */
	private function buildJoinClause()
	{
		$sql = '';
		
		foreach ($this->joins as $join) {
			if ($join['type'] === 'raw') {
				// join sql이 문자열인지 확인하고 처리
				$joinSql = is_array($join['sql']) ? implode(' ', $join['sql']) : $join['sql'];
				$sql .= ' ' . $joinSql;
				continue;
			}
			
			// 기존 join 처리 코드
			$sql .= " {$join['type']} JOIN {$join['table']}";
			$sql .= " ON {$join['first']} {$join['operator']} {$join['second']}";
		}
		
		return $sql;
	}

	/* ==========================================
     * WHERE 계열
     * ========================================== */
	/**
	 * where 메서드
	 * @param string $column 컬럼
	 * @param string $operator 연산자
	 * @param string $value 값
	 * @return $this
	 */
    public function where($column, $operator = null, $value = null)
    {
        // 콜백 함수를 첫 번째 인자로 받는 경우 처리
        if (is_callable($column) && func_num_args() === 1) {
            // 새로운 QueryBuilder 인스턴스 생성하여 하위 조건 담기
            $nestedQuery = new self($this->db);
            
            // 콜백 함수 실행하여 조건 구성
            $column($nestedQuery);
            
            // 중첩 조건 타입으로 추가
            $this->wheres[] = [
                'type' => 'nested',
                'query' => $nestedQuery,
                'boolean' => 'AND'
            ];
            
            return $this;
        }

        if (func_num_args() === 2) {
            // 인자가 2개일 경우, 기본 연산자는 '='
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

	/**
	 * OR WHERE 조건 추가
	 * @param string $column 컬럼
	 * @param string $operator 연산자
	 * @param string $value 값
	 * @return $this
	 */
    public function orWhere($column, $operator = null, $value = null)
    {
        // 콜백 함수를 처리하는 경우
        if (is_callable($column) && func_num_args() === 1) {
            // 새로운 QueryBuilder 인스턴스 생성
            $nestedQuery = new self($this->db);
            
            // 콜백 함수 실행
            $column($nestedQuery);
            
            // 중첩 조건 추가 (OR 조건으로)
            $this->wheres[] = [
                'type' => 'nested',
                'query' => $nestedQuery,
                'boolean' => 'OR'
            ];
            
            return $this;
        }

        // 기본 처리 (인자가 2개일 경우)
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'or',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

	/**
	 * whereRaw 메서드가 positional 매개변수를 named 방식으로 변환하도록 수정
	 * @param string $rawSql
	 * @param array $bindings
	 * @return $this
	 */	
	public function whereRaw($rawSql, array $bindings = [])
	{
		// SQL에 물음표(?) 플레이스홀더가 있는지 확인
		if (strpos($rawSql, '?') !== false) {
			// 물음표 방식이면 그대로 사용
			$this->wheres[] = [
				'type' => 'raw',
				'sql' => $rawSql,
				'bindings' => $bindings,
				'is_positional' => true
			];
		} else {
			// 이름 방식이면 처리
			$localBindings = [];
			$paramIndex = count($this->bindings); // 현재 바인딩 개수를 기준으로 고유 이름 생성

			foreach ($bindings as $key => $value) {
				// 고유한 바인딩 이름 생성
				$paramName = ":param2_" . $paramIndex++;
				
				// 기존 :key가 포함된 SQL을 새로운 $paramName으로 치환
				$rawSql = str_replace(':' . $key, $paramName, $rawSql);
				
				// 바인딩 추가
				$this->bindings[$paramName] = $value; // 전역 바인딩에 추가
				$localBindings[$paramName] = $value; // 로컬 바인딩에 추가
			}

			// raw 조건 추가
			$this->wheres[] = [
				'type' => 'raw',
				'sql' => $rawSql,
				'bindings' => $localBindings,
				'is_positional' => false
			];
		}

		return $this;
	}

	/**
	 * OR WHERE Raw SQL 표현식 추가
	 * @param string $rawSql
	 * @param array $bindings
	 * @return $this
	 */
    public function orWhereRaw($rawSql, array $bindings = [])
    {
        // SQL에 물음표(?) 플레이스홀더가 있는지 확인
        if (strpos($rawSql, '?') !== false) {
            // 물음표 방식이면 그대로 사용
            $this->wheres[] = [
                'type' => 'raw_or',
                'sql' => $rawSql,
                'bindings' => $bindings,
                'is_positional' => true
            ];
        } else {
            // 이름 방식이면 처리
            $localBindings = [];
            $paramIndex = count($this->bindings); // 현재 바인딩 개수를 기준으로 고유 이름 생성

            foreach ($bindings as $key => $value) {
                // 고유한 바인딩 이름 생성
                $paramName = ":param2_" . $paramIndex++;
                
                // 기존 :key가 포함된 SQL을 새로운 $paramName으로 치환
                $rawSql = str_replace(':' . $key, $paramName, $rawSql);
                
                // 바인딩 추가
                $this->bindings[$paramName] = $value; // 전역 바인딩에 추가
                $localBindings[$paramName] = $value; // 로컬 바인딩에 추가
            }

            // raw 조건 추가 (OR 타입으로)
            $this->wheres[] = [
                'type' => 'raw_or',
                'sql' => $rawSql,
                'bindings' => $localBindings,
                'is_positional' => false
            ];
        }

        return $this;
    }

	/**
	 * WHERE IN 조건 추가
	 * @param string $column 컬럼
	 * @param array $values 값
	 * @return $this
	 */
	public function whereIn($column, array $values)
	{
		$this->wheres[] = [
			'type' => 'in',
			'column' => $column,
			'values' => $values,
			'not' => false // 기본적으로 NOT IN이 아님
		];
		return $this;
	}

	/**
	 * WHERE NOT IN 조건 추가
	 * @param string $column 컬럼
	 * @param array $values 값
	 * @return $this
	 */
	public function whereNotIn($column, array $values)
	{
		$this->wheres[] = [
			'type' => 'in',
			'column' => $column,
			'values' => $values,
			'not' => true // NOT IN으로 설정
		];
		return $this;
	}

	/**
	 * WHERE BETWEEN 조건 추가
	 * @param string $column 컬럼
	 * @param array $values 값
	 * @return $this
	 */
    public function whereBetween($column, array $values)
    {
        if (count($values) !== 2) {
			throw new \InvalidArgumentException("The whereBetween method requires an array with exactly 2 values.");
        }

        $this->wheres[] = [
            'type' => 'between',
            'column' => $column,
            'values' => $values
        ];

        return $this;
    }

	/**
	 * WHERE NOT BETWEEN 조건 추가
	 * @param string $column 컬럼
	 * @param array $values 값
	 * @return $this
	 */
	public function whereNotBetween($column, array $values)
	{
		if (count($values) !== 2) {
			throw new \InvalidArgumentException("The whereNotBetween method requires an array with exactly 2 values.");
		}

		$this->wheres[] = [
			'type' => 'notBetween',
			'column' => $column,
			'values' => $values
		];

		return $this;
	}

	/**
	 * WHERE NULL 조건 추가
	 * @param string $column 컬럼
	 * @return $this
	 */
	public function whereNull($column)
	{
		$this->wheres[] = [
			'type' => 'null',
			'column' => $column,
			'not' => false
		];
		return $this;
	}

	/**
	 * WHERE NOT NULL 조건 추가
	 * @param string $column 컬럼
	 * @return $this
	 */
	public function whereNotNull($column)
	{
		$this->wheres[] = [
			'type' => 'null',
			'column' => $column,
			'not' => true
		];
		return $this;
	}

	/**
	 * WHERE DATE(column) 비교 추가
	 * @param string $column 컬럼
	 * @param string $operator 비교 연산자 또는 값 (인자 2개면 '='로 간주)
	 * @param mixed $value 값 (선택)
	 * @return $this
	 */
	public function whereDate($column, $operator = null, $value = null)
	{
		if (func_num_args() === 2) {
			$value = $operator;
			$operator = '=';
		}

		$this->wheres[] = [
			'type' => 'date',
			'column' => $column,
			'operator' => $operator,
			'value' => $value
		];
		return $this;
	}

	/**
	 * 조건부 쿼리 실행 (Laravel의 when 메서드와 동일)
	 * 
	 * @param mixed $condition 조건 (truthy/falsy 값)
	 * @param callable $callback 조건이 true일 때 실행할 콜백
	 * @param callable|null $default 조건이 false일 때 실행할 콜백 (선택사항)
	 * @return $this
	 */
	public function when($condition, callable $callback, ?callable $default = null)
	{
		if ($condition) {
			$callback($this, $condition);
		} elseif ($default) {
			$default($this, $condition);
		}

		return $this;
	}

	/**
	 * WHERE 절 빌드
	 * @param array $params
	 * @return string
	 */
	protected function buildWhereClause(&$params)
	{
		if (empty($this->wheres)) {
			return '';
		}

		$clauses = [];
		$paramIndex = count($params); // 고유 바인딩 이름 생성 기준

		foreach ($this->wheres as $where) {

			switch ($where['type']) {
			case 'basic':
				$paramName = ":param_" . $paramIndex++;
				$escapedCol = $this->escapeColumnName($where['column']);
				$clauses[] = "$escapedCol {$where['operator']} $paramName";
				$params[$paramName] = $where['value'];
				break;

			case 'or':
				$paramName = ":param_" . $paramIndex++;
				$escapedCol = $this->escapeColumnName($where['column']);
				$lastClause = array_pop($clauses); // 마지막 조건을 가져옴
				$clauses[] = "($lastClause OR $escapedCol {$where['operator']} $paramName)";
				$params[$paramName] = $where['value'];
				break;

			case 'between':
				$paramStart = ":param_" . $paramIndex++;
				$paramEnd = ":param_" . $paramIndex++;
				$escapedCol = $this->escapeColumnName($where['column']);
				$clauses[] = "$escapedCol BETWEEN $paramStart AND $paramEnd";
				$params[$paramStart] = $where['values'][0];
				$params[$paramEnd] = $where['values'][1];
				break;

			case 'notBetween':
				$paramStart = ":param_" . $paramIndex++;
				$paramEnd = ":param_" . $paramIndex++;
				$escapedCol = $this->escapeColumnName($where['column']);
				$clauses[] = "$escapedCol NOT BETWEEN $paramStart AND $paramEnd";
				$params[$paramStart] = $where['values'][0];
				$params[$paramEnd] = $where['values'][1];
				break;

				case 'in':
				$placeholders = [];
				foreach ($where['values'] as $value) {
					$paramName = ":param_" . $paramIndex++;
					$placeholders[] = $paramName;
					$params[$paramName] = $value;
				}
				$escapedCol = $this->escapeColumnName($where['column']);
				$inOperator = isset($where['not']) && $where['not'] ? 'NOT IN' : 'IN';
				$clauses[] = "$escapedCol $inOperator (" . implode(', ', $placeholders) . ")";
				break;

				case 'date':
					$paramName = ":param_" . $paramIndex++;
					$escapedCol = $this->escapeColumnName($where['column']);
					$clauses[] = "DATE($escapedCol) {$where['operator']} $paramName";
					$params[$paramName] = $where['value'];
					break;

				case 'null':
					$escapedCol = $this->escapeColumnName($where['column']);
					$clauses[] = "$escapedCol IS " . ($where['not'] ? "NOT NULL" : "NULL");
					break;

				case 'raw':
					$clauses[] = $where['sql'];
                    
                    if (isset($where['is_positional']) && $where['is_positional']) {
                        // 위치 기반 바인딩
                        foreach ($where['bindings'] as $value) {
                            $params[] = $value; // 배열의 끝에 추가 (위치 바인딩)
                        }
                    } else {
                        // 이름 기반 바인딩
                        foreach ($where['bindings'] as $key => $value) {
                            if (!isset($params[$key])) {
                                $params[$key] = $value; // 중복 방지
                            }
                        }
                    }
					break;
                    
                case 'raw_or':
                    $lastClause = array_pop($clauses); // 마지막 조건을 가져옴
                    if ($lastClause) {
                        $clauses[] = "($lastClause OR {$where['sql']})";
                    } else {
                        $clauses[] = $where['sql']; // 이전 조건이 없으면 그냥 추가
                    }
                    
                    if (isset($where['is_positional']) && $where['is_positional']) {
                        // 위치 기반 바인딩
                        foreach ($where['bindings'] as $value) {
                            $params[] = $value; // 배열의 끝에 추가 (위치 바인딩)
                        }
                    } else {
                        // 이름 기반 바인딩
                        foreach ($where['bindings'] as $key => $value) {
                            if (!isset($params[$key])) {
                                $params[$key] = $value; // 중복 방지
                            }
                        }
                    }
                    break;
                    
                case 'nested':
                    // 중첩 쿼리 처리
                    $nestedQuery = $where['query'];
                    $nestedParams = [];
                    $nestedWhere = $nestedQuery->buildWhereClause($nestedParams);
                    
                    // WHERE 키워드 제거
                    if (strpos($nestedWhere, 'WHERE ') === 0) {
                        $nestedWhere = substr($nestedWhere, 6);
                    }
                    
                    // boolean 값에 따라 AND 또는 OR로 처리
                    if (!empty($clauses) && $where['boolean'] === 'OR') {
                        $lastClause = array_pop($clauses);
                        $clauses[] = "($lastClause OR ($nestedWhere))";
                    } else {
                        $clauses[] = "($nestedWhere)";
                    }
                    
                    // 중첩 파라미터를 메인 파라미터 배열에 병합
                    foreach ($nestedParams as $key => $value) {
                        if (is_int($key)) {
                            // 위치 기반 바인딩
                            $params[] = $value;
                        } else {
                            // 이름 기반 바인딩
                            $params[$key] = $value;
                        }
                    }
                    break;
			}
		}

		return 'WHERE ' . implode(' AND ', $clauses);
	}

	/* ==========================================
     * GROUP BY, ORDER BY, LIMIT 계열 HAVING 추가해야함
     * ========================================== */
	/**
	 * GROUP BY 조건 추가
	 * @param string|array $columns
	 * @return $this
	 */
    public function groupBy(...$columns)
    {
		// 컬럼명을 백틱으로 보호
		$escapedColumns = array_map(function($column) {
			return $this->escapeColumnName($column);
		}, $columns);
        $this->groupBy = array_merge($this->groupBy, $escapedColumns);
        return $this;
    }

	/**
	 * ORDER BY 조건 추가
	 * @param string $column
	 * @param string $direction
	 * @return $this
	 */
	public function orderBy($column, $direction = 'ASC')
	{
		// 기존 $this->orderBy를 배열로 관리
		// 컬럼명에 점(.)이 포함되어 있으면 테이블명.컬럼명 형태이므로 각각 백틱 처리
		$escapedColumn = $this->escapeColumnName($column);
		$this->orderBy[] = "$escapedColumn $direction";
		return $this;
	}

	/**
	 * ORDER BY Raw SQL 표현식 추가
	 * @param string $rawSql
	 * @return $this
	 */
	public function orderByRaw($rawSql)
	{
		$this->orderBy[] = new RawExpression($rawSql); // RawExpression으로 추가
		return $this;
	}

	/**
	 * LIMIT 조건 추가
	 * @param string $value
	 * @return $this
	 */
	public function limit($value)
	{
		$this->limit = !empty($value) ? "$value" : ''; // LIMIT 키워드 제거
		return $this;
	}

	/* ==========================================
     * 실행/조회
     * ========================================== */

	/**
	 * 결과 전체 조회
	 * @param array|string|null $columns 선택적 컬럼 지정 (Laravel 스타일)
	 * @return $this
	 */
	public function get($columns = null)
	{
		// 파라미터로 컬럼이 지정되면 select() 호출
		if ($columns !== null) {
			if (is_array($columns)) {
				$this->select($columns);
			} else {
				$this->select($columns);
			}
		}

		$query = "SELECT {$this->columns} FROM {$this->table}";

		if (!empty($this->joins)) {
			$query .= " " . implode(' ', $this->joins);
		}

		$params = []; // 참조를 명확히 하기 위해 초기화
		$whereClause = $this->buildWhereClause($params); // 참조로 전달
		if (!empty($whereClause)) {
			$query .= " $whereClause";
		}

		if (!empty($this->groupBy)) {
			$query .= ' GROUP BY ' . implode(', ', $this->groupBy);
		}

		if (!empty($this->orderBy)) {
			$query .= ' ORDER BY ' . implode(', ', array_map(function ($order) {
				return $order instanceof RawExpression ? (string)$order : $order;
			}, $this->orderBy));
		}

		if (!empty($this->limit)) {
			$query .= " LIMIT {$this->limit}";
		}

		try {
			// 파라미터 확인 및 변환
			$hasNamedParams = false;
			$hasPositionalParams = false;
			$finalParams = [];
			
			// 모든 파라미터 타입 확인
			foreach ($params as $key => $value) {
				if (is_string($key)) {
					$hasNamedParams = true;
				} else {
					$hasPositionalParams = true;
				}
			}
			
			// 파라미터가 혼합된 경우
			if ($hasNamedParams && $hasPositionalParams) {
				// 모든 이름 기반 파라미터를 위치 기반으로 변환
				$converted_query = $query;
				$final_positional_params = [];
				
				// 위치 기반 파라미터 추출
				foreach ($params as $key => $value) {
					if (!is_string($key)) {
						$final_positional_params[] = $value;
					}
				}
				
				// 이름 기반 파라미터를 위치 기반으로 변환
				foreach ($params as $key => $value) {
					if (is_string($key)) {
						// :param_x를 ?로 대체
						$pos = strpos($converted_query, $key);
						if ($pos !== false) {
							$converted_query = substr_replace($converted_query, '?', $pos, strlen($key));
							$final_positional_params[] = $value;
						}
					}
				}
				
				// 변환된 쿼리와 파라미터로 실행
				$stmt = $this->db->prepare($converted_query);
				$stmt->execute($final_positional_params);
			} 
			else if ($hasNamedParams) {
				// 이름 기반 파라미터만 사용
				$stmt = $this->db->prepare($query);
				$stmt->execute($params);
			} 
			else {
				// 위치 기반 파라미터만 사용 또는 파라미터 없음
				$stmt = $this->db->prepare($query);
				$stmt->execute($hasPositionalParams ? array_values($params) : null);
			}
			
			$this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // withCount 적용
            if (!empty($this->withCounts) && !empty($this->data)) {
                $this->applyWithCounts();
            }
			return $this; // 객체 자신을 반환하여 메서드 체이닝 가능하게 함
			
		} catch (\PDOException $e) {
			echo "SQL Error: " . $e->getMessage() . "\n";
			echo "Query: " . $query . "\n";
			echo "Params: " . print_r($params, true) . "\n";
			throw $e;
		}
	}

    /**
     * 조건에 맞는 첫 번째 행을 ModelObject로 반환
	 * @return ModelObject|null
     */
    public function first()
    {
        $this->limit(1);
        $result = $this->get();
        $data = $result->toArray();
        $row = $data[0] ?? null;

        if ($row === null) {
            return null;
        }

        // 캐스트 적용
        $model = \App\Core\BaseModel::forTable($this->table);
        if ($model) {
            $row = $model->applyCastsForGet($row);
        }

        return new ModelObject($this->db, $this->table, $row, 'idx');
    }

    /**
     * Primary Key를 기준으로 단일 행을 검색
	 * @param mixed $id 찾을 레코드의 ID
	 * @param string $primaryKey 기본키 컬럼명
	 * @return ModelObject|null
     */
    public function find($id, $primaryKey = 'idx')
    {
        return $this->where($primaryKey, '=', $id)->first();
    }

	/**
	 * 단일 컬럼 값 조회 (Laravel의 value() 메서드와 유사)
	 * 
	 * @param string $column 조회할 컬럼명
	 * @return mixed|null
	 */
	public function value($column)
	{
		$this->select($column)->limit(1);
		$result = $this->get()->toArray();
		
		if (empty($result)) {
			return null;
		}
		
		return $result[0][$column] ?? null;
	}

	/**
	 * 결과 데이터를 배열로 반환
	 * @return array
	 */
	public function toArray()
	{
		return $this->data ?? [];
	}

	/**
	 * 결과 데이터 설정 (EagerLoadBuilder에서 사용)
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * 결과 배열을 특정 컬럼 값을 키로 하는 연관 배열로 변환
	 * @param string $column 키로 사용할 컬럼명
	 * @return $this
	 */
	public function keyBy($column)
	{
		// 현재 데이터가 없으면 빈 배열 반환
		if (empty($this->data)) {
			$this->data = [];
			return $this;
		}

		$result = [];
		foreach ($this->data as $item) {
			if (isset($item[$column])) {
				$result[$item[$column]] = $item;
			}
		}
		
		$this->data = $result;
		return $this;
	}

	/**
	 * 특정 컬럼의 값들만 추출 (Laravel의 pluck 메서드)
	 * @param string $column 추출할 컬럼명
	 * @param string|null $key 결과 배열의 키로 사용할 컬럼명 (선택)
	 * @return $this
	 */
	public function pluck($column, $key = null)
	{
		if (empty($this->data)) {
			$this->data = [];
			return $this;
		}

		$result = [];
		foreach ($this->data as $item) {
			if (is_array($item)) {
				$value = $item[$column] ?? null;
				
				if ($key !== null) {
					// 키 지정된 경우: ['key' => 'value']
					$keyValue = $item[$key] ?? null;
					if ($keyValue !== null) {
						$result[$keyValue] = $value;
					}
				} else {
					// 키 없는 경우: ['value1', 'value2', ...]
					$result[] = $value;
				}
			}
		}

		$this->data = $result;
		return $this;
	}

	/**
	 * 조건에 맞는 항목만 필터링 (Laravel의 filter 메서드)
	 * @param callable|null $callback 필터 조건 콜백 (null이면 truthy 값만 유지)
	 * @return $this
	 */
	public function filter($callback = null)
	{
		if (empty($this->data)) {
			return $this;
		}

		if ($callback === null) {
			// 콜백이 없으면 truthy 값만 필터링
			$this->data = array_filter($this->data);
		} else {
			// 콜백이 있으면 조건에 맞는 항목만 필터링
			$this->data = array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH);
		}

		return $this;
	}

	/**
	 * 배열 인덱스를 0부터 재정렬 (Laravel의 values 메서드)
	 * @return $this
	 */
	public function values()
	{
		if (!empty($this->data)) {
			$this->data = array_values($this->data);
		}
		return $this;
	}

	/**
	 * 전체 데이터를 배열로 반환 (Laravel의 all 메서드)
	 * @return array
	 */
	public function all()
	{
		return $this->data ?? [];
	}

    /**
     * Laravel-style 페이징 메서드 (최종 리팩토링 버전)
     *
     * @param int $perPage 페이지당 항목 수
     * @param int $page 현재 페이지 번호
     * @return array 페이징 결과 (데이터, 전체 개수, 현재 페이지 번호, 전체 페이지 수)
     */
    public function paginate2($perPage = 15, $page = 1)
    {
        // 유효성 보정
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);

        // 총 개수 계산용 쿼리 복제
        $totalQuery = clone $this;
        $totalQuery->limit('');
        $totalQuery->orderBy = [];
        $totalQuery->selectRaw('COUNT(*) as total_count');

        // 전체 개수 조회
        $totalData = $totalQuery->get()->toArray();
        $total = isset($totalData[0]['total_count']) ? (int)$totalData[0]['total_count'] : 0;

        // 마지막 페이지 계산
        $lastPage = max(1, (int)ceil($total / $perPage));

        // 현재 페이지 보정 (범위 초과 방지)
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        // 페이징 처리
        $offset = ($page - 1) * $perPage;
        $this->limit("$offset, $perPage");

        // 데이터 조회
        $data = $this->get()->toArray();

        // 결과 반환
        return [
            'data' => $data ?: [],
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }
	
    public function paginate($perPage = 15, $page = 1)
    {
        // 유효성 보정
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);

        // 총 개수 계산용 쿼리 복제
        $totalQuery = clone $this;
        $totalQuery->limit('');
        $totalQuery->orderBy = [];
        
        // 기존 select 초기화 (selectRaw로 복잡한 쿼리가 있을 수 있음)
        $totalQuery->columns = '*';

        // groupBy가 걸려 있으면 DISTINCT count로 처리
        if (!empty($this->groupBy)) {
            // 가장 첫 번째 group by 컬럼 기준으로 DISTINCT count
            $firstGroupBy = is_array($this->groupBy) ? $this->groupBy[0] : $this->groupBy;
            $totalQuery->groupBy = []; // groupBy 제거
            $totalQuery->selectRaw("COUNT(DISTINCT {$firstGroupBy}) as total_count");
        } else {
            $totalQuery->selectRaw('COUNT(*) as total_count');
        }

        // 전체 개수 조회
        $totalData = $totalQuery->get()->toArray();
        $total = isset($totalData[0]['total_count']) ? (int)$totalData[0]['total_count'] : 0;

        // 마지막 페이지 계산
        $lastPage = max(1, (int)ceil($total / $perPage));

        // 현재 페이지 보정
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        // 페이징 처리
        $offset = ($page - 1) * $perPage;
        $this->limit("$offset, $perPage");

        // 데이터 조회
        $data = $this->get()->toArray();

        // 결과 반환
        return [
            'data' => $data ?: [],
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }

	/**
	 * 쿼리 문자열 반환
	 * @return string
	 */
	public function toSql()
	{
		$query = "SELECT {$this->columns} FROM {$this->table}";

		if (!empty($this->joins)) {
			$query .= " " . implode(' ', $this->joins);
		}

		$params = [];
		$whereClause = $this->buildWhereClause($params);
		if (!empty($whereClause)) {
			$query .= " $whereClause";
		}

		if (!empty($this->groupBy)) {
			$query .= ' GROUP BY ' . implode(', ', $this->groupBy);
		}

		if (!empty($this->orderBy)) {
			$query .= ' ORDER BY ' . implode(', ', array_map(function ($order) {
				return $order instanceof RawExpression ? (string)$order : $order;
			}, $this->orderBy));
		}

		if (!empty($this->limit)) {
			$query .= " LIMIT {$this->limit}";
		}

		return $query;
	}

	/**
	 * 바인딩된 파라미터 반환
	 * @return array
	 */
	public function getBindings()
	{
		$bindings = [];
		foreach ($this->wheres as $where) {
			switch ($where['type']) {
				case 'basic':
				case 'or':
					$bindings[":param_" . count($bindings)] = $where['value'];
					break;

				case 'between':
					$bindings[":param_" . count($bindings) . "_start"] = $where['values'][0];
					$bindings[":param_" . count($bindings) . "_end"] = $where['values'][1];
					break;

				case 'in':
					foreach ($where['values'] as $value) {
						$bindings[":param_" . count($bindings)] = $value;
					}
					break;

				case 'raw':
					foreach ($where['bindings'] as $key => $value) {
						$bindings["$key"] = $value;
					}
					break;

				case 'null':
					// 바인딩 없음
					break;
			}
		}
		return $bindings;
	}

	/* ==========================================
     * 집계/존재 확인
     * ========================================== */

	/**
	 * 공통 집계 실행기
	 * @param string $function 집계 함수명 (MAX|MIN|SUM|AVG)
	 * @param string $column 대상 컬럼 또는 표현식
	 * @return mixed|null 결과값 또는 null (행이 없을 때)
	 */
	private function runAggregate(string $function, string $column)
	{
		$aggregateQuery = clone $this;
		$aggregateQuery->limit('');
		$aggregateQuery->orderBy = [];
		$aggregateQuery->groupBy = [];
		$aggregateQuery->columns = '*';
		
		// 컬럼명 백틱 처리
		$escapedColumn = $this->escapeColumnName($column);
		$aggregateQuery->selectRaw("{$function}({$escapedColumn}) AS aggregate");

		$result = $aggregateQuery->get()->toArray();
		$value = $result[0]['aggregate'] ?? null;
		if ($value === null) {
			return null;
		}
		return is_numeric($value) ? ($value + 0) : $value;
	}

	/**
	 * 최대값
	 * @param string $column
	 * @return mixed|null
	 */
	public function max(string $column)
	{
		return $this->runAggregate('MAX', $column);
	}

	/**
	 * 최소값
	 * @param string $column
	 * @return mixed|null
	 */
	public function min(string $column)
	{
		return $this->runAggregate('MIN', $column);
	}

	/**
	 * 합계
	 * @param string $column
	 * @return mixed|null
	 */
	public function sum(string $column)
	{
		return $this->runAggregate('SUM', $column);
	}

	/**
	 * 평균
	 * @param string $column
	 * @return mixed|null
	 */
	public function avg(string $column)
	{
		return $this->runAggregate('AVG', $column);
	}

	/**
	 * 쿼리 결과의 행 수를 반환
	 * @return int
	 */
	public function count()
	{
		// 현재 데이터가 있으면 그 길이 반환
		// if (isset($this->data)) {
		// 	return count($this->data);
		// }
		
		// 데이터가 없으면 쿼리 실행하여 결과 수 반환
		$query = "SELECT COUNT(*) as count FROM {$this->table}";
		
		if (!empty($this->joins)) {
			$query .= " " . implode(' ', $this->joins);
		}
		
		$params = [];
		$whereClause = $this->buildWhereClause($params);
		if (!empty($whereClause)) {
			$query .= " $whereClause";
		}
		
		if (!empty($this->groupBy)) {
			$query .= ' GROUP BY ' . implode(', ', $this->groupBy);
		}

		try {
			$stmt = $this->db->prepare($query);
			
			// 파라미터 바인딩
			if (!empty($params)) {
				foreach ($params as $key => $value) {
					$stmt->bindValue($key, $value);
				}
			}
			
			$stmt->execute();
			$result = $stmt->fetch(\PDO::FETCH_ASSOC);
			
			// 그룹화된 결과인 경우 행 수 반환
			if (!empty($this->groupBy)) {
				return $stmt->rowCount();
			}
			
			// 단일 결과인 경우 count 값 반환
			return isset($result['count']) ? (int)$result['count'] : 0;
		} catch (\PDOException $e) {
			// 오류 발생 시 0 반환
			return 0;
		}
	}

	/**
	 * 특정 조건의 레코드 존재 여부 확인
	 * @return bool
	 */
	public function exists()
	{
		// 파라미터 준비
		$params = [];
		$whereClause = $this->buildWhereClause($params);

		// 테이블 이름 안전성 보장
		$table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->table);

		$sql = "SELECT EXISTS(SELECT 1 FROM `{$table}` {$whereClause}) AS exists_flag";

		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return isset($result['exists_flag']) && (bool) $result['exists_flag'];
	}

	/**
	 * 최대값 조회
	 * @param string $column 컬럼명
	 * @return int
	 */
	public function getMax($column) {
		$escapedCol = $this->escapeColumnName($column);
		$query = "SELECT MAX($escapedCol) AS max_value FROM {$this->table}";

		// WHERE 조건 추가
		if (!empty($this->wheres)) {
			$whereClauses = array_map(function ($where) {
				$escapedCol = $this->escapeColumnName($where['column']);
				return "$escapedCol {$where['operator']} :{$where['column']}";
			}, $this->wheres);

			$query .= " WHERE " . implode(' AND ', $whereClauses);
		}

		$stmt = $this->db->prepare($query);

		// 바인딩된 파라미터 준비
		$params = [];
		foreach ($this->wheres as $where) {
			$params[$where['column']] = $where['value'];
		}

		$stmt->execute($params);
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $result['max_value'] ?? 0; // 최대값이 없으면 0 반환
	}


	/* ==========================================
     * 쓰기 수정 삭제 - insert, update, delete
     * ========================================== */

	/**
	 * 데이터 삽입
	 * @param array $data
	 * @return bool
	 */
	public function insert($data) {
		if (empty($data)) {
			throw new \Exception("Insert data cannot be empty.");
		}

		// 다중 Insert인지 단일 Insert인지 판별
		if (array_keys($data) === range(0, count($data) - 1)) {
			// 다중 Insert
			return $this->insertMultiple($data);
		} else {
			// 단일 Insert
			return $this->insertSingle($data);
		}
	}

	/**
	 * 단일 행 삽입
	 * @param array $data
	 * @return bool
	 */
	private function insertSingle($data) {
		$columns = implode(', ', array_map(function($key) {
			return "`$key`";
		}, array_keys($data)));
		$placeholders = ':' . implode(', :', array_keys($data));

		$query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
		$stmt = $this->db->prepare($query);

		foreach ($data as $key => $value) {
			$stmt->bindValue(":$key", $value);
		}

		// 실행
		$stmt->execute();

		// 마지막 삽입된 ID 반환
		return $this->db->lastInsertId();
	}


	// 다중 행 삽입 
	//PHP 7.4 이상일때
	/*
	private function insertMultiple(array $dataArray) {
		$columns = implode(', ', array_keys($dataArray[0]));
		$placeholders = '(' . implode(', ', array_map(fn($key) => ":$key", array_keys($dataArray[0]))) . ')';

		$query = "INSERT INTO {$this->table} ({$columns}) VALUES ";

		$placeholdersArray = [];
		$params = [];
		foreach ($dataArray as $index => $data) {
			$rowPlaceholders = [];
			foreach ($data as $key => $value) {
				$paramKey = "{$key}_{$index}";
				$rowPlaceholders[] = ":$paramKey";
				$params[$paramKey] = $value;
			}
			$placeholdersArray[] = '(' . implode(', ', $rowPlaceholders) . ')';
		}

		$query .= implode(', ', $placeholdersArray);
		
		$stmt = $this->db->prepare($query);
		return $stmt->execute($params);
	}
	*/

	/**
	 * 다중 행 삽입
	 * @param array $dataArray
	 * @return bool
	 */
	//PHP 7.4 이하 화살표 함수(fn) 오류로 인해
	private function insertMultiple(array $dataArray) {
		$columns = implode(', ', array_map(function($key) {
			return "`$key`";
		}, array_keys($dataArray[0])));
		$placeholders = '(' . implode(', ', array_map(function($key) {
			return ":$key";
		}, array_keys($dataArray[0]))) . ')';

		$query = "INSERT INTO {$this->table} ({$columns}) VALUES ";

		$placeholdersArray = [];
		$params = [];
		foreach ($dataArray as $index => $data) {
			$rowPlaceholders = [];
			foreach ($data as $key => $value) {
				$paramKey = "{$key}_{$index}";
				$rowPlaceholders[] = ":$paramKey";
				$params[$paramKey] = $value;
			}
			$placeholdersArray[] = '(' . implode(', ', $rowPlaceholders) . ')';
		}

		$query .= implode(', ', $placeholdersArray);
		
		$stmt = $this->db->prepare($query);
		return $stmt->execute($params);
	}


	/**
	 * 데이터 업데이트
	 * @param array $data
	 * @param array $conditions
	 * @return bool
	 */
	public function update($data, $conditions = null) {
		if (empty($data)) {
			throw new \Exception("Update data cannot be empty.");
		}

		// 다중 Update인지 단일 Update인지 판별
		if (array_keys($data) === range(0, count($data) - 1)) {
			// 다중 Update
			return $this->updateMultiple($data);
		} else {
			// 단일 Update
			if (empty($conditions)) {
				// 빌더에 이미 where 절이 설정되어 있으면 이를 사용
				if (!empty($this->wheres)) {
					return $this->updateWithBuilderConditions($data);
				}
				throw new \Exception("Conditions required for single update.");
			}
			return $this->updateSingle($data, $conditions);
		}
	}

	/**
	 * 단일 행 업데이트
	 * @param array $data
	 * @param array $conditions
	 * @return bool
	 */
	private function updateSingle($data, $conditions) {

		$setPart = implode(', ', array_map(function($key) {
			return "`$key` = :$key";
		}, array_keys($data)));

		$wherePart = implode(' AND ', array_map(function($key) {
			return "`$key` = :where_$key";
		}, array_keys($conditions)));

		$query = "UPDATE {$this->table} SET $setPart WHERE $wherePart";
		$stmt = $this->db->prepare($query);

		foreach ($data as $key => $value) {
			$stmt->bindValue(":$key", $value);
		}

		foreach ($conditions as $key => $value) {
			$stmt->bindValue(":where_$key", $value);
		}

		return $stmt->execute();

	}

	/**
	 * 다중 행 업데이트
	 * @param array $dataArray
	 * @return bool
	 */
	private function updateMultiple(array $dataArray) {
		$this->db->beginTransaction();
		try {
			foreach ($dataArray as $data) {
				if (!isset($data['conditions']) || !isset($data['update'])) {
					throw new \Exception("Each data item must contain 'update' and 'conditions' keys.");
				}
				$this->updateSingle($data['update'], $data['conditions']);
			}
			$this->db->commit();
			return true;
		} catch (\Exception $e) {
			$this->db->rollBack();
			throw new \Exception("Batch update failed: " . $e->getMessage());
		}
	}

	/**
	 * 조건이 명시되지 않은 경우, 내부 WHERE 절을 기반으로 업데이트 처리
	 * @param array $data
	 * @return bool
	 */
	private function updateWithBuilderConditions($data) {
		if (empty($this->wheres)) {
			throw new \Exception("WHERE conditions are required for update.");
		}

		// SET 절 생성 (RawExpression은 SQL로 직접 삽입)
		$setPart = implode(', ', array_map(function($key, $value) {
			return $value instanceof RawExpression ? "`$key` = $value" : "`$key` = :$key";
		}, array_keys($data), $data));

		// WHERE 절 생성
		$whereClauses = implode(' AND ', array_map(function($where) {
			$escapedCol = $this->escapeColumnName($where['column']);
			return "$escapedCol {$where['operator']} :where_{$where['column']}";
		}, $this->wheres));

		// SQL 쿼리 생성
		$query = "UPDATE {$this->table} SET $setPart WHERE $whereClauses";
		$stmt = $this->db->prepare($query);

		// SET 절의 파라미터 바인딩 (RawExpression은 제외)
		foreach ($data as $key => $value) {
			if (!$value instanceof RawExpression) {
				$stmt->bindValue(":$key", $value);
			}
		}

		// WHERE 절의 파라미터 바인딩
		foreach ($this->wheres as $where) {
			$stmt->bindValue(":where_{$where['column']}", $where['value']);
		}

		// 실행
		return $stmt->execute();
	}

	/**
	 * 연산자를 사용한 업데이트
	 * @param array $operations
	 * @param array $conditions
	 * @return bool
	 */
	public function updateWithOperation(array $operations, array $conditions = [])
	{

		$setClauses = [];
		$bindings = [];

		// SET 절 생성
		foreach ($operations as $column => [$operator, $value]) {
			if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
				throw new \Exception("Invalid column name: $column");
			}

			$paramName = ":set_" . $column;
			$setClauses[] = "`$column` = `$column` $operator $paramName";
			$bindings[$paramName] = $value;
		}

		// WHERE 절 생성 (조건이 명시되지 않은 경우, 쿼리빌더의 wheres 속성 참조)
		$whereClauses = [];
		if (empty($conditions)) {
			$conditions = $this->wheres;
			foreach ($conditions as $where) {
				$paramName = ":where_" . $where['column'];
				$whereClauses[] = "`{$where['column']}` {$where['operator']} $paramName";
				$bindings[$paramName] = $where['value'];
			}
		} else {
			foreach ($conditions as $column => $value) {
				$paramName = ":where_" . $column;
				$whereClauses[] = "`$column` = $paramName";
				$bindings[$paramName] = $value;
			}
		}

		$setClause = implode(', ', $setClauses);
		$whereClause = implode(' AND ', $whereClauses);

		$query = "UPDATE {$this->table} SET $setClause WHERE $whereClause";

		//return $query."/".json_encode($bindings, JSON_UNESCAPED_UNICODE);

		$stmt = $this->db->prepare($query);

		foreach ($bindings as $param => $val) {
			//$stmt->bindParam($param, $val);
			$stmt->bindValue($param, $val);
		}

		return $stmt->execute();

	}

    /**
	 * Increment 메서드
	 * @param string $column
	 * @param int $value
	 * @param array $conditions
	 * @return bool
	 */
	public function increment($column, $value = 1, $conditions = [])
	{
		return $this->updateWithOperation([$column => ["+", $value]], $conditions);
	}

    /**
	 * Decrement 메서드
	 * @param string $column
	 * @param int $value
	 * @param array $conditions
	 * @return bool
	 */
	public function decrement($column, $value = 1, $conditions = [])
	{
		return $this->updateWithOperation([$column => ["-", $value]], $conditions);
	}

	/**
	 * 단일 및 다중 행 삭제 메서드
	 * @param array $conditions
	 * @return bool
	 */
	public function delete($conditions = null) {
		if (empty($conditions) && empty($this->wheres)) {
			throw new \Exception("Delete operation requires conditions or WHERE clause.");
		}

		// 다중 삭제 판별
		if (is_array($conditions) && array_keys($conditions) === range(0, count($conditions) - 1)) {
			// 다중 삭제: ID 배열이 전달된 경우
			return $this->deleteMultipleByIds($conditions);
		} else {
			// WHERE 절이 설정되어 있으면 buildWhereClause 사용
			if (!empty($this->wheres)) {
				return $this->deleteWithWheres();
			} else {
				// 단일 삭제
				return $this->deleteSingle($conditions);
			}
		}
	}

	/**
	 * 단일 행 삭제 메서드
	 * @param array $conditions
	 * @return bool
	 */
	private function deleteSingle($conditions) {
		$whereClauses = implode(' AND ', array_map(function ($key) {
			return "`$key` = :$key";
		}, array_keys($conditions)));

		$query = "DELETE FROM {$this->table} WHERE $whereClauses";
		$stmt = $this->db->prepare($query);

		foreach ($conditions as $key => $value) {
			$stmt->bindValue(":$key", $value);
		}

		return $stmt->execute();
	}

	/**
	 * 다중 행 삭제 (단일 쿼리로 처리)
	 * @param array $ids
	 * @return bool
	 */
	private function deleteMultipleByIds(array $ids) {
		$placeholders = implode(', ', array_fill(0, count($ids), '?'));

		$query = "DELETE FROM {$this->table} WHERE idx IN ($placeholders)";
		$stmt = $this->db->prepare($query);

		return $stmt->execute($ids);
	}

	/**
	 * WHERE 절을 사용한 삭제 메서드
	 * @return bool
	 */
	private function deleteWithWheres() {
		$params = [];
		$whereClause = $this->buildWhereClause($params);
		
		$query = "DELETE FROM {$this->table} $whereClause";

		// 파라미터 확인 및 변환 (get()과 동일한 로직)
		$hasNamedParams = false;
		$hasPositionalParams = false;
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$hasNamedParams = true;
			} else {
				$hasPositionalParams = true;
			}
		}

		if ($hasNamedParams && $hasPositionalParams) {
			$converted_query = $query;
			$final_positional_params = [];

			// 기존 위치 기반 파라미터를 먼저 추가
			foreach ($params as $key => $value) {
				if (!is_string($key)) {
					$final_positional_params[] = $value;
				}
			}

			// 이름 기반 파라미터를 '?'로 치환하며 순서대로 추가
			foreach ($params as $key => $value) {
				if (is_string($key)) {
					$pos = strpos($converted_query, $key);
					if ($pos !== false) {
						$converted_query = substr_replace($converted_query, '?', $pos, strlen($key));
						$final_positional_params[] = $value;
					}
				}
			}

			$stmt = $this->db->prepare($converted_query);
			return $stmt->execute($final_positional_params);
		}
		else if ($hasNamedParams) {
			$stmt = $this->db->prepare($query);
			return $stmt->execute($params);
		}
		else {
			$stmt = $this->db->prepare($query);
			return $stmt->execute($hasPositionalParams ? array_values($params) : null);
		}
	}

	/**
	 * withCount 적용기: relation 이름을 통해 관련 모델/키를 찾아 그룹 카운트를 계산하고 주입
	 * @return void
	 */
	protected function applyWithCounts(): void
	{
		$model = \App\Core\BaseModel::forTable($this->table);
		if (!$model) return;

		foreach ($this->withCounts as $item) {
			$relationName = $item['name'];
			$alias = $item['alias'];

			if (!method_exists($model, $relationName)) continue;

			$relationObj = call_user_func([$model, $relationName]);
			$ref = new \ReflectionObject($relationObj);

			$class = $ref->getName();
			if ($class !== 'App\\Core\\HasManyRelation' && $class !== 'App\\Core\\HasOneRelation') {
				continue;
			}

			$relatedInstance = $this->getPrivate($ref, $relationObj, 'relatedInstance');
			$foreignKey = $this->getPrivate($ref, $relationObj, 'foreignKey');
			$localKey = $this->getPrivate($ref, $relationObj, 'localKey');

			$ownerIds = [];
			foreach ($this->data as $r) {
				if (array_key_exists($localKey, $r)) {
					$ownerIds[] = $r[$localKey];
				}
			}
			$ownerIds = array_values(array_unique($ownerIds));
			if (empty($ownerIds)) {
				foreach ($this->data as &$r) { $r[$alias] = 0; } unset($r);
				continue;
			}

		$counts = $relatedInstance->queryBuilder()
			->selectRaw("`{$foreignKey}`, COUNT(*) AS aggregate")
			->whereIn($foreignKey, $ownerIds)
			->groupBy($foreignKey)
			->get()
			->toArray();

			$byFk = [];
			foreach ($counts as $row) {
				$fk = (string)($row[$foreignKey] ?? '');
				$byFk[$fk] = (int)($row['aggregate'] ?? 0);
			}

			foreach ($this->data as &$r) {
				$fk = (string)($r[$localKey] ?? '');
				$r[$alias] = $byFk[$fk] ?? 0;
			}
			unset($r);
		}
	}

	/**
	 * 프라이빗 메서드 접근
	 * @param \ReflectionObject $ref
	 * @param object $obj
	 * @param string $name
	 * @return mixed
	 */
	private function getPrivate(\ReflectionObject $ref, $obj, string $name)
	{
		$prop = $ref->getProperty($name);
		$prop->setAccessible(true);
		return $prop->getValue($obj);
	}

	/**
	 * 컬럼명을 백틱으로 보호 (테이블명.컬럼명 형태도 지원)
	 * @param string $column 컬럼명 또는 테이블명.컬럼명
	 * @return string 백틱으로 감싼 컬럼명
	 */
	private function escapeColumnName($column)
	{
		// 이미 백틱이 있거나 * 이면 그대로 반환
		if (strpos($column, '`') !== false || $column === '*') {
			return $column;
		}

		$trimmedColumn = trim($column);

		// SQL 함수나 복잡한 표현식이 포함된 경우 그대로 반환
		// 괄호가 있으면 함수 또는 복잡한 표현식으로 간주
		if (strpos($trimmedColumn, '(') !== false || strpos($trimmedColumn, ')') !== false) {
			return $trimmedColumn;
		}

		// AS 별칭이 포함된 경우 처리: "D.nick AS guide_nick" → "`D`.`nick` AS guide_nick"
		if (preg_match('/^(.+?)\s+AS\s+(.+)$/i', $trimmedColumn, $matches)) {
			$columnPart = trim($matches[1]);
			$aliasPart = trim($matches[2]);
			
			// 컬럼 부분만 재귀적으로 처리
			$escapedColumn = $this->escapeColumnName($columnPart);
			return $escapedColumn . ' AS ' . $aliasPart;
		}

		// 테이블명.컬럼명 형태인 경우
		if (strpos($trimmedColumn, '.') !== false) {
			$parts = explode('.', $trimmedColumn);
			$tableName = $parts[0];
			$columnName = $parts[1];
			
			// 와일드카드(*)는 백틱으로 감싸지 않음: A.* → `A`.*
			if ($columnName === '*') {
				return '`' . $tableName . '`.*';
			}
			
			// 일반 컬럼: A.name → `A`.`name`
			return '`' . $tableName . '`.`' . $columnName . '`';
		}

		// 일반 컬럼명
		return '`' . $trimmedColumn . '`';
	}

	/**
	 * 테이블명을 백틱으로 보호 (별칭 처리 지원)
	 * @param string $table 테이블명 또는 "테이블명 AS 별칭"
	 * @return string 백틱으로 감싼 테이블명
	 */
	private function escapeTableName($table)
	{
		// 이미 백틱이 있으면 그대로 반환
		if (strpos($table, '`') !== false) {
			return $table;
		}

		$trimmedTable = trim($table);

		// AS 키워드로 별칭이 있는 경우 (대소문자 구분 없이)
		if (preg_match('/^(.+?)\s+AS\s+(.+)$/i', $trimmedTable, $matches)) {
			$tableName = trim($matches[1]);
			$alias = trim($matches[2]);
			return "`$tableName` AS $alias";
		}

		// 공백으로 별칭이 있는 경우 (AS 키워드 없이)
		if (preg_match('/^(\S+)\s+(\S+)$/', $trimmedTable, $matches)) {
			$tableName = trim($matches[1]);
			$alias = trim($matches[2]);
			return "`$tableName` $alias";
		}

		// 일반 테이블명
		return "`$trimmedTable`";
	}

}

/**
 * QueryBuilder에서 반환되는 모델 객체
 * 라라벨 Eloquent의 축소판
 */
class ModelObject implements \ArrayAccess, \JsonSerializable {

    private $db;
    private $table;
    private $attributes;
    private $primaryKey;
    private $original;

    public function __construct($db, $table, $attributes, $primaryKey = 'idx') {
        $this->db = $db;
        $this->table = $table;
        $this->attributes = $attributes;
        $this->original = $attributes;
        $this->primaryKey = $primaryKey;
    }

    /* ==========================================
     * 매직 Getter/Setter
     * ========================================== */
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    /* ==========================================
     * ArrayAccess 구현 (배열처럼 접근 가능)
     * ========================================== */
    public function offsetExists(mixed $offset): bool {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->attributes[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    

    public function offsetUnset(mixed $offset): void {
        unset($this->attributes[$offset]);
    }

    /* ==========================================
     * JsonSerializable 구현
     * ========================================== */
    public function jsonSerialize(): mixed {
        return $this->attributes;
    }


    /* ==========================================
     * CRUD 기능
     * ========================================== */

	/**
     * 새 레코드 생성
     *
     * @param array $data 삽입할 데이터
     * @return static|null 성공 시 ModelObject, 실패 시 null
     */
    public function create(array $data)
    {
        // BaseModel 인스턴스 가져와 fillable/casts 적용
        $model = \App\Core\BaseModel::forTable($this->table);
        if ($model) {
            $data = $model->prepareForSet($data);
        }

        $queryBuilder = new QueryBuilder($this->db);
        $insertId = $queryBuilder->table($this->table)->insert($data);

        if ($insertId) {
            // 방금 삽입된 레코드 다시 조회 (first()가 이미 ModelObject와 캐스트 적용하여 반환)
            return $queryBuilder->table($this->table)->where($this->primaryKey, $insertId)->first();
        }

        return null;
    }

    /**
     * 모델 객체 업데이트
     *
     * @param array|null $data 업데이트할 데이터 (없으면 변경된 속성만 반영)
     * @return bool 성공 여부
     */
    public function update(?array $data = null)
    {
        // 변경된 속성만 추출
        if ($data === null) {
            $data = [];
            foreach ($this->attributes as $key => $value) {
                if ($key !== $this->primaryKey && $value !== ($this->original[$key] ?? null)) {
                    $data[$key] = $value;
                }
            }
        }

        if (empty($data)) {
            return true; // 변경사항 없음
        }

        // BaseModel 캐스트 적용
        $model = \App\Core\BaseModel::forTable($this->table);
        if ($model) {
            $data = $model->prepareForSet($data);
        }

        $primaryKeyValue = $this->attributes[$this->primaryKey];
        $conditions = [$this->primaryKey => $primaryKeyValue];

        $queryBuilder = new QueryBuilder($this->db);
        $result = $queryBuilder->table($this->table)->update($data, $conditions);

        if ($result) {
            foreach ($data as $key => $value) {
                $this->attributes[$key] = $value;
                $this->original[$key]   = $value;
            }

			// 업데이트 성공 후, 메모리에 outbound casts 적용
			if ($model) {
				$model->castModelForGet($this);
			}
        }

        return $result;
    }

    /**
     * 모델 객체 삭제
     *
     * @return bool 성공 여부
     */
    public function delete()
    {
        $primaryKeyValue = $this->attributes[$this->primaryKey];
        $conditions = [$this->primaryKey => $primaryKeyValue];

        $queryBuilder = new QueryBuilder($this->db);
        return $queryBuilder->table($this->table)->delete($conditions);
    }

    /**
     * 모델의 속성을 배열로 반환
     * @return array
     */
    public function toArray() {
        return $this->attributes;
    }
}

class RawExpression {
    protected $expression;

    public function __construct($expression) {
        $this->expression = $expression;
    }

    public function __toString() {
        return $this->expression; // 문자열로 반환 (SQL 구문)
    }
}