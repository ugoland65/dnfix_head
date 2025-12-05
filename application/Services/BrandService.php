<?php

namespace App\Services;

use App\Models\BrandModel;

class BrandService
{

    /**
     * 브랜드 셀렉트바를 위한 조회
     * 
     * @param array|null $extraData 추가 파라미터
     * └ $extraData {bool} $extraData['listActive'] : 목록 화면에서 사용 여부
     * @return array
     */
    public function getBrandForSelect($extraData=null) 
    {
        
        $query = BrandModel::select('BD_IDX', 'BD_NAME')
            ->orderBy('BD_NAME', 'asc');

        // null-safe 처리: $extraData가 배열이고 'listActive' 키가 존재하며 true일 경우에만 조건 추가
        if( is_array($extraData) && !empty($extraData['listActive']) ){
            $query->where('BD_LIST_ACTIVE', 'Y');
        }

        $brandList = $query->get()
            ->toArray();

        return $brandList;

    }

    /**
     * 브랜드 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getOnadbBrandList($criteria)
    {

        $brandList = BrandModel::query()
            ->where('bd_onadb_active', 'Y')
            ->orderBy('bd_onadb_sort_num', 'asc')
            ->get()
            ->toArray();

        return $brandList;

    }


    /**
     * 브랜드 정보 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getBrandInfo($idx)
    {
        $brandInfo = BrandModel::query()
            ->where('BD_IDX', $idx)
            ->first()
            ->toArray();

        return $brandInfo;

    }

}

