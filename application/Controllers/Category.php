<?php
namespace App\Controllers;

use App\Core\BaseClass;


class Category extends BaseClass {

    public function __construct() {
        parent::__construct();
    }


	/**
	 * tree에 맞는 구조로 포맷
	 */
	private function formatTree($nodes) {
		$formatted = [];

		foreach ( $nodes as $node ) {

			//jstree
			$formattedNode = [
				'id' => $node['idx'],
				'text' => $node['name'],
				'data' => [
					'node_path' => $node['node_path'],
					'depth' => $node['depth']
				],
			];

			/*
			Fancytree
			$formattedNode = [
				'title' => $node['name'],
				'key' => $node['idx'],
				'node_path' => $node['node_path'],
				'folder' => !empty($node['children']),
			];
			*/

			if (!empty($node['children'])) {
				$formattedNode['children'] = $this->formatTree($node['children']);
			}

			$formatted[] = $formattedNode;
		}

		return $formatted;
	}


	/**
	 * 트리 불러오기
	 */
	public function getTree() {

		$postData = $this->postData;
		$group_code = $postData['group_code'];
		$parentNodePath = $postData['parentNodePath'] ?? null;

		$query = $this->queryBuilder
			->table('category')
			->where('group_code', '=', $group_code);

		if (!is_null($parentNodePath) && trim($parentNodePath) !== '') {
			$query->where('node_path', 'LIKE', $parentNodePath.'%');
		}

		$results = $query->orderBy('sort_order', 'ASC')
			->get();

		// 부모-자식 관계 매핑
		$tree = [];
		$lookup = [];

		foreach ($results['data'] as $row) {
			$row['children'] = []; // 자식 노드 초기화
			$lookup[$row['idx']] = $row; // 인덱스 기반 저장
		}

		foreach ($lookup as $key => &$node) {
			if (($node['parent'] ?? null) && isset($lookup[$node['parent']])) {
				$lookup[$node['parent']]['children'][] = &$node;
			} else {
				$tree[] = &$node;
			}
			unset($node); // 참조 해제
		}

		return $this->formatTree($tree);

	}


    /**
     * 생성
     */
    public function createCategory() {

		try {

			$postData = $this->postData;
		
			//return $postData;
			$group_code = $postData['group_code'];
			$depth = $postData['depth'] ?? 1;
			$parentKey = $postData['parentKey'] ?? null;
			$parentNodePath = $postData['parentNodePath'] ?? null;
			$name = $postData['name'];


			$query = $this->queryBuilder
				->table('category')
				->where('group_code', '=', $group_code);

			if ($parentKey) {
				$query->where('parent', '=', $parentKey);
			}

			$maxSortOrder = $query->getMax('sort_order');

			/*
			// 최대 sort_order 가져오기
			$maxSortOrder = $this->queryBuilder
				->table('category')
				->where('group_code', '=', $group_code)
				->when($parentKey, function($query) use ($parentKey) {
					return $query->where('parent', '=', $parentKey);
				})
				->getMax('sort_order') ?? 0; // null일 경우 기본값 0
			*/
			$insertData = [
				'group_code' => $group_code,
				'depth' => $depth,
				'parent' => $parentKey,
				'name' => $name,
				'sort_order' => ++$maxSortOrder,
			];

			$lastInsertId = $this->queryBuilder
				->table('category')
				->insert($insertData);


			//node_path 다시 저장하기
			//root 노드일경우
			if( $depth == 1 ){
				
				$node_path = "/{$lastInsertId}/";

			}else{
				if (!empty($parentNodePath)) {
					$node_path = "{$parentNodePath}{$lastInsertId}/";
				} else {
					// parentNodePath가 없을 경우 기본 값 설정 (예외 처리)
					throw new \Exception("부모 노드 경로가 누락되었습니다.");
				}
			}

			$updateData = [
				'node_path' => $node_path
			];

			$this->queryBuilder
				->table('category')
				->update($updateData, ['idx' => $lastInsertId]);

			return [
				'status' => 'success',
				'insert_id' => $lastInsertId,
				'message' => "신규등록 처리되었습니다."
			];


			//return json_encode(['status' => 'success', 'insert_id' => $lastInsertId, 'message' => '신규등록 처리되었습니다.']);

        } catch (\Exception $e) {

            return ['status' => 'error', 'message' => $e->getMessage()];

        }

	}


