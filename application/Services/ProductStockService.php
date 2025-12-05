<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\ProductStockModel;

class ProductStockService extends BaseClass 
{

    /**
     * 상품 재고 Where In 조회
     * @param array $idxs
     * @return array
     */
    public function getProductStockWhereIn($ids) {
        // 빈 배열이 전달된 경우 빈 배열 반환
        if (empty($ids)) {
            return [];
        }

        // 라라벨 스타일로 사용
        return ProductStockModel::select([
                'prd_stock.ps_idx', 'prd_stock.ps_rack_code', 'prd_stock.ps_stock',
                'cd.CD_IDX', 'cd.CD_CODE', 'cd.CD_NAME', 'cd.cd_cost_price'
            ])
            ->join('COMPARISON_DB as cd', 'prd_stock.ps_prd_idx', '=', 'cd.CD_IDX', 'LEFT')
            ->whereIn('prd_stock.ps_idx', $ids)
            ->get()
            ->keyBy('ps_idx')
            ->toArray();
    }



}
