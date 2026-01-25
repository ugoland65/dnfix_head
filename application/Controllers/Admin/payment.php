<?php

namespace App\Controllers\Admin;

use App\Core\AuthAdmin;
use App\Core\BaseClass;
use App\Models\PaymentModel;
use App\Models\OrderSheetModel;
use App\Models\BasecodeModel;
use App\Utils\Pagination;
use App\Utils\TelegramUtils;

class payment extends BaseClass {

	/**
	 * 결제/입금 관리 - 화면
	 */
    public function paymentIndex() {

		$getData = $this->requestHandler->getAll(); // GET 데이터 받기

		$page = isset($getData['page']) ? $getData['page'] : 1;
		$perPage = isset($getData['per_page']) ? $getData['per_page'] : 50;

		// 기본 조건
		$payment = $this->queryBuilder
			->table('v2_payment AS A')
			->select([
				'A.*',
				'B.ad_name',
			])
			->join('admin AS B', 'B.idx', '=', 'A.write_idx','LEFT');

		$paymentResults = $payment
			->orderBy('A.idx', 'DESC')
			->paginate($perPage, $page);

		// 추가정보 삽입 & 가공
		foreach ( $paymentResults['data'] as &$payment ) {
			$payment['reg'] = json_decode($payment['reg'] ?? '[]', true);
		}

		$pagination = new Pagination($paymentResults['total'], $paymentResults['per_page'], $paymentResults['current_page'], 10);

		$baseUrl = '';
		$renderLinks = $pagination->renderLinks($baseUrl);

		$results = [
			'status' => 'success',
			'payment' => $paymentResults['data'],
			'paga_nation' => $renderLinks,
			'total' => $paymentResults['total'],
			'per_page' => $paymentResults['per_page'], // 페이지당  수
			'current_page' => $paymentResults['current_page'], // 현재 페이지
			'last_page' => $paymentResults['last_page'], // 전체 페이지 수
		];

		return $results;

	}


    //결제요청 INDEX
    public function paymentRegIndex() {
		
		try{

			$postData = $this->postData;

			$idx = $postData['idx'] ?? null;
			$mode = $postData['mode'];

			$target['mode'] = $mode;

			//주문서 결제요청일경우
			if( $mode == "orderSheet" ){

				$target['mode_text'] = "주문서 결제요청";

				$OrderSheetModel = new OrderSheetModel();
				$orderSheet = $OrderSheetModel->find($idx, ['oo_name']);

				$target['name'] = $orderSheet['oo_name'];
				$target['target_idx'] = $idx;
				
			//결제/입금 관리 페이지에서 결제요청일경우
			}elseif( $mode == "payment" ){

				$BasecodeModel = new BasecodeModel();
				$kind_result = BasecodeModel::query()
					->select(['idx', 'name'])
					->where('cate', '=', 'Payment')
					->orderBy('sort_order', 'ASC')
					->get();

				$target['kind'] = $kind_result;

			}

			return [
				'target' => $target
			];

		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}

    }


    //결제요청 등록
    public function createPayment() {

		try{

			$postData = $this->postData;

			$_price = str_replace(',','', $postData['price']);

			if( $postData['mode'] == "payment" ){

				$BasecodeModel = new BasecodeModel();
				$basecode_result = BasecodeModel::query()
					->select(['name'])
					->find($postData['target_idx']);

				$mode_text = $basecode_result['name'];

			}else{
				$_target_idx = $postData['target_idx'] ?? null;
			}

			$insertData = [
				'kind' => 'minus',
				'mode' => $postData['mode'] ?? null,
				'mode_text' => $mode_text ?? null,
				'target_idx' => $_target_idx,
				'write_idx' => AuthAdmin::getSession('sess_idx'),
				'price' => $_price ?? 0,
				'desired_date' => $postData['desired_date'] ?? null,
				'state' => $postData['state'] ?? 0,
				'bank' => $postData['bank'] ?? null,
				'memo' => $postData['memo'] ?? null,
				'reg' => json_encode([
					'step1' => AuthAdmin::getConnectionInfo()
				], JSON_UNESCAPED_UNICODE) ?? null,
			];

			$PaymentModel = new PaymentModel();
			$paymentInsertResult = $PaymentModel->insert($insertData);

			
			/* 텔레글램 메세지 보내기 */
			//$telegramResult = TelegramUtils::sendMessage($chatId, $message);

			// 사용 예제
			/*
			try {
				$telegram = new TelegramUtils();
				$chatId = "-4701687399";
				$message = "결제요청 등록";

				$result = $telegram->sendMessage($chatId, $message);
				print_r($result);
			} catch (\Exception $e) {
				echo 'Error: ' . $e->getMessage();
			}
			*/

			$telegram = new TelegramUtils();
			$chatId = "-1002314485608";
			$message = "==<b>결제요청 등록<b>==\n{$postData['memo']}";

			$telegramResult = $telegram->sendMessage($chatId, $message);

			return [
				'status' => 'success',
				'message' => "예약이 확인되었습니다."
			];

		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}

	}

    //결제요청 등록
    public function paymentStatusModify() {

		try{

			$postData = $this->postData;

			$_idx = $postData['idx'];
			
			if( empty($postData['state']) ){
				$_state = 3;
			}else{
				$_state = $postData['state'];
			}

			$_step = $_state + 1;

			$PaymentModel = new PaymentModel();

			$payment_result = PaymentModel::query()
				->select(['reg'])
				->find($_idx);

			$payment_reg = json_decode($payment_result['reg'], true);

			$stepKey = 'step' . $_step;
			$payment_reg[$stepKey] = AuthAdmin::getConnectionInfo();

            $updateData = [
                'state' => $_state,
                'reg' => json_encode($payment_reg, JSON_UNESCAPED_UNICODE) ?? null,
            ];

			$paymentInsertResult = $PaymentModel->update($_idx, $updateData);

			return [
				'status' => 'success',
				'message' => "예약이 확인되었습니다."
			];

		} catch (\Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}

	}


    //결제요청 등록
    public function test() {

			return AuthAdmin::getConnectionInfo();

	}


}