	/**
	 * 카테고리 정보보기
	 */
	public function getCateInfo() {

		$postData = $this->postData;
		$idx = $postData['idx'];

		$results = $this->queryBuilder
			->table('category')
			->find($idx);

		//테스트
		$test = [];

		return [
			'category' => $results,
			'test' => $test
		];

	}


    /**
     *  수정
     */
    public function modify() {

        try {

			$postData = $this->postData;

			$idx = $postData['idx'];
			$code = $postData['code'];
			$name = $postData['name'];
			$memo = $postData['memo'];

			// 유효성 검사
			if (empty($name) || trim($name) === '') {
				throw new \InvalidArgumentException('이름 필드는 비어 있을 수 없습니다.');
			}

			$updateData = [
				'code' => $code,
				'name' => $name,
				'memo' => $memo,
			];

			$this->queryBuilder
				->table('category')
				->update($updateData, ['idx' => $idx]);

            return ['status' => 'success', 'message' => '수정 완료'];

		} catch (\InvalidArgumentException $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => '시스템 오류가 발생했습니다.'];
		}

    }


    /**
     *  테이블에서 삭제
     */
    public function delete() {

        try {

			//$this->queryBuilder->db->beginTransaction(); // 트랜잭션 시작

			$postData = $this->postData;

			$idx = $postData['idx'];
			$group_code = $postData['group_code'];
			$nodeDepth = $postData['nodeDepth'];
			$parentNodePath = $postData['parentNodePath'];

			if (empty($idx)) {
				return ['status' => 'error', 'message' => '처리할 데이터가 없습니다.'];
			}

			// 다중 삭제 판별
			if (is_array($idx)) {
				$this->queryBuilder
					->table('category')
					->delete($idx);
			} else {
				$this->queryBuilder
					->table('category')
					->delete(['idx' => $idx]);
			}

			$results = $this->queryBuilder
				->table('category')
				->where('group_code', '=', $group_code)
				->where('node_path', 'LIKE', $parentNodePath.'%')
				->where('depth', '=', $nodeDepth)
				->orderBy('sort_order', 'ASC')
				->get();

			if( $results['total'] > 0 ){

				$_new_sort_order = 0;
				$updateData = []; // 초기화

				foreach ($results['data'] as $row) {
				
					$updateData[] =	[
						'update' => [
							'sort_order' => ++$_new_sort_order,
						],
						'conditions' => [
							'idx' => $row['idx'],
						]
					];

				}

				$this->queryBuilder
					->table('category')
					->update($updateData);

			}
			/*
			$this->queryBuilder->exec("SET @new_sort_order = 0");
			$this->queryBuilder
				->table('category')
				->where('group_code', '=', $group_code)
				->where('node_path', 'LIKE', $parentNodePath . '%')
				->orderBy('sort_order', 'ASC')
				->updateWithBuilderConditions([
					'sort_order' => $this->queryBuilder->raw("(@new_sort_order := @new_sort_order + 1)")
				]);
			*/

			//$this->queryBuilder->db->commit(); // 트랜잭션 커밋

            return ['status' => 'success', 'message' => '삭제 완료'];

        } catch (\Exception $e) {

            return ['status' => 'error', 'message' => $e->getMessage()];

        }
    }


    /**
     * Drag & Drop 처리 메서드
     * @param int $nodeId 드래그된 노드의 ID
     * @param int $newParentId 새 부모 노드의 ID
     * @param int $newPosition 새 부모 노드 내에서의 위치
     * @return array 응답 상태와 메시지
     */
    public function dnd() {
	
        try {

            // POST 데이터 받기
            $postData = $this->postData;

            $nodeId = $postData['node_id']; // 드래그된 노드의 ID
            //$newParentId =  (int) $postData['new_parent_id']; // 새로운 부모의 ID
			$newParentId = $postData['new_parent_id']; // #이 넘어 올수도 있음
            $newPosition = $postData['new_position']; // 새로운 정렬 위치
            $oldParentId = $postData['old_parent_id']; // 이전부모


			// 기존 노드 정보 조회
			$currentNode = $this->queryBuilder
				->table('category')
				->find($nodeId);

			$group_code = $currentNode['group_code'];

			if (!$currentNode) {
				throw new \Exception("드래그된 노드를 찾을 수 없습니다.");
			}

			//최상위 루트로 이동함
			if( $newParentId == "#" ){

				$newNodePath = '/' . $nodeId . '/';
				$newDepth =  1;

			}else{

				// 새 부모 노드의 node_path 조회
				$parentNode = $this->queryBuilder
					->table('category')
					->find($newParentId);

				if (!$parentNode) {
					throw new \Exception("새 부모 노드를 찾을 수 없습니다.");
				}

				$newNodePath = $parentNode['node_path'] . $nodeId . '/';
				$newDepth = $parentNode['depth'] + 1;

			}


			// 값이 변경된 경우에만 업데이트 실행
			$updateData = [];

			if ($currentNode['parent'] != $newParentId) {
				$updateData['parent'] = $newParentId;
			}

			if ($currentNode['node_path'] != $newNodePath) {
				$updateData['node_path'] = $newNodePath;
			}

			if ($currentNode['depth'] != $newDepth) {
				$updateData['depth'] = $newDepth;
			}

			if (!empty($updateData)) {
				$this->queryBuilder
					->table('category')
					->update($updateData, ['idx' => $nodeId]);
			}

			// ------------------------------------------------------------------------------------------------------------------
			// $newPosition 값 보정
			$adjustedPosition = $newPosition + 1; // sort_order는 1부터 시작

			// 새로운 위치가 기존의 sort_order와 다를 경우에만 정렬 업데이트
			$currentSortOrder = $currentNode['sort_order']; // 현재 노드의 sort_order
			if ( $currentSortOrder !== $adjustedPosition ) { // +1로 보정
				
				//캐싱 제거
				//$this->queryBuilder->clearCache();

				// 새 부모에서 모든 자식 노드 가져오기
				$childNodes = $this->queryBuilder
					->table('category')
					->where('group_code', '=', $group_code)
					->where('parent', '=', $newParentId)
					->orderBy('sort_order', 'ASC')
					->get();

				if (empty($childNodes['data'])) {
					//throw new \Exception("No child nodes found for parent ID {$newParentId}. total {$childNodes['total']} ");
				}


				$updatedNodes = [];
				$currentSortOrder = 0;
				$nodeInserted = false; // 노드 삽입 여부 확인

				foreach ($childNodes['data'] as $childNode) {

					if ($childNode['idx'] == $nodeId) {
						continue; // 이동할 노드를 제외하고 처리
					}

					// 새로운 위치에 이동된 노드 삽입
					if (!$nodeInserted && $currentSortOrder + 1 == $adjustedPosition) {
						$updatedNodes[] = [
							'update' => ['sort_order' => ++$currentSortOrder],
							'conditions' => ['idx' => $nodeId]
						];
						$nodeInserted = true;
					}

					// 기존 노드의 정렬 순서 갱신
					$updatedNodes[] = [
						'update' => ['sort_order' => ++$currentSortOrder],
						'conditions' => ['idx' => $childNode['idx']]
					];
				}

				// 이동된 노드가 마지막에 위치해야 하는 경우 처리
				if (!$nodeInserted) {
					$updatedNodes[] = [
						'update' => ['sort_order' => ++$currentSortOrder],
						'conditions' => ['idx' => $nodeId]
					];
				}

				if (!empty($updatedNodes)) {
					// 다중 업데이트 실행
					$this->queryBuilder
						->table('category')
						->update($updatedNodes);
				}
				
				//return $newParentId."/".$childNodes;
				//throw new \Exception("위치 변경 발견!");
				//$this->updateSortOrder($newParentId, $nodeId, $newPosition);

			}

			// ------------------------------------------------------------------------------------------------------------------
			// 부모가 바뀌는 상황일경우
			if ($currentNode['parent'] != $newParentId) {
			
				// 자식 노드들의 node_path도 재귀적으로 업데이트
				$this->updateChildNodePaths($nodeId, $newNodePath);
			
				//빠져나간 예전부모 sort_order도 다시 재정비해줘야 함
				$this->reorderSortOrder($oldParentId);

			}


   

			// 이동된 노드의 정렬 위치 업데이트
			//$this->updateSortOrder($newParentId, $nodeId, $newPosition);




            return ['status' => 'success', 'message' => '노드가 성공적으로 이동되었습니다.'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
	}

	/**
     * 자식 노드들의 node_path 재귀적으로 업데이트
     * @param int $parentId 부모 노드 ID
     * @param string $parentNodePath 새로운 부모 노드의 node_path
     */
    private function updateChildNodePaths($parentId, $parentNodePath) {
        
		$isChild = $this->queryBuilder
			->table('category')
			->where('parent', '=', $parentId)
			->exists();

		//자식이 존재할 경우
		if ($isChild) {

			$childNodes = $this->queryBuilder
				->table('category')
				->where('parent', '=', $parentId)
				->get();

			foreach ($childNodes['data'] as $childNode) {
				$newChildPath = $parentNodePath . $childNode['idx'] . '/';
				$this->queryBuilder
					->table('category')
					->update([
						'node_path' => $newChildPath
					], ['idx' => $childNode['idx']]);

				// 재귀 호출로 하위 자식 노드 업데이트
				$this->updateChildNodePaths($childNode['idx'], $newChildPath);
			}

		}

    }


    /**
     * 특정 부모 노드 아래의 자식 노드들 sort_order 재정렬
     * @param int $parentId 부모 노드 ID
     */
    private function reorderSortOrder($parentId) {
        $childNodes = $this->queryBuilder
            ->table('category')
            ->where('parent', '=', $parentId)
            ->orderBy('sort_order', 'ASC')
            ->get();

        $newSortOrder = 0;
        foreach ($childNodes['data'] as $childNode) {
            $this->queryBuilder
                ->table('category')
                ->update([
                    'sort_order' => ++$newSortOrder
                ], ['idx' => $childNode['idx']]);
        }
    }


	/**
	 * 새 위치 기반으로 정렬 재정렬
	 * @param int $parentId 새 부모 노드의 ID
	 * @param int $nodeId 이동된 노드의 ID
	 * @param int $newPosition 새로운 위치
	 */
	/*
	private function updateSortOrder($parentId, $nodeId, $newPosition) {
		try {
			
			$parentId = (int) $parentId;

			if ($newPosition < 0) {
				throw new \Exception("Invalid new position value: {$newPosition}");
			}

			// $newPosition 값 보정
			$adjustedPosition = $newPosition + 1; // sort_order는 1부터 시작

			// 캐시 초기화
			//$this->queryBuilder->clearCache();

			// 새 부모의 모든 자식 노드 가져오기
			$childNodes = $this->queryBuilder
				->table('category')
				->where('parent', '=', $parentId)
				->orderBy('sort_order', 'ASC')
				->get();

			if (empty($childNodes['data'])) {
			//if ( $childNodes['total'] == 0 ) {
				throw new \Exception("No child nodes found for parent ID {$parentId}. total {$childNodes['total']} / new position value: {$newPosition} ");
			}

			$updatedNodes = [];
			$currentSortOrder = 0;
			$nodeInserted = false; // 노드 삽입 여부 확인

			foreach ($childNodes['data'] as $childNode) {
				if ($currentSortOrder + 1 == $adjustedPosition && !$nodeInserted) {
					// 이동된 노드를 새로운 위치에 삽입
					$updatedNodes[] = [
						'update' => ['sort_order' => ++$currentSortOrder],
						'conditions' => ['idx' => $nodeId]
					];
					$nodeInserted = true;
				}

				// 기존 노드의 정렬 순서 갱신
				$updatedNodes[] = [
					'update' => ['sort_order' => ++$currentSortOrder],
					'conditions' => ['idx' => $childNode['idx']]
				];
			}

			// 이동된 노드가 마지막에 위치해야 하는 경우 처리
			if (!$nodeInserted) {
				$updatedNodes[] = [
					'update' => ['sort_order' => ++$currentSortOrder],
					'conditions' => ['idx' => $nodeId]
				];
			}

			if (empty($updatedNodes)) {
				throw new \Exception("No nodes to update. 'updatedNodes' array is empty.");
			}

			// 다중 업데이트 실행
			$this->queryBuilder
				->table('category')
				->updateMultiple($updatedNodes);

		} catch (\Exception $e) {
			throw new \Exception("Failed to update sort order: " . $e->getMessage());
		}

	}
	*/


}