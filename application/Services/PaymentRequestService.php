<?php
namespace App\Services;

use Exception;
use App\Models\PaymentRequestModel;
use App\Core\AuthAdmin;

class PaymentRequestService
{

    /**
     * 결제요청 목록 조회
     * 
     * @return array
     */
    public function getPaymentRequestList($criteria)
    {

        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $status = $criteria['status'] ?? '요청';
        $keyword = $criteria['keyword'] ?? '';

        $query = PaymentRequestModel::query()
            ->when($status, function($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('idx', 'desc')
            ->get();

        //$result = $query->toArray();
        $result = $query->paginate($perPage, $page);

        foreach($result['data'] as &$row){
            $row['meta_json'] = json_decode($row['meta_json'], true);
        }
        unset($row);

        return $result;
    }


    /**
     * 결제요청 상세 조회
     * 
     * @param int $idx 결제요청 번호
     * @return array
     */
    public function getPaymentRequestInfo($idx)
    {

        $query = PaymentRequestModel::find($idx)->toArray();
        if( !$query ){
            throw new Exception("결제요청 정보를 찾을 수 없습니다.");
        }
        
        return $query;

    }


    /**
     * 결제요청 저장
     * 
     * @param array $requestData
     * @return array
     */
    public function createPaymentRequest($requestData)
    {

        $category = $requestData['category'] ?? null;
        $kind = $requestData['kind'] ?? null;
        $kind_idx_raw = $requestData['kind_idx'] ?? null;
        $kind_idx = (is_numeric($kind_idx_raw) && (string)$kind_idx_raw !== '') ? (int)$kind_idx_raw : null;
        $currency = $requestData['currency'] ?? 'KRW';
        $amount = str_replace(',', '', (string)($requestData['amount'] ?? ''));
        $foreign_account = $requestData['foreign_account'] ?? null;
        $is_vat = $requestData['is_vat'] ?? 'Y';
        $request_date = $requestData['request_date'] ?? date('Y-m-d');

        $ad_pk = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $ad_name = AuthAdmin::getSession('sess_name');

        $meta_json = [];
        if( $category == '환불' ){
            $godo_order_no = trim((string)($requestData['godo_order_no'] ?? ''));
            if ($godo_order_no === '') {
                throw new Exception("환불일 때 고도몰 주문번호는 필수입니다.");
            }
            $kind = 'godo_refund';
            $kind_idx = null;
            $meta_json['godo_order_no'] = $godo_order_no;
        }

        $meta_json = json_encode($meta_json, JSON_UNESCAPED_UNICODE);

        $inputData = [
            'kind' => $kind,
            'kind_idx' => $kind_idx,
            'category' => $category,
            'currency' => $currency,
            'amount' => $amount,
            'depositor_name' => $requestData['depositor_name'],
            'foreign_account' => $foreign_account,
            'is_vat' => $is_vat,
            'request_date' => $request_date,
            'bank' => $requestData['bank'],
            'bank_account' => $requestData['bank_account'],
            'depositor' => $requestData['depositor'],
            'memo' => $requestData['memo'],
            'meta_json' => $meta_json,
            'ad_pk' => $ad_pk,
            'ad_name' => $ad_name,
        ];

        $result = PaymentRequestModel::insert($inputData);

        return $result;

    }


    /**
     * 결제요청 수정
     * 
     * @param array $requestData
     * @return array
     */
    public function updatePaymentRequest($requestData)
    {
        $idx = $requestData['idx'] ?? null;

        if( empty($idx) ){
            throw new Exception("결제요청 번호가 없습니다.");
        }

        $category = $requestData['category'] ?? null;
        $kind = $requestData['kind'] ?? null;
        $kind_idx_raw = $requestData['kind_idx'] ?? null;
        $kind_idx = (is_numeric($kind_idx_raw) && (string)$kind_idx_raw !== '') ? (int)$kind_idx_raw : null;
        $currency = $requestData['currency'] ?? 'KRW';
        $amount = str_replace(',', '', (string)($requestData['amount'] ?? ''));
        $foreign_account = $requestData['foreign_account'] ?? null;
        $is_vat = $requestData['is_vat'] ?? 'Y';
        $request_date = $requestData['request_date'] ?? date('Y-m-d');

        $status = $requestData['status'] ?? null;
        $approved_ad_pk = 0;
        $approved_ad_name = '';
        $process_date = null;
        $process_memo = '';


        if( $status != '요청' ){
            $approved_ad_pk = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
            $approved_ad_name = AuthAdmin::getSession('sess_name');
            $process_date = $requestData['process_date'] ?? date('Y-m-d');
            $process_memo = $requestData['process_memo'] ?? null;
        }

        $meta_json = [];
        if( $category == '환불' ){
            $godo_order_no = trim((string)($requestData['godo_order_no'] ?? ''));
            if ($godo_order_no === '') {
                throw new Exception("환불일 때 고도몰 주문번호는 필수입니다.");
            }
            $kind = 'godo_refund';
            $kind_idx = null;
            $meta_json['godo_order_no'] = $godo_order_no;
        }

        $meta_json = json_encode($meta_json, JSON_UNESCAPED_UNICODE);        

        $updateData = [
            'kind' => $kind,
            'kind_idx' => $kind_idx,
            'category' => $category,
            'currency' => $currency,
            'amount' => $amount,
            'depositor_name' => $requestData['depositor_name'],
            'foreign_account' => $foreign_account,
            'is_vat' => $is_vat,
            'request_date' => $request_date,
            'status' => $status,
            'bank' => $requestData['bank'],
            'bank_account' => $requestData['bank_account'],
            'depositor' => $requestData['depositor'],
            'memo' => $requestData['memo'],
            'meta_json' => $meta_json,
            'approved_ad_pk' => $approved_ad_pk,
            'approved_ad_name' => $approved_ad_name,
            'process_date' => $process_date,
            'process_memo' => $process_memo ?? '',
        ];

        $result = PaymentRequestModel::update(['idx' => $idx], $updateData);

        return $result;



    }


}