<?php
namespace App\Services;

use App\Models\CsRequestModel;
use App\Auth\AdminAuth;
use App\Services\AdminServices;
use App\Models\AdminModel;
use App\Utils\TelegramUtils;

class CsRequestService
{

    /**
     * C/S 목록 조회
     * 
     * @return array
     */
    public function getCsRequestList($criteria)
    {
        
        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $cs_status = $criteria['cs_status'] ?? '요청+처리중';
        $keyword = $criteria['keyword'] ?? '';

        $query = CsRequestModel::query()
            ->when($cs_status, function($query) use ($cs_status) {
                if( $cs_status == '요청+처리중' ){
                    $query->where('cs_status', '요청');
                    $query->orWhere('cs_status', '처리중');
                }else{
                    $query->where('cs_status', $cs_status);
                }
            })
            ->when($keyword, function($query) use ($keyword) {
                $query->where('mem_id', 'like', '%'.$keyword.'%');
                $query->orWhere('mem_name', 'like', '%'.$keyword.'%');
                $query->orWhere('mem_phone', 'like', '%'.$keyword.'%');
                $query->orWhere('receiver_name', 'like', '%'.$keyword.'%');
                $query->orWhere('receiver_phone', 'like', '%'.$keyword.'%');
                $query->orWhere('order_no', 'like', '%'.$keyword.'%');
            })
            ->orderBy('idx', 'desc')
            ->get();

        //$result = $query->toArray();
        $result = $query->paginate($perPage, $page);

        $adminMap = AdminModel::query()
            ->select(['idx', 'ad_id', 'ad_name', 'ad_nick'])
            ->get()
            ->keyBy('idx')
            ->toArray();
         
        foreach($result['data'] as &$row){
            $row['reg_name'] = $adminMap[$row['reg_pk']]['ad_name'] ?? '';
        }
        unset($row);

        return $result;
    }


    /**
     * C/S 상태별 카운트 조회
     * 
     * @return array
     */
    public function getCsRequestCount()
    {
        $query = CsRequestModel::query()
            ->select(['cs_status', 'COUNT(*) as count'])
            ->where('cs_status', '!=', '처리완료')
            ->groupBy('cs_status')
            ->get()
            ->toArray();

        $result = $query;

        return $result;
    }


    /**
     * C/S 상세 조회
     * 
     * @param int $idx C/S 고유번호
     * @return array
     */
    public function getCsRequestDetail($idx)
    {
        $query = CsRequestModel::find($idx)
            ->toArray();

        return $query;

    }


    /**
     * C/S 처리 요청
     * 
     * @param array $requestData 요청 데이터
     * @return array
     */
    public function createCsRequest($data)
    {

        $admin = AdminAuth::user();

        // 필수 값 기본 가드
        $orderNo   = $data['orderNo'] ?? null;
        $orderDate = $data['orderDate'] ?? null;
        $paymentDt = $data['paymentDt'] ?? null;
        $memNo = $data['memNo'] ?? null;
        $memId = trim((string)($data['memId'] ?? ''));
        if ($memId === '') {
            $memId = trim((string)($memNo ?? ''));
        }
        $memName = $data['memName'] ?? null;
        $memPhone = $data['memPhone'] ?? null;
        $receiverName = $data['receiverName'] ?? null;
        $receiverPhone = $data['receiverPhone'] ?? null;
        $groupNm = $data['groupNm'] ?? null;
        $csBody = $data['csBody'] ?? null;
        $category = $data['category'] ?? '출고준비';
        $actionDate = $data['actionDate'] ?? null;
        if ($category !== '출고지정일') {
            $actionDate = null;
        }

        // 빈 필수값이 있으면 예외 반환
        /*
        if (!$orderNo || !$orderDate || !$memNo || !$memId || !$groupNm) {
            throw new \InvalidArgumentException('필수 값이 누락되었습니다.');
        }
        */

        $regId = $admin["sess_id"] ?? null;
        $regPk = $admin["sess_idx"] ?? null;

        $adminServices = new AdminServices();
        $adminData = $adminServices->getAdmin(['idx' => $regPk]);

        $inputData = [
            'category' => $category,
            'order_no' => $orderNo,
            'order_date' => $orderDate,
            'payment_date' => $paymentDt,
            'action_date' => $actionDate,
            'mem_no' => $memNo,
            'mem_id' => $memId,
            'mem_name' => $memName,
            'mem_phone' => $memPhone,
            'receiver_name' => $receiverName,
            'receiver_phone' => $receiverPhone,
            'group_nm' => $groupNm,
            'cs_status' => '요청',
            'cs_body' => $csBody,
            'reg_id' => $regId,
            'reg_pk' => $regPk,
        ];

        $csRequest = CsRequestModel::insert($inputData);

        $telegram_chat_id = config('admin.telegram_chat_id');
        $chat_room_id = $telegram_chat_id['chat_room_ids']['cs']['chat_id'];

        //dd($chat_room_id);

        $telegram = new TelegramUtils();

        $message = "🟣 ".$category." C/S 요청\n";
        $message .= "주문번호 : " .  $orderNo . " \n";
        $message .= "주문일시 : " . date('Y-m-d H:i:s', strtotime($orderDate)) . "\n";
        $message .= "----------------------------\n";
        $message .= $csBody . "\n";
        $message .= "----------------------------\n";
        $message .= "요청일 : " . date('Y-m-d H:i:s') . "\n";
        $message .= "등록자 : " . $adminData['ad_name'] . "\n";
        $message .= "----------------------------\n";

        $telegramResult = $telegram->sendMessage($chat_room_id, $message, 'HTML');
        //dd($telegramResult);

        return $csRequest;

    }


    /**
     * C/S 상태변경
     * 
     * @param array $requestData 요청 데이터
     * @return array
     */
    public function updateCsStatus($data)
    {
        $query = CsRequestModel::find($data['idx']);
        $category = $data['category'] ?? ($query->category ?? null);
        $actionDate = $data['action_date'] ?? null;
        if ($category !== '출고지정일') {
            $actionDate = null;
        }

        $admin = AdminAuth::user();

        $processor_id = $admin["sess_id"] ?? null;
        $processor_pk = $admin["sess_idx"] ?? null;
        $processor_name = $admin["sess_name"] ?? null;

        $update_data = [
            'category' => $category,
            'cs_status' => $data['cs_status'],
            'action_date' => $actionDate,
            'processor_id' => $processor_id,
            'processor_pk' => $processor_pk,
            'processor_name' => $processor_name,
            'process_action' => $data['process_action'] ?? null,
            'processor_date' => date('Y-m-d H:i:s'),
        ];

        $csRequest = CsRequestModel::update(['idx' => $data['idx']], $update_data);

        return $csRequest;

    }
}   