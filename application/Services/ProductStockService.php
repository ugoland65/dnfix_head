<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\ProductStockModel;
use App\Services\ProductService;

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

        $prdStockList = ProductStockModel::select([
                'prd_stock.ps_idx', 'prd_stock.ps_rack_code', 'prd_stock.ps_stock',
                'cd.CD_IDX', 'cd.CD_CODE', 'cd.CD_NAME', 'cd.cd_cost_price', 'cd.cd_size_fn'
            ])
            ->join('COMPARISON_DB as cd', 'prd_stock.ps_prd_idx', '=', 'cd.CD_IDX', 'LEFT')
            ->whereIn('prd_stock.ps_idx', $ids)
            ->get()
            ->keyBy('ps_idx')
            ->toArray();

        $productService = new ProductService();

        foreach ($prdStockList as &$prdStock) {
            $prdStock['cd_size_fn'] = json_decode($prdStock['cd_size_fn'] ?? '{}', true);
            if (!is_array($prdStock['cd_size_fn'])) {
                $prdStock['cd_size_fn'] = [];
            }

            $_cd_size_w = (float)($prdStock['cd_size_fn']['package']['W'] ?? 0);
            $_cd_size_h = (float)($prdStock['cd_size_fn']['package']['H'] ?? 0);
            $_cd_size_d = (float)($prdStock['cd_size_fn']['package']['D'] ?? 0);

            if( !empty($_cd_size_w) || !empty($_cd_size_h) || !empty($_cd_size_d) ){
                $_cd_size_volume = $_cd_size_w * $_cd_size_h * $_cd_size_d;
                $prdStock['package_volume'] = $_cd_size_volume;
                $prdStock['package_volume_m3'] = $_cd_size_volume / 1000000;
                $prdStock['package_volume_level'] = $productService->getVolumeLevel($_cd_size_volume);
            }else{
                $prdStock['package_volume'] = 0;
                $prdStock['package_volume_m3'] = 0;
                $prdStock['package_volume_level'] = 0;    
            }

        }
        unset($prdStock);

        return $prdStockList;
    }



}
