<?php

namespace App\Services;

use Exception;
use App\Models\OrderSheetModel;
use App\Models\WorkLogModel;
use App\Models\ProductModel;
use App\Models\CalendarModel;
use App\Models\CommentModel;
use App\Models\AdminModel;
use App\Models\WorkViewCheckModel;
use App\Models\CsRequestModel;


class CommentService
{
    
    /**
     * 코멘트 챗 데이터 조회
     */
    public function getCommentList($payload)
    {

		$ad_idx = $payload['ad_idx'] ?? null;
		if (empty($ad_idx)) {
			throw new Exception('ad_idx는 필수입니다.');
		}

        $mode = $payload['mode'] ?? 'unchecked';
        $paging = $payload['paging'] ?? false;
        $page = $payload['page'] ?? 1;
        $perPage = $payload['per_page'] ?? 50;

		$_target_mb_text = "@" . $ad_idx;
		$ad_idx_int = (int) $ad_idx;

		$viewSub = WorkViewCheckModel::query()
			->select(['tidx', 'mode'])
			->whereRaw("mb_idx = {$ad_idx_int}")
			->groupBy('tidx')
			->groupBy('mode');

		$query = CommentModel::query()
			->from('work_comment AS A')
			->select([
				'A.idx',
				'A.mode',
				'A.tidx',
				'A.mb_idx',
				'A.comment',
				'A.reg_date',
				'C.ad_name',
				'C.ad_image',
			])
			->join('admin AS C', 'C.idx', '=', 'A.mb_idx', 'LEFT')
			->joinSub($viewSub, 'B', function ($join) {
				$join->on('B.tidx', '=', 'A.idx');
				$join->on('B.mode', '=', 'A.mode');
			}, 'LEFT')
			->whereRaw("A.mention_mb LIKE CONCAT('%', :target_mb_text, '%')", ['target_mb_text' => $_target_mb_text])
			->when($mode === 'checked', function ($query) {
				$query->whereNotNull('B.tidx');
			}, function ($query) {
				$query->whereNull('B.tidx');
			})
			->orderBy('A.idx', 'DESC');
 
        if ($paging) {
            $results = $query->paginate($perPage, $page);
            $rows = $results['data'] ?? [];
        } else {
            $rows = $query->limit($perPage)->get()->toArray();
            $results = [
                'data' => $rows,
            ];
        }

		// 모드별 제목 정보 배치 조회 (N+1 방지)
		$titleMap = $this->getTitleInfoBatch($rows);
		foreach ($rows as &$list) {
			$modeKey = $list['mode'] ?? '';
			$tidxKey = $list['tidx'] ?? '';
			$TitleInfo = $titleMap[$modeKey][$tidxKey] ?? ['title_mode' => '', 'title_name' => ''];
			$list['target']['mode_text'] = $TitleInfo['title_mode'];
			$list['target']['subject'] = $TitleInfo['title_name'];
		}

		return [
			'list' => $rows,
            'pagination' => $paging ? $results : null,
		];

    }

    /**
     * 타이틀 정보 배치 조회 (N+1 방지)
     *
     * @param array $rows
     * @return array
     */
    protected function getTitleInfoBatch(array $rows): array
    {
        $modes = [
            'orderSheet' => [
                'model' => OrderSheetModel::class,
                'field' => 'oo_name',
                'title' => '주문서',
            ],
            'log' => [
                'model' => WorkLogModel::class,
                'field' => 'subject',
                'title' => '업무 게시판',
            ],
            'prd' => [
                'model' => ProductModel::class,
                'field' => 'CD_NAME',
                'title' => '상품',
            ],
            'calendar' => [
                'model' => CalendarModel::class,
                'field' => 'subject',
                'title' => '캘린더',
            ],
            'cs' => [
                'model' => CsRequestModel::class,
                'field' => 'order_no',
                'title' => 'C/S',
            ],
        ];

        $modeGroups = [];
        foreach ($rows as $row) {
            $rowMode = $row['mode'] ?? '';
            $rowTidx = $row['tidx'] ?? null;
            if (!$rowMode || $rowTidx === null) {
                continue;
            }
            $modeGroups[$rowMode][] = $rowTidx;
        }

        $titleMap = [];
        foreach ($modeGroups as $rowMode => $tidxs) {
            if (!isset($modes[$rowMode])) {
                continue;
            }

            $modelClass = $modes[$rowMode]['model'];
            $modelInstance = new $modelClass();
            $field = $modes[$rowMode]['field'];
            $title = $modes[$rowMode]['title'];
            $primaryKey = $modelInstance->getPrimaryKey();

            $uniqueTidxs = array_values(array_unique($tidxs));
            $rowsData = $modelClass::query()
                ->select([$primaryKey, $field])
                ->whereIn($primaryKey, $uniqueTidxs)
                ->get()
                ->toArray();

            foreach ($rowsData as $dataRow) {
                $pkValue = $dataRow[$primaryKey] ?? null;
                if ($pkValue === null) {
                    continue;
                }
                $titleMap[$rowMode][$pkValue] = [
                    'title_mode' => $title,
                    'title_name' => $dataRow[$field] ?? '',
                ];
            }
        }

        return $titleMap;
    }


