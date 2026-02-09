<?php

namespace App\Services;

use App\Models\OnaOrderGroupModel;

class OnaOrderGroupService
{

    /**
     * 주문서 그룹 셀렉트바를 위한 조회
     * @param array|null $criteria 검색 조건
     * @return array
     */
    public function getOnaOrderGroupForSelect($criteria=null) 
    {
        $query = OnaOrderGroupModel::select('oog_idx', 'oog_name')
            ->orderBy('oog_name', 'asc');

        $result = $query->get()
            ->toArray();

        return $result;
    }

}