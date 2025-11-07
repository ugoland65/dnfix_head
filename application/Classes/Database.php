<?php
namespace App\Classes;

class Database {

	private static $instance = null;
    private $conn;

    public function __construct() {

		$config = require __DIR__ . '/../config/config.php'; 

		$host = $config['db_host'];
		$db_name = $config['db_name'];
		$username = $config['db_user'];
		$password = $config['db_pass'];

		try {
			$this->conn = new \PDO("mysql:host={$host};dbname={$db_name};charset=utf8", $username, $password);
			$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			echo "Connection error: " . $e->getMessage();
			$this->conn = null;
			throw new \Exception("Database connection failed: " . $e->getMessage());
		}

    }


    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
	/*
    public static function getInstance() {
        return new self();
    }
	*/

    public function getConnection() {
        return $this->conn;
    }

	//연결 해제 메서드
    public function disconnect() {
        $this->conn = null;
    }

    public function beginTransaction() {
        $this->conn->beginTransaction();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function rollBack() {
        $this->conn->rollBack();
    }

    /**
     * Helper 기능: Fetch All
     */
	public function fetchAll($query, $params = []) {
		if (!$this->conn) {
			throw new Exception("Database connection is not established.");
		}

		try {
			$stmt = $this->conn->prepare($query);
			$stmt->execute($params);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception("쿼리 실행 중 오류 발생: " . $e->getMessage() . " | Query: $query");
		}
	}

    /**
     * Helper 기능: Fetch One
     */
    public function fetchOne($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("쿼리 실행 중 오류 발생: " . $e->getMessage());
        }
    }

    /**
     * Helper 기능: Execute (삽입/수정/삭제용)
     */
	public function execute($query, $params = []) {
		if (!$this->conn) {
			throw new Exception("Database connection이 설정되지 않았습니다.");
		}

		try {
			$stmt = $this->conn->prepare($query);
			return $stmt->execute($params);
		} catch (PDOException $e) {
			throw new Exception("쿼리 실행 중 오류 발생: " . $e->getMessage() . " | Query: $query");
		}
	}

    /**
     * Helper 기능: 조건을 바탕으로 데이터 조회
     */
    public function select($table, $columns = "*", $where = []) {
        $wherePartString = "";
        if (!empty($where)) {
            $wherePart = [];
            foreach ($where as $key => $value) {
                $wherePart[] = "$key = :$key";
            }
            $wherePartString = "WHERE " . implode(" AND ", $wherePart);
        }

        $query = "SELECT $columns FROM $table $wherePartString";
        return $this->fetchAll($query, $where);
    }

    /**
     * Helper 기능: 특정 컬럼의 최대값 가져오기
     */
    public function getMaxValue($table, $column, $conditions = []) {
        $query = "SELECT MAX($column) AS max_value FROM $table";
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :$key";
            }
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $result = $this->fetchOne($query, $conditions);
        return $result['max_value'] ?? 0; // null이면 0 반환
    }


	// 단일 INSERT
    public function insert($table, $data) {
        $fields = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        return $stmt->execute();
    }

    // 다중 INSERT
    public function insertMultiple($table, $dataArray) {
        if (empty($dataArray)) {
            throw new Exception("데이터 배열이 비어 있습니다.");
        }

        $fields = implode(", ", array_keys($dataArray[0]));
        $placeholders = "(" . implode(", ", array_map(function ($key) {
            return ":$key";
        }, array_keys($dataArray[0]))) . ")";

        $query = "INSERT INTO $table ($fields) VALUES $placeholders";

        $stmt = $this->conn->prepare($query);

        $this->conn->beginTransaction(); // 트랜잭션 시작

        try {
            foreach ($dataArray as $data) {
                foreach ($data as $key => &$value) {
                    $stmt->bindParam(":$key", $value);
                }

                $stmt->execute();
            }

            $this->conn->commit(); // 커밋
        } catch (Exception $e) {
            $this->conn->rollBack(); // 롤백
            throw new Exception("다중 INSERT 중 오류 발생: " . $e->getMessage());
        }

        return true;
    }

    // UPDATE 기능
    public function update($table, $data, $where) {
        $setPart = [];
        foreach ($data as $key => $value) {
            $setPart[] = "$key = :$key";
        }
        $setPartString = implode(", ", $setPart);

        $wherePart = [];
        foreach ($where as $key => $value) {
            $wherePart[] = "$key = :where_$key";
        }
        $wherePartString = implode(" AND ", $wherePart);

        $query = "UPDATE $table SET $setPartString WHERE $wherePartString";
        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
        foreach ($where as $key => &$value) {
            $stmt->bindParam(":where_$key", $value);
        }

        return $stmt->execute();
    }

    // DELETE 기능
    public function delete($table, $where) {
        $wherePart = [];
        foreach ($where as $key => $value) {
            $wherePart[] = "$key = :$key";
        }
        $wherePartString = implode(" AND ", $wherePart);

        $query = "DELETE FROM $table WHERE $wherePartString";
        $stmt = $this->conn->prepare($query);

        foreach ($where as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        return $stmt->execute();
    }

}
