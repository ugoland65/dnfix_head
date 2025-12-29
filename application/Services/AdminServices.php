<?php

namespace App\Services;

use App\Models\AdminModel;

class AdminServices
{
    
    /**
     * 직원 목록 조회
     * 
     * @param array $payload 파라미터
     * @return array 직원 목록 데이터
     */
    public function getAdminList($criteria)
    {

        $ad_work_status = $criteria['ad_work_status'] ?? null;

        $query = AdminModel::query();

        if( $ad_work_status ){
            $query->where('ad_work_status', $ad_work_status);
        }

        $result = $query->get()->toArray();

        return $result;

    }

}