<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\AuthAdmin;
use App\Core\BaseClass;
use App\Classes\Request;

use App\Services\CommentService;

use App\Models\AdminModel;
use App\Models\CommentModel;
use App\Models\OrderSheetModel;
use App\Models\WorkViewCheckModel;
use App\Models\WorkLogModel;
use App\Models\ProductModel;
use App\Models\CalendarModel;
use App\Models\CsRequestModel;
use App\Utils\Pagination;
use App\Utils\TelegramUtils;

class CommentController extends BaseClass
{

	public function getTitleInfo($mode, $tidx)
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
			throw new \Exception("Invalid mode: {$mode}");
		}

		// 동적으로 모델 초기화
		$modelClass = $modes[$mode]['model'];
		$model = new $modelClass();
		$field = $modes[$mode]['field'];
		$title = $modes[$mode]['title'];

		// 데이터 조회
		$data = $model->find($tidx, [$field]);

		return [
			'title_mode' => $title,
			'title_name' => $data[$field] ?? '',
		];
	}


	/**
	 * 코멘트 메인 - 화면
	 */
	public function commentMainIndex()
	{

		$postData = $this->postData;

		$result = [];

		//달력 댓글경우 데이터가 없을경우 생성
		if (isset($postData['mode']) && $postData['mode'] == "calendar" && empty($postData['idx']) && !empty($postData['dayCode'])) {

			$_subject = $postData['dayCode'] . " 캘린더";
			$_date_s = $postData['dayCode'] . " 00:00:00";
			$_date_e	 = $postData['dayCode'] . " 23:59:59";

			$CalendarModel = new CalendarModel();

			$isCalendar = CalendarModel::query()
				->where('mode', '=', 'comment')
				->where('date_s', '=', $_date_s)
				->exists();

			//return $isCalendar;

			//자식이 존재할 경우
			if ($isCalendar) {

				$CalendarInsertResult = CalendarModel::query()
					->select('idx')
					->where('mode', '=', 'comment')
					->where('date_s', '=', $_date_s)
					->first();

				$result['CalendarInsert']['idx'] = $CalendarInsertResult['idx'];
			} else {

				$insertData = [
					'subject' => $_subject,
					'mode' => 'comment',
					'date_s' => $_date_s,
					'date_e' => $_date_e,
				];

				$CalendarInsertResult = $CalendarModel->insert($insertData);

				$result['CalendarInsert']['idx'] = $CalendarInsertResult['insert_id'];
			}
		}

		return $result;
	}


	/**
	 * 코멘트 쳇 리스트 - 구버전
	 */
	public function commentListIndex()
	{

		$ad_idx = AuthAdmin::getSession('sess_idx');
		$_target_mb_text = "@" . $ad_idx;

		$sql = "
			SELECT
				A.idx,
				A.mode,
				A.tidx,
				A.mb_idx,
				A.comment,
				A.reg_date,
				C.ad_name,
				C.ad_image
			FROM work_comment A
			LEFT JOIN admin C
				ON C.idx = A.mb_idx
			LEFT JOIN work_view_check B
				ON B.mode = A.mode
				AND B.tidx = A.idx
				AND B.mb_idx = :ad_idx
			WHERE A.mention_mb LIKE CONCAT('%', :target_mb_text, '%')
				AND B.tidx IS NULL
			ORDER BY A.idx DESC
			LIMIT 50;
		";


		// 바인딩 값 설정
		$params = [
			'target_mb_text' => "@" . $ad_idx,
			'ad_idx' => $ad_idx,
		];


		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);


		// 추가 정보 삽입 및 가공
		foreach ($results as &$list) {

			$TitleInfo = $this->getTitleInfo($list['mode'], $list['tidx']);
			$list['target']['mode_text'] = $TitleInfo['title_mode'];
			$list['target']['subject'] = $TitleInfo['title_name'];

		}

		return [
			'list' => $results,
		];
	}


	/**
	 * 코멘트 쳇 리스트 
	 */
	public function commentList(Request $request)
	{

		$requestData = $request->all();
		$mode = $requestData['mode'] ?? 'unchecked';
		$page = $requestData['page'] ?? 1;
		$perPage = $requestData['per_page'] ?? 50;
		$ad_idx = AuthAdmin::getSession('sess_idx');
		$payload = [
			'mode' => $mode,
			'page' => $page,
			'per_page' => $perPage,
			'ad_idx' => $ad_idx,
		];
		$commentService = new CommentService();
		$commentList = $commentService->getCommentList($payload);

		//dd($payload);

		$data = [
			'commentList' => $commentList['list'],
			'pagination' => $commentList['pagination'],
		];

		return view('admin.comment.comment_list', $data);

	}


	/**
	 * @deprecated 2026-01-22
	 * 
	 * 코멘트 쳇 - 화면
	 */
	public function commentChatIndex()
	{

		$postData = $this->postData;

		$_mode = $postData['mode'];
		$_tidx = $postData['tidx'];

		$TitleInfo = $this->getTitleInfo($_mode, $_tidx);
		$_title_mode = $TitleInfo['title_mode'];
		$_title_name = $TitleInfo['title_name'];

		/*
		//주문서 댓글
		if( $_mode == "orderSheet" ){

			$orderSheetModel = new orderSheetModel();
			$orderSheet = $orderSheetModel->find( $_tidx, ['oo_name']);

			$_title_mode = "주문서";
			$_title_name = $orderSheet['oo_name'];

		// 업무게시판 댓글
		} elseif ( $_mode == "log") {

			$workLogModel = new WorkLogModel();
			$workLog = $workLogModel->find( $_tidx, ['subject']);

			$_title_mode = "업무 게시판";
			$_title_name =  $workLog['subject'];

		// 상품 댓글
		} elseif ( $_mode == "prd") {

			$ProductModel = new ProductModel();
			$product = $ProductModel->find( $_tidx, ['CD_NAME']);

			$_title_mode = "상품";
			$_title_name =  $product['CD_NAME'];

		// 달력 댓글
		} elseif ( $_mode == "calendar") {

			$CalendarModel = new CalendarModel();
			$Calendar = $CalendarModel->find( $_tidx, ['subject']);

			$_title_mode = "캘린더";
			$_title_name =  $Calendar['subject'];

		}
		*/


		// 주요 댓글 데이터 조회
		$query = $this->queryBuilder
			->table('work_comment AS A')
			->select([
				'A.*',
				'B.ad_nick',
				'B.ad_image',
			])
			->join('admin AS B', 'B.idx', '=', 'A.mb_idx', 'LEFT')
			->where('A.mode', '=', $_mode)
			->where('A.kind', '=', 'S')
			->where('A.tidx', '=', $_tidx)
			->orderBy('A.idx', 'ASC')
			->get()
			->toArray();

		// 모든 mention_mb ID 수집
		$mentionIds = [];
		foreach ($query as $comment) {
			if (!empty($comment['mention_mb'])) {
				$mentionIds = array_merge($mentionIds, explode('@', $comment['mention_mb']));
			}
		}

		// mentionIds 정리 및 비어있는 값 제거
		$mentionIds = array_filter(array_unique($mentionIds), function ($value) {
			return $value !== ""; // 빈 값 제거
		});

		// mention_mb 데이터 일괄 조회
		$mentionData = [];
		if (!empty($mentionIds)) {
			// mentionIds가 있을 경우에만 쿼리 실행
			$mentionsQuery = $this->queryBuilder
				->table('admin')
				->select(['idx', 'ad_nick', 'ad_name', 'ad_image'])
				->whereIn('idx', $mentionIds)
				->get()
				->toArray();

			foreach ($mentionsQuery as $mention) {
				$mentionData[$mention['idx']] = $mention;
			}
		} else {
			// mentionIds가 비어 있을 때 기본 처리
			$mentionData = [];
		}

		// 모든 댓글에 관련된 work_view_check 데이터 일괄 조회
		$viewCheckData = [];
		if (!empty($query) && !empty($mentionIds)) {
			$commentIds = array_column($query, 'idx');
			$viewCheckQuery = $this->queryBuilder
				->table('work_view_check')
				->select(['tidx', 'mb_idx', 'reg_date'])
				->where('mode', '=', $_mode)
				->whereIn('tidx', $commentIds)
				->whereIn('mb_idx', $mentionIds)
				->get()
				->toArray();

			foreach ($viewCheckQuery as $viewCheck) {
				$viewCheckData[$viewCheck['tidx']][$viewCheck['mb_idx']] = $viewCheck['reg_date'];
			}
		}

		// 댓글 데이터에 mention 및 view_check 정보 추가
		foreach ($query as &$comment) {
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
			'comment' => $query,
			/*
			'test' => [
				'viewCheckQuery' => $viewCheckQuery,
				'mentionIds' => $mentionIds,
			],
			*/
		];
	}


	/**
	 * 코멘트 챗화면
	 */
	public function commentChat(Request $request)
	{

		try{

			$requestData = $request->all();

			$payload = [
				'mode' => $requestData['mode'],
				'tidx' => $requestData['tidx'],
			];

			$commentService = new CommentService();
			$commentList = $commentService->getCommentChat($payload);

			return view('admin.comment.comment_chat', $commentList);

		} catch (Throwable $e) {
			return view('admin.errors.404', [
				'message' => $e->getMessage(),
			])->response(404);
		}
	}

	/**
	 * 코멘트 등록
	 */
	public function createComment()
	{

		try {

			$postData = $this->postData;
			$action_time = date('Y-m-d H:i:s');

			$telegram = new TelegramUtils();
			$AdminModel = new AdminModel();

			$TitleInfo = $this->getTitleInfo($postData['mode'], $postData['tidx']);
			$_title_mode = $TitleInfo['title_mode'];
			$_title_name = $TitleInfo['title_name'];

			$message = "🟣 멘션 |";
			$message .= "<b>[" . $postData['tidx'] . "] " . $_title_mode . " (" . $_title_name . ")</b>";


			$_is_reply = 0;

			//만약 답장일때
			if (!empty($postData['reply_idx'])) {

				$_is_reply = 1;

				$maxLength = 50;

				// 특수문자 및 줄 바꿈, 탭 문자 제거
				$replySummary = trim($postData['reply_summary']);
				$replySummary = preg_replace('/[\r\n\t]+/', ' ', $replySummary);

				// 내용 자르기
				$replySummary = mb_substr($replySummary, 0, $maxLength, 'UTF-8');

				$_reply_data = json_encode([
					"idx" => $postData['reply_idx'],
					"name" =>  $postData['reply_name'],
					"summary" =>  $replySummary,
				], JSON_UNESCAPED_UNICODE);

				$WorkViewCheckModel = new WorkViewCheckModel();

				//부모글을 내가 체크했나 안했나 확인해보자.
				$isViewCheck = WorkViewCheckModel::query()
					->where('mode', '=', $postData['mode'])
					->where('tidx', '=', $postData['reply_idx'])
					->where('mb_idx', '=', AuthAdmin::getSession('sess_idx'))
					->exists();

				//부모글을 체크 안했다면 바로 체크해버린다.
				if (!$isViewCheck) {

					$insertData = [
						'mode' => $postData['mode'],
						'tidx' => $postData['reply_idx'],
						'mb_idx' => AuthAdmin::getSession('sess_idx'),
						'reg' => json_encode(AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE) ?? null,
						'reg_date' => $action_time,
					];

					$WorkViewCheckInsertResult = $WorkViewCheckModel->insert($insertData);
				}

				/*
				$message = "<b><u>".$_title_mode." (".$_title_name.") 멘션되었습니다.</u></b>";
				위에 쓴 메세지가 무시되고 새롭게 다시 쓴다.
				*/

				$message = "🟢 답변 | ";
				$message .= "<b>[" . $postData['tidx'] . "] " . $_title_mode . " (" . $_title_name . ") </b>\n";
				$message .= "---------------------------------------------------\n";
				$message .= "(" . $postData['reply_name'] . ")\n";
				$message .= "" . $replySummary . "\n";
				$message .= "---------------------------------------------------";
			}

			$message .= "\n\n" . $postData['comment'] . "";
			$message .= "\n\n( " . AuthAdmin::getSession('sess_name') . " :: " . $action_time . ")";

			//만약 답장일때
			if (!empty($postData['reply_idx']) && !empty($postData['reply_mb_idx'])) {
				$_target_mb_idx = [$postData['reply_mb_idx']];
			} else {
				$_target_mb_idx = $postData['target_mb_idx'];
			}

			$_mention_mb = "";
			foreach ($_target_mb_idx as $mb_idx) {
				$_mention_mb .= "@" . $mb_idx;

				$ad = $AdminModel->find($mb_idx, ['ad_telegram_token']);

				if (!empty($ad) && !empty($ad['ad_telegram_token'])) {
					$chatId = $ad['ad_telegram_token'];
					//$message .= "<a href='https://example.com'>[멘션확인 처리하기]</a>";
					$telegramResult = $telegram->sendMessage($chatId, $message, 'HTML');
				}
			} //foreach END

			// Mention 문자열 생성
			// $_mention_mb = "@" . implode("@", $postData['target_mb_idx']);

			$maxIdx = CommentModel::query()->getMax('idx');
			$nextIdx = $maxIdx ? ((int)$maxIdx + 1) : 1;

			$insertData = [
				'idx' => $nextIdx,
				'mode' => $postData['mode'] ?? null,
				'kind' => 'S',
				'tidx' => $postData['tidx'] ?? null,
				'comment' => $postData['comment'] ?? null,
				'mb_idx' => AuthAdmin::getSession('sess_idx'),
				'reg' => json_encode(AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE) ?? null,
				'mention_mb' => $_mention_mb ?? null,
				'is_reply' => $_is_reply,
				'reply_data' => $_reply_data ?? null,
				'grpno' => '0',
				'grpord' => '0',
				'depth' => '0',
				'ancestor' => '0',
			];

			$CommentModel = new CommentModel();
			$CommentInsertResult = $CommentModel->insert($insertData);

			//주문서 댓글
			if ( $postData['mode'] == "orderSheet" ) {

				$this->queryBuilder
					->table('ona_order')
					->where('oo_idx', '=', $postData['tidx'])
					->whereNull('comment_count')
					->update(['comment_count' => 0]);
				$incrementResults = $this->queryBuilder
					->table('ona_order')
					->where('oo_idx', '=', $postData['tidx'])
					->increment('comment_count', 1);

			// 업무게시판 댓글
			} elseif ( $postData['mode'] == "log" ) {

				$workLogModel = new WorkLogModel();
				WorkLogModel::query()
					->where('idx', '=', $postData['tidx'])
					->whereNull('cmt_s_count')
					->update(['cmt_s_count' => 0]);
					
				$incrementResults = WorkLogModel::query()
					->where('idx', '=', $postData['tidx'])
					->increment('cmt_s_count', 1);

			//상품 댓글
			} elseif ( $postData['mode'] == "prd" ) {

				$this->queryBuilder
					->table('COMPARISON_DB')
					->where('CD_IDX', '=', $postData['tidx'])
					->whereNull('comment_count')
					->update(['comment_count' => 0]);
				$incrementResults = $this->queryBuilder
					->table('COMPARISON_DB')
					->where('CD_IDX', '=', $postData['tidx'])
					->increment('comment_count', 1);

			// 달력 댓글
			} elseif ( $postData['mode'] == "calendar" ) {

				$CalendarModel = new CalendarModel();
				CalendarModel::query()
					->where('idx', '=', $postData['tidx'])
					->whereNull('comment_count')
					->update(['comment_count' => 0]);
				$incrementResults = CalendarModel::query()
					->where('idx', '=', $postData['tidx'])
					->increment('comment_count', 1);

			// C/S
			} elseif ( $postData['mode'] == "cs" ) {

				$CsRequestModel = new CsRequestModel();
				CsRequestModel::query()
					->where('idx', '=', $postData['tidx'])
					->whereNull('comment_count')
					->update(['comment_count' => 0]);
				$incrementResults = CsRequestModel::query()
					->where('idx', '=', $postData['tidx'])
					->increment('comment_count', 1);					

			}

			return [
				'status' => 'success',
				'message' => "등록완료",
				'mode' => $postData['mode'],
				'tidx' => $postData['tidx']
			];

		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}

	}


	/**
	 * 코멘트 리액션
	 */
	public function commentReaction()
	{

		try {

			$postData = $this->postData;
			$action_time = date('Y-m-d H:i:s');

			$telegram = new TelegramUtils();
			$AdminModel = new AdminModel();

			$CommentModel = new CommentModel();
			//$Comment = $CommentModel->find($postData['idx'], ['mode','tidx','comment','mb_idx','reaction']);
			$Comment = $CommentModel->find($postData['idx'], []);

			$TitleInfo = $this->getTitleInfo($Comment['mode'], $Comment['tidx']);
			$_title_mode = $TitleInfo['title_mode'];
			$_title_name = $TitleInfo['title_name'];

			// 기존 반응 데이터 디코딩
			$_old_reaction = json_decode($Comment['reaction'] ?? '[]', true);

			if (!is_array($_old_reaction)) {
				$_old_reaction = [];
			}

			// 새로운 반응 데이터
			$_new_reaction = [
				"mb_idx" => AuthAdmin::getSession('sess_idx'),
				"mb_name" => AuthAdmin::getSession('sess_name'),
				"mode" => $postData['reaction_mode'],
				"created_at" => $action_time,
			];

			// 중복 여부 확인
			$isDuplicate = false;
			foreach ($_old_reaction as $reaction) {
				if ($reaction['mb_idx'] === $_new_reaction['mb_idx'] && $reaction['mode'] === $_new_reaction['mode']) {
					$isDuplicate = true;
					break;
				}
			}

			// 중복이 없으면 업데이트
			if (!$isDuplicate) {
				$_old_reaction[] = $_new_reaction;

				// 업데이트 처리
				$CommentModel->update($postData['idx'], [
					'reaction' => json_encode($_old_reaction, JSON_UNESCAPED_UNICODE),
				]);

				$ad = $AdminModel->find($Comment['mb_idx'], ['ad_telegram_token']);
				$chatId = $ad['ad_telegram_token'];

				$_reaction_icon['Good'] = "👍";
				$_reaction_icon['Heart'] = "❤️";
				$_reaction_icon['Clapping'] = "👏";
				$_reaction_icon['Check'] = "✔️";

				$message = $_reaction_icon[$postData['reaction_mode']] . " 리액션 | ";
				$message .= "<b>[" . $postData['idx'] . "] " . $_title_mode . " (" . $_title_name . ") </b>\n";
				$message .= "---------------------------------------------------\n";
				$message .= "" . $Comment['comment'] . "\n";
				$message .= "---------------------------------------------------";
				$message .= "\n\n( " . AuthAdmin::getSession('sess_name') . " :: " . $action_time . ")\n";
				$message .= $_reaction_icon[$postData['reaction_mode']] . " 리액션 했습니다.";

				$telegramResult = $telegram->sendMessage($chatId, $message, 'HTML');
			}

			$WorkViewCheckModel = new WorkViewCheckModel();

			//부모글을 내가 체크했나 안했나 확인해보자.
			$isViewCheck = WorkViewCheckModel::query()
				->where('mode', '=', $postData['mode'])
				->where('tidx', '=', $postData['idx'])
				->where('mb_idx', '=', AuthAdmin::getSession('sess_idx'))
				->exists();

			//부모글을 체크 안했다면 바로 체크해버린다.
			if (!$isViewCheck) {

				$insertData = [
					'mode' => $postData['mode'],
					'tidx' => $postData['idx'],
					'mb_idx' => AuthAdmin::getSession('sess_idx'),
					'reg' => json_encode(AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE) ?? null,
					'reg_date' => $action_time,
				];

				$WorkViewCheckInsertResult = $WorkViewCheckModel->insert($insertData);
			}

			return [
				'status' => 'success',
				'message' => "등록완료"
			];
		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}
	}



	/**
	 * 코멘트 뷰체크
	 */
	public function commentViewCheck()
	{

		$postData = $this->postData;
		$action_time = date('Y-m-d H:i:s');
		$return_time = date('y.m.d H:i', strtotime($action_time));

		$insertData = [
			'mode' => $postData['mode'] ?? null,
			'tidx' => $postData['tidx'] ?? null,
			'mb_idx' => AuthAdmin::getSession('sess_idx'),
			'reg' => json_encode(AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE) ?? null,
			'reg_date' => $action_time,
		];

		$WorkViewCheckModel = new WorkViewCheckModel();
		$WorkViewCheckInsertResult = $WorkViewCheckModel->insert($insertData);

		return [
			'status' => 'success',
			'return_mb_idx' => AuthAdmin::getSession('sess_idx'),
			'return_time' => $return_time,
			'message' => "처리완료"
		];
	}

	/**
	 * 코멘트 뷰체크 전부 확인
	 */
	public function commentViewCheckAll()
	{

		$postData = $this->postData;

		$action_time = date('Y-m-d H:i:s');

		$ad_idx = AuthAdmin::getSession('sess_idx');
		$_target_mb_text = "@" . $ad_idx;

		// SQL 쿼리 작성
		$sql = "
			SELECT A.mode, A.idx
			FROM work_comment AS A
			LEFT JOIN work_view_check AS B 
				ON B.tidx = A.idx 
				AND B.mode = A.mode
			   AND B.mb_idx = A.mb_idx
			WHERE A.mode = :mode
				AND A.tidx = :tidx
				AND INSTR(A.mention_mb, :target_mb_text) > 0
				AND B.idx IS NULL
			ORDER BY A.idx DESC;
		";

		// 바인딩 값 설정
		$params = [
			'mode' => $postData['mode'],
			'tidx' => $postData['tidx'],
			'target_mb_text' => $_target_mb_text,
		];

		$stmt = $this->db->prepare($sql);
		$stmt->execute($params);
		$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		$_reg = json_encode(AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE);

		foreach ($results as $val) {

			$insertData[] = [
				'mode' => $val['mode'],
				'tidx' => $val['idx'],
				'mb_idx' => $ad_idx,
				'reg' =>  $_reg,
				'reg_date' => $action_time,
			];
		}

		$WorkViewCheckModel = new WorkViewCheckModel();
		$WorkViewCheckInsertResult = $WorkViewCheckModel->insert($insertData);

		return [
			'status' => 'success',
			'message' => "처리완료"
		];
	}
}
