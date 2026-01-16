<?php
namespace App\Services;

use App\Models\CsRequestModel;
use App\Auth\AdminAuth;

class CsRequestService
{

    /**
     * C/S 목록 조회
     * 
     * @return array
     */
    public function getCsRequestList()
    {
        
        $query = CsRequestModel::query();
        $query->orderBy('order_date', 'desc');
        $result = $query->get()->toArray();
        return $result;

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
        $orderNo   = $data['orderNo']   ?? '';
        $orderDate = $data['orderDate'] ?? '';
        $memNo     = $data['memNo']     ?? '';
        $memId     = $data['memId']     ?? '';
        $groupNm   = $data['groupNm']   ?? '';
        $csBody    = $data['csBody']    ?? '';

        // 빈 필수값이 있으면 예외 반환
        if (!$orderNo || !$orderDate || !$memNo || !$memId || !$groupNm) {
            throw new \InvalidArgumentException('필수 값이 누락되었습니다.');
        }

        $regId = $admin["sess_id"] ?? null;
        $regPk = $admin["sess_idx"] ?? null;

        $inputData = [
            'order_no' => $orderNo,
            'order_date' => $orderDate,
            'mem_no' => $memNo,
            'mem_id' => $memId,
            'group_nm' => $groupNm,
            'cs_status' => '요청',
            'cs_body' => $csBody,
            'reg_id' => $regId,
            'reg_pk' => $regPk,
        ];

        $csRequest = CsRequestModel::insert($inputData);

        return $csRequest;

    }
}   