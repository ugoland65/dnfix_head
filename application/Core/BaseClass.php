<?php
namespace App\Core;

use App\Classes\Database;
use App\Classes\QueryBuilder;
use App\Classes\RequestHandler; // RequestHandler 포함
use Exception;

class BaseClass {

    protected $db;
	protected $queryBuilder;
	protected $requestHandler;
	protected $postData; // POST 데이터를 저장할 프로퍼티

    public function __construct() {
        
		try {

            // Database 클래스의 싱글톤 인스턴스를 가져와서 사용
            $this->db = Database::getInstance()->getConnection();
			
            // QueryBuilder 초기화
            $this->queryBuilder = new QueryBuilder($this->db);

            // RequestHandler 초기화
            $this->requestHandler = new RequestHandler();

			$this->postData = $this->requestHandler->getAllPost(); // POST 데이터 초기화

        } catch (Exception $e) {
            throw new Exception("BaseClass Initialization Error: " . $e->getMessage());
        }
    }

    // 필요에 따라 공통 메서드 추가 가능
	/*
    protected function fetchAll($query, $params = []) {
        return Database::getInstance()->fetchAll($query, $params);
    }

    protected function fetchOne($query, $params = []) {
        return Database::getInstance()->fetchOne($query, $params);
    }

    protected function execute($query, $params = []) {
        return Database::getInstance()->execute($query, $params);
    }
	*/
}
