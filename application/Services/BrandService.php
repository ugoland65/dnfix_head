<?php

namespace App\Services;

use App\Models\BrandModel;

class BrandService {

    /**
     * 브랜드 셀렉트바를 위한 조회
     * @param array|null $extraData 추가 파라미터
     * └ $extraData {bool} $extraData['listActive'] : 목록 화면에서 사용 여부
     * @return array
     */
    public function getBrandForSelect($extraData=null) {
        
        $query = BrandModel::select('BD_IDX', 'BD_NAME')
            ->orderBy('BD_NAME', 'asc');

        if( $extraData['listActive'] ){
            $query->where('BD_LIST_ACTIVE', 'Y');
        }

        $brandList = $query->get()
            ->toArray();

        return $brandList;

    }

}

