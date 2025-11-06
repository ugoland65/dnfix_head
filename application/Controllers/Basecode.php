<?php

/**/
/* 사용안함 */
/**/
/**/

namespace App\Controllers;

use App\Core\BaseClass;


class Basecode extends BaseClass {

    public function __construct() {
        parent::__construct();
    }

    /**
     * basecode 테이블에서 cate가 'BASECODE'인 데이터를
     * sort_order 오름차순으로 가져오는 함수
     *
     * @return array 정렬된 결과 배열
     */
    public function getSortedBasecode() {

		/*
        $query = "SELECT * FROM basecode WHERE cate = :cate ORDER BY sort_order ASC";
        $params = ['cate' => 'BASECODE'];
        return $this->db->fetchAll($query, $params);  // 정렬된 결과 반환
		*/
		return $this->queryBuilder
			->table('basecode')
			->where('cate', '=', 'BASECODE')
			->orderBy('sort_order', 'ASC')
			->get();  // 정렬된 결과 반환

    }


    public function getBasecode($cate = null) {
		
		// 인자로 받은 $cate가 null이 아니면 이를 우선 적용
		$cate = $cate ?? $this->requestHandler->getValue('cate');
		
		$data = $this->queryBuilder
			->table('basecode')
			->where('cate', '=', $cate)
			->orderBy('sort_order', 'ASC')
			->get();

		// 결과가 없으면 빈 배열 반환
		return $data ?: [];

    }


    public function saveBasecode() {
       
		try {

			$postData = $this->postData;

			$cate = $postData['cate'];
			$rows = $postData['rows'] ?? [];
			$editedRows = $postData['editedRows'] ?? [];

			if (empty($rows) && empty($editedRows)) {
				return ['status' => 'error', 'message' => '입력할 데이터가 없습니다.'];
			}

			$newCount = 0; // 신규 입력된 데이터 수
			$updatedCount = 0; // 수정된 데이터 수

			// 신규 데이터 처리
			if (!empty($rows)) {
				$maxSortOrder = $this->queryBuilder
					->table('basecode')
					->where('cate', '=', $cate)
					->getMax('sort_order');

				$dataArray = array_reduce($rows, function ($carry, $row) use (&$maxSortOrder, $cate, &$newCount) {
					if (!empty($row['name'])) {
						$newCount++;
						$carry[] = [
							'cate' => $cate,
							'code' => $row['code'],
							'name' => $row['name'],
							'memo' => $row['memo'],
							'sort_order' => ++$maxSortOrder,
						];
					}
					return $carry;
				}, []);

				if (!empty($dataArray)) {
					$this->queryBuilder
						->table('basecode')
						->insert($dataArray);
				}
			}

			// 수정 데이터 처리
			if (!empty($editedRows)) {
				$updateData = array_map(function ($row) use (&$updatedCount) {
					$updatedCount++;
					return [
						'update' => [
							'cate' => $row['cate'],
							'code' => $row['code'],
							'name' => $row['name'],
							'memo' => $row['memo'],
						],
						'conditions' => [
							'idx' => $row['idx'],
						],
					];
				}, $editedRows);

				$this->queryBuilder
					->table('basecode')
					->update($updateData);
			}

			// 결과 메시지 생성
			return [
				'status' => 'success',
				'message' => "신규 {$newCount}건, 수정 {$updatedCount}건 처리되었습니다."
			];

        } catch (\Exception $e) {

            return ['status' => 'error', 'message' => $e->getMessage()];

        }

    }


    /**
     * basecode 수정
     *
     */
    public function modify() {

        try {

			$postData = $this->postData;

			$idx = $postData['idx'];
			$code = $postData['rowData']['code'];
			$name = $postData['rowData']['name'];
			$memo = $postData['rowData']['memo'];

			//return $postData;

			$updateData = [
				'code' => $code,
				'name' => $name,
				'memo' => $memo,
			];

			$this->queryBuilder
				->table('basecode')
				->update($updateData, ['idx' => $idx]);

            return ['status' => 'success', 'message' => '삭제 완료'];

        } catch (\Exception $e) {

            return ['status' => 'error', 'message' => $e->getMessage()];

        }

    }


    /**
     * basecode 테이블에서 삭제
     *
     */
    public function delete() {

        try {

			$postData = $this->postData;

			$idx = $postData['idx'];

			if (empty($idx)) {
				return ['status' => 'error', 'message' => '처리할 데이터가 없습니다.'];
			}

			// 다중 삭제 판별
			if (is_array($idx)) {
				$this->queryBuilder
					->table('basecode')
					->delete($idx);
			} else {
				$this->queryBuilder
					->table('basecode')
					->delete(['idx' => $idx]);
			}

            return ['status' => 'success', 'message' => '삭제 완료'];

        } catch (\Exception $e) {

            return ['status' => 'error', 'message' => $e->getMessage()];

        }
    }




}