    /**
     * 코멘트 챗 데이터 조회
     */
    public function getCommentChat(array $payload): array
    {
        $mode = $payload['mode'] ?? null;
        $tidx = $payload['tidx'] ?? null;

        if (empty($mode) || empty($tidx)) {
            throw new Exception('mode와 tidx는 필수입니다.');
        }

        // 제목 정보
        $titleInfo = $this->getTitleInfo($mode, $tidx);
        $_title_mode = $titleInfo['title_mode'];
        $_title_name = $titleInfo['title_name'];

        // 주요 댓글 데이터 조회
        $commentRows = CommentModel::query()
            ->from('work_comment AS A') // 별칭 사용
            ->select([
                'A.*',
                'B.ad_nick',
                'B.ad_image',
            ])
            ->join('admin AS B', 'B.idx', '=', 'A.mb_idx', 'LEFT')
            ->where('A.mode', '=', $mode)
            ->where('A.kind', '=', 'S')
            ->where('A.tidx', '=', $tidx)
            ->orderBy('A.idx', 'ASC')
            ->get()
            ->toArray();

        // 댓글이 없으면 바로 리턴
        if (empty($commentRows)) {
            return [
                'title' => [
                    'mode' => $_title_mode,
                    'name' => $_title_name,
                ],
                'comment' => [],
            ];
        }

        // 모든 mention_mb ID 수집 (array_merge 반복 제거 → set 방식)
        $mentionIdSet = [];
        foreach ($commentRows as $comment) {
            if (!empty($comment['mention_mb'])) {
                foreach (explode('@', $comment['mention_mb']) as $mid) {
                    $mid = trim($mid);
                    if ($mid !== '') {
                        $mentionIdSet[$mid] = true;
                    }
                }
            }
        }
        $mentionIds = array_keys($mentionIdSet);

        // mention_mb 데이터 일괄 조회
        $mentionData = [];
        if (!empty($mentionIds)) {
            $mentionsQuery = AdminModel::query()
                ->select(['idx', 'ad_nick', 'ad_name', 'ad_image'])
                ->whereIn('idx', $mentionIds)
                ->get()
                ->toArray();

            foreach ($mentionsQuery as $mention) {
                $mentionData[$mention['idx']] = $mention;
            }
        }

        // 모든 댓글에 관련된 work_view_check 데이터 일괄 조회
        $viewCheckData = [];
        if (!empty($mentionIds)) {
            $commentIds = array_column($commentRows, 'idx');
            if (!empty($commentIds)) {
                $viewCheckQuery = WorkViewCheckModel::query()
                    ->select(['tidx', 'mb_idx', 'reg_date'])
                    ->where('mode', '=', $mode)
                    ->whereIn('tidx', $commentIds)
                    ->whereIn('mb_idx', $mentionIds)
                    ->get()
                    ->toArray();

                foreach ($viewCheckQuery as $viewCheck) {
                    $viewCheckData[$viewCheck['tidx']][$viewCheck['mb_idx']] = $viewCheck['reg_date'];
                }
            }
        }

        // 댓글 데이터에 mention 및 view_check 정보 추가
        foreach ($commentRows as &$comment) {
            $comment['mention'] = [];
            if (!empty($comment['mention_mb'])) {
                foreach (explode('@', $comment['mention_mb']) as $mb_idx) {
                    if (isset($mentionData[$mb_idx])) {
                        $viewCheck = isset($viewCheckData[$comment['idx']][$mb_idx]);
                        $viewCheckDate = $viewCheck ? $viewCheckData[$comment['idx']][$mb_idx] : "";

                        $comment['mention'][] = [
                            'mb_idx' => $mb_idx,
                            'name' => $mentionData[$mb_idx]['ad_name'],
                            'viewCheck' => $viewCheck,
                            'viewCheckDate' => $viewCheckDate,
                        ];
                    }
                }
            }
            $comment['reply_data'] = json_decode($comment['reply_data'] ?? '[]', true);
            $comment['reaction'] = json_decode($comment['reaction'] ?? '[]', true);
        }

        return [
            'title' => [
                'mode' => $_title_mode,
                'name' => $_title_name,
            ],
            'comment' => $commentRows,
        ];
    }


    /**
     * 타이틀 정보 조회 (모드별)
     */
    protected function getTitleInfo(string $mode, $tidx): array
    {
        $modes = [
            'orderSheet' => [
                'model' => OrderSheetModel::class,
                'field' => 'oo_name',
                'title' => '주문서',
            ],
            'log' => [
                'model' => WorkLogModel::class,
                'field' => 'subject',
                'title' => '업무 게시판',
            ],
            'prd' => [
                'model' => ProductModel::class,
                'field' => 'CD_NAME',
                'title' => '상품',
            ],
            'calendar' => [
                'model' => CalendarModel::class,
                'field' => 'subject',
                'title' => '캘린더',
            ],
            'cs' => [
                'model' => CsRequestModel::class,
                'field' => 'order_no',
                'title' => 'C/S',
            ],
        ];

        if (!isset($modes[$mode])) {
            throw new Exception("Invalid mode: {$mode}");
        }

        $modelClass = $modes[$mode]['model'];
        $model = new $modelClass();
        $field = $modes[$mode]['field'];
        $title = $modes[$mode]['title'];

        $data = $model->find($tidx, [$field]);

        return [
            'title_mode' => $title,
            'title_name' => $data[$field] ?? '',
        ];
    }

    /**
     * 멘션 타켓 대상자 조회
     */
    public function getMentionTarget()
    {

        $query = AdminModel::query()
            ->select(['idx', 'ad_nick', 'ad_name', 'ad_image'])
            ->where('is_mention', 'Y')
            ->get()
            ->toArray();

        return $query;
    }

}