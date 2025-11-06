<?php
namespace App\Controllers\Admin;

use App\Core\AuthAdmin;
use App\Core\BaseClass;
use App\Models\BasecodeModel;

class Basecode extends BaseClass {

	/**
	 * 베이스코드 INDEX
	 */
    public function basecodeIndex() {

		$getData = $this->requestHandler->getAll(); // GET 데이터 받기

		$BasecodeModel = new BasecodeModel();

		$maincode_result = $BasecodeModel->queryBuilder()
			->where('cate', '=', 'BASECODE')
			->orderBy('sort_order', 'ASC')
			->get();

		$cate = $getData['cate'];

		$data_result = $BasecodeModel->queryBuilder()
			->where('cate', '=', $cate)
			->orderBy('sort_order', 'ASC')
			->get();

		return [
			"maincode" => $maincode_result,
			"data" => $data_result,
			"data_total" => count($data_result)
		];

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


}
