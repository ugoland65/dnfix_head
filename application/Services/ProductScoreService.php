<?php

namespace App\Services;

use App\Models\ProductScoreModel;

class ProductScoreService
{

    /**
     * 상품 평점 조회
     * 
     * @param int $pd_idx 상품 인덱스
     * @return array 상품 평점 데이터
     */
    public function getProductScoreByPdIdx($pd_idx)
    {

        $query = ProductScoreModel::where('ps_pd_idx', $pd_idx)
            ->where('ps_mode', 'total')
            ->first();

        if (!empty($query)) {
            $result = $query->toArray();
        }

        if (!empty($result)) {
            if (!empty($result['ps_score'])) {
                $result['ps_score'] = json_decode($result['ps_score'], true);
            }
        }

        return $result ?? [];

    }

}