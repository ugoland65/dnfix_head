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

    // 생성자가 PDO를 직접 받도록 수정
    public function __construct(PDO $db) {
        $this->db = $db;
    }

	// PDO의 exec 메서드 호출
    public function exec($query) {
        return $this->db->exec($query); 
    }

	//쿼리의 특정 부분에 원시 SQL 구문(raw SQL)을 삽입할 때 사용
    public function raw($expression) {
        return new RawExpression($expression);
    }

    /**
     * clearCache 메서드: 쿼리 상태 초기화
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
        return $this;
    }

    public function table($table)
    {
		$this->clearCache(); // 자동으로 캐시 제거
        $this->table = $table;
        return $this;
    }

	/* join 메서드는 기본 ON 조건만 처리하므로, 추가적인 조건(예: AND) 가능하도록 수정 */
	/* 25.01.02 Lion */
	public function join($table, $column1, $operator, $column2, $type = 'INNER', $additionalConditions = null)
	{
		$joinClause = strtoupper($type) . " JOIN $table ON $column1 $operator $column2";

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

	public function select($columns = '*')
	{
		/*
		func_num_args()를 사용하여 전달된 인자의 개수를 확인
		만약 2개 이상의 인자가 전달되었다면, func_get_args()를 사용하여 배열로 변환합니다.
		*/
		if (func_num_args() > 1) {
			$columns = func_get_args();
		}
	
		$this->columns = is_array($columns) ? implode(', ', $columns) : $columns;
		return $this;
	}

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
	 * where 메서드
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

    public function whereBetween($column, array $values)
    {
        if (count($values) !== 2) {
            throw new InvalidArgumentException("The whereBetween method requires an array with exactly 2 values.");
        }

        $this->wheres[] = [
            'type' => 'between',
            'column' => $column,
            'values' => $values
        ];

        return $this;
    }

	public function whereNotBetween($column, array $values)
	{
		if (count($values) !== 2) {
			throw new InvalidArgumentException("The whereNotBetween method requires an array with exactly 2 values.");
		}

		$this->wheres[] = [
			'type' => 'notBetween',
			'column' => $column,
			'values' => $values
		];

		return $this;
	}


	/*
	* whereRaw 메서드가 positional 매개변수를 named 방식으로 변환하도록 수정
	* 25.01.02 Lion
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
					$clauses[] = "{$where['column']} {$where['operator']} $paramName";
					$params[$paramName] = $where['value'];
					break;

				case 'or':
					$paramName = ":param_" . $paramIndex++;
					$lastClause = array_pop($clauses); // 마지막 조건을 가져옴
					$clauses[] = "($lastClause OR {$where['column']} {$where['operator']} $paramName)";
					$params[$paramName] = $where['value'];
					break;

				case 'between':
					$paramStart = ":param_" . $paramIndex++;
					$paramEnd = ":param_" . $paramIndex++;
					$clauses[] = "{$where['column']} BETWEEN $paramStart AND $paramEnd";
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
					$clauses[] = "{$where['column']} IN (" . implode(', ', $placeholders) . ")";
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

    // Increment 메서드
	public function increment($column, $value = 1, $conditions = [])
	{
		return $this->updateWithOperation([$column => ["+", $value]], $conditions);
	}

    // Decrement 메서드
	public function decrement($column, $value = 1, $conditions = [])
	{
		return $this->updateWithOperation([$column => ["-", $value]], $conditions);
	}


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
			$setClauses[] = "$column = $column $operator $paramName";
			$bindings[$paramName] = $value;
		}

		// WHERE 절 생성 (조건이 명시되지 않은 경우, 쿼리빌더의 wheres 속성 참조)
		$whereClauses = [];
		if (empty($conditions)) {
			$conditions = $this->wheres;
			foreach ($conditions as $where) {
				$paramName = ":where_" . $where['column'];
				$whereClauses[] = "{$where['column']} {$where['operator']} $paramName";
				$bindings[$paramName] = $where['value'];
			}
		} else {
			foreach ($conditions as $column => $value) {
				$paramName = ":where_" . $column;
				$whereClauses[] = "$column = $paramName";
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


	// 특정 조건의 레코드 존재 여부 확인
	// EXISTS는 다수의 레코드가 존재하더라도 첫 번째 레코드만 확인되면 즉시 종료
	// 카운터 보다 효율적
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


    public function groupBy(...$columns)
    {
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

	public function orderByRaw($rawSql, array $bindings = [])
	{
		if (!empty($bindings)) {
			// 바인딩이 있는 경우 처리
			$localBindings = [];
			$paramIndex = count($this->bindings);

			foreach ($bindings as $key => $value) {
				if (is_int($key)) {
					// 위치 기반 바인딩 (?)
					$paramName = ":orderby_param_" . $paramIndex++;
					$rawSql = preg_replace('/\?/', $paramName, $rawSql, 1);
					$this->bindings[$paramName] = $value;
					$localBindings[$paramName] = $value;
				} else {
					// 이름 기반 바인딩 (:key)
					$paramName = ":orderby_" . $key . "_" . $paramIndex++;
					$rawSql = str_replace(':' . $key, $paramName, $rawSql);
					$this->bindings[$paramName] = $value;
					$localBindings[$paramName] = $value;
				}
			}
		}
		
		$this->orderBy[] = new RawExpression($rawSql); // RawExpression으로 추가
		return $this;
	}

	public function orderBy($column, $direction = 'ASC')
	{
		// 기존 $this->orderBy를 배열로 관리
		$this->orderBy[] = "$column $direction";
		return $this;
	}

	public function limit($value)
	{
		$this->limit = !empty($value) ? "$value" : ''; // LIMIT 키워드 제거
		return $this;
	}


    /**
     * 조건에 맞는 첫 번째 행을 반환
     */
    public function first()
    {
        $this->limit(1); // 첫 번째 행만 가져오도록 제한
        $result = $this->get()->toArray(); // get()의 결과를 배열로 변환
        return $result[0] ?? null; // 첫 번째 행 또는 null 반환
    }

    /**
     * Primary Key를 기준으로 단일 행을 검색
     */
    public function find($id, $primaryKey = 'idx')
    {
        $result = $this->where($primaryKey, '=', $id)->first();
        if ($result) {
            return new ModelObject($this->db, $this->table, $result, $primaryKey);
        }
        return null;
    }

	public function get()
	{
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
			return $this; // 객체 자신을 반환하여 메서드 체이닝 가능하게 함
			
		} catch (\PDOException $e) {
			echo "SQL Error: " . $e->getMessage() . "\n";
			echo "Query: " . $query . "\n";
			echo "Params: " . print_r($params, true) . "\n";
			throw $e;
		}
	}



    /**
     * Laravel-style 페이징 메서드 (최종 리팩토링 버전)
     *
     * @param int $perPage 페이지당 항목 수
     * @param int $page 현재 페이지 번호
     * @return array 페이징 결과 (데이터, 전체 개수, 현재 페이지 번호, 전체 페이지 수)
     */
    public function paginate($perPage = 15, $page = 1)
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


    /**
     * insert
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

	// 단일 행 삽입
	private function insertSingle($data) {
		$columns = implode(', ', array_keys($data));
		$placeholders = ':' . implode(', :', array_keys($data));

		$query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
		$stmt = $this->db->prepare($query);

		foreach ($data as $key => &$value) {
			$stmt->bindParam(":$key", $value);
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

	//PHP 7.4 이하 화살표 함수(fn) 오류로 인해
	private function insertMultiple(array $dataArray) {
		$columns = implode(', ', array_keys($dataArray[0]));
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
     * update
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
			if (empty($conditions) && empty($this->wheres)) {
				throw new \Exception("Conditions required for single update.");
			}
			
			// where 조건이 설정되어 있으면 Builder 조건 사용
			if (!empty($this->wheres) && empty($conditions)) {
				return $this->updateWithBuilderConditions($data);
			}
			
			// 직접 조건이 전달되면 기존 방식 사용
			return $this->updateSingle($data, $conditions);
		}
	}

	// 단일 행 업데이트
	private function updateSingle($data, $conditions) {

		$setPart = implode(', ', array_map(function($key) {
			return "$key = :$key";
		}, array_keys($data)));

		$wherePart = implode(' AND ', array_map(function($key) {
			return "$key = :where_$key";
		}, array_keys($conditions)));

		$query = "UPDATE {$this->table} SET $setPart WHERE $wherePart";
		$stmt = $this->db->prepare($query);

		foreach ($data as $key => &$value) {
			$stmt->bindParam(":$key", $value);
		}

		foreach ($conditions as $key => &$value) {
			$stmt->bindParam(":where_$key", $value);
		}

		return $stmt->execute();

	}

	// 다중 행 업데이트
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

	// 조건이 명시되지 않은 경우, 내부 WHERE 절을 기반으로 업데이트 처리
	/*
	private function updateWithBuilderConditions($data) {
		$setPart = implode(', ', array_map(function($key) {
			return "$key = :$key";
		}, array_keys($data)));

		$whereClauses = implode(' AND ', array_map(function($where) {
			return "{$where['column']} {$where['operator']} :where_{$where['column']}";
		}, $this->wheres));

		$query = "UPDATE {$this->table} SET $setPart WHERE $whereClauses";
		$stmt = $this->db->prepare($query);

		foreach ($data as $key => &$value) {
			$stmt->bindParam(":$key", $value);
		}

		// 내부 where 조건 바인딩
		$params = [];
		foreach ($this->wheres as $where) {
			$params[":where_{$where['column']}"] = $where['value'];
		}

		return $stmt->execute($params);
	}
	*/
	private function updateWithBuilderConditions($data) {
		if (empty($this->wheres)) {
			throw new \Exception("WHERE conditions are required for update.");
		}

		// SET 절 생성 (RawExpression은 SQL로 직접 삽입)
		$setPart = implode(', ', array_map(function($key, $value) {
			return $value instanceof RawExpression ? "$key = $value" : "$key = :$key";
		}, array_keys($data), $data));

		// WHERE 절 생성
		$whereClauses = implode(' AND ', array_map(function($where) {
			return "{$where['column']} {$where['operator']} :where_{$where['column']}";
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
	 * save
	 * 주어진 데이터에 기본키가 포함되어 있으면 update, 아니면 insert 수행
	 * @param array $data 저장할 데이터
	 * @param string $primaryKey 기본키 컬럼명 (기본값: idx)
	 * @return mixed update 시 bool, insert 시 lastInsertId 문자열
	 * @throws \Exception
	 */
	public function save(array $data, $primaryKey = 'idx') {
		if (empty($data)) {
			throw new \Exception("Save data cannot be empty.");
		}

		if (empty($this->table)) {
			throw new \Exception("Table is not set. Call table() before save().");
		}

		// 기본키가 존재하고 유효한 값이면 update 처리
		if (array_key_exists($primaryKey, $data) && $data[$primaryKey] !== null && $data[$primaryKey] !== '') {
			$pkValue = $data[$primaryKey];
			$updateData = $data;
			unset($updateData[$primaryKey]);

			// 변경할 컬럼이 없으면 갱신 불필요
			if (empty($updateData)) {
				return true;
			}

			$qb = new self($this->db);
			return $qb->table($this->table)->update($updateData, [$primaryKey => $pkValue]);
		}

		// 기본키가 없으면 insert 처리
		$qb = new self($this->db);
		return $qb->table($this->table)->insert($data);
	}



	/**
	 * delete
	 * 단일 및 다중 행 삭제 메서드
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
			// 단일 삭제
			if (empty($conditions)) {
				$conditions = $this->wheres; // WHERE 절이 설정되어 있다면 사용
			}
			return $this->deleteSingle($conditions);
		}
	}

	// 단일 행 삭제 메서드
	private function deleteSingle($conditions) {
		$whereClauses = implode(' AND ', array_map(function ($key) {
			return "$key = :$key";
		}, array_keys($conditions)));

		$query = "DELETE FROM {$this->table} WHERE $whereClauses";
		$stmt = $this->db->prepare($query);

		foreach ($conditions as $key => $value) {
			$stmt->bindParam(":$key", $value);
		}

		return $stmt->execute();
	}

	// 다중 행 삭제 (단일 쿼리로 처리)
	private function deleteMultipleByIds(array $ids) {
		$placeholders = implode(', ', array_fill(0, count($ids), '?'));

		$query = "DELETE FROM {$this->table} WHERE idx IN ($placeholders)";
		$stmt = $this->db->prepare($query);

		return $stmt->execute($ids);
	}



	//최대값
	public function getMax($column) {
		$query = "SELECT MAX({$column}) AS max_value FROM {$this->table}";

		// WHERE 조건 추가
		if (!empty($this->wheres)) {
			$whereClauses = array_map(function ($where) {
				return "{$where['column']} {$where['operator']} :{$where['column']}";
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
			}
		}
		return $bindings;
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
	 * 결과 데이터를 배열로 반환
	 * @return array
	 */
	public function toArray()
	{
		return $this->data ?? [];
	}

}

/**
 * ModelObject 클래스
 * find() 메서드로 반환되는 객체를 위한 클래스입니다.
 * 라라벨 스타일의 모델 객체처럼 작동합니다.
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

    /**
     * 속성 가져오기 - 매직 메서드
     */
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    /**
     * 속성 설정 - 매직 메서드
     */
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * ArrayAccess 인터페이스 구현 - 배열처럼 접근 가능하게 함
     */
    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    /**
     * ArrayAccess 인터페이스 구현 - 배열처럼 값 가져오기
     */
    public function offsetGet($offset) {
        return $this->attributes[$offset] ?? null;
    }

    /**
     * ArrayAccess 인터페이스 구현 - 배열처럼 값 설정하기
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * ArrayAccess 인터페이스 구현 - 배열처럼 값 제거하기
     */
    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }

    /**
     * JsonSerializable 인터페이스 구현 - JSON 직렬화 지원
     */
    public function jsonSerialize() {
        return $this->attributes;
    }

    /**
     * 모델 객체 업데이트
     * @param array $data 업데이트할 데이터
     * @return bool 성공 여부
     */
    public function update(array $data = null) {
        // 데이터가 없으면 변경된 속성 사용
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

        // Primary Key로 조건 설정
        $primaryKeyValue = $this->attributes[$this->primaryKey];
        $conditions = [$this->primaryKey => $primaryKeyValue];

        // QueryBuilder 생성 및 업데이트 실행
        $queryBuilder = new QueryBuilder($this->db);
        $result = $queryBuilder->table($this->table)->update($data, $conditions);

        // 업데이트 성공 시 원본 데이터 업데이트
        if ($result) {
            foreach ($data as $key => $value) {
                $this->attributes[$key] = $value;
                $this->original[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 모델 객체 저장
     * update(...)의 래퍼
     * @param array|null $data 저장할 데이터 (null이면 변경된 속성만 저장)
     * @return bool
     */
    public function save(array $data = null) {
        return $this->update($data);
    }

    /**
     * 모델 객체 삭제
     * @return bool 성공 여부
     */
    public function delete() {
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
