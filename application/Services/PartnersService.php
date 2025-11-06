<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\PartnersModel;

class PartnersService extends BaseClass {

    /**
     * 거래처 목록 조회
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * @return array
     */
    public function getPartnersList($getData, $extraData=null) {

        $payloadData = array_replace((array)$getData, (array)$extraData);

        $page = isset($payloadData['page']) ? $payloadData['page'] : 1;
        $perPage = isset($payloadData['per_page']) ? $payloadData['per_page'] : 100;

        $query = PartnersModel::query();

        if( isset($payloadData['category']) ){
            $query->where('category', $payloadData['category']);
        }

        /*
        $result = $query->orderBy('idx', 'DESC')
            ->paginate($perPage, $page);
        */
        $result = $query->orderBy('idx', 'DESC')
            ->get()
            ->toArray();

        return $result;

    }

    /**
     * 파트너 공급처 셀렉트바 조회
     * @param array|null $extraData 추가 파라미터
     * └ $extraData {string} $extraData['showMode'] : WHOLE_SUPPLIER - 성인용품도매(도매공급사)
     * └ $extraData {bool} $extraData['listActive'] : 목록 화면에서 사용 여부
     * @return array
     */
    public function getPartnersForSelect($extraData=null) {
        
        $query = PartnersModel::select('idx', 'name')
            ->orderBy('name', 'asc');

        if( $extraData['showMode'] == 'WHOLE_SUPPLIER' ){
            $query->where('category', '성인용품공급');
        }

        /*
        if( $extraData['listActive'] ){
            $query->where('BD_LIST_ACTIVE', 'Y');
        }
        */

        $partnerList = $query->get()
            ->toArray();

        return $partnerList;

    }


}