<?php

namespace App\Services;

use Exception;
use App\Core\BaseClass;
use App\Models\ProductStockModel;
use App\Services\ProductService;
use App\Core\AuthAdmin;
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

    /**
     * 상품 세일 설정
     * @param array $requestData
     * @return array
     */
    public function setProductSale($data)
    {
        $ps_idx = $data['ps_idx'] ?? null;
        $mode = $data['mode'] ?? null;

        if( empty($ps_idx) || empty($mode) ){
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $query = ProductStockModel::find($ps_idx);
        
        if( empty($query) ){
            throw new Exception('상품 재고가 존재하지 않습니다.');
        }

        $productStock = $query->toArray();

        $updateData = [];
        $message = '처리가 완료되었습니다.';

        $sale_data = json_decode($productStock['ps_sale_data'] ?? '{}', true);
        if (!is_array($sale_data)) {
            $sale_data = [];
        }

        $isMonthly = !empty($productStock['is_sale_month']);
        $isSpecial = !empty($productStock['is_sale_special']);

        if( $mode == 'monthly' ){
            if ($isSpecial) {
                $updateData['is_sale_special'] = 0;
                $sale_data['special']['off'] = [
                    'date' => date('Y-m-d'),
                    'reg' => AuthAdmin::getConnectionInfo()
                ];
                $message = '이미 특가할인중입니다. 특가할인을 해제하고 월간할인으로 지정합니다.';
            }
            $updateData['is_sale_month'] = 1;
        }

        if( $mode == 'special' ){
            if ($isMonthly) {
                $updateData['is_sale_month'] = 0;
                $sale_data['monthly']['off'] = [
                    'date' => date('Y-m-d'),
                    'reg' => AuthAdmin::getConnectionInfo()
                ];
                $message = '이미 월간할인중입니다. 월간할인을 해제하고 특가할인으로 지정합니다.';
            }
            $updateData['is_sale_special'] = 1;
        }

        $sale_data[$mode]['on'] = [
            'date' => date('Y-m-d'),
            'reg' => AuthAdmin::getConnectionInfo()
        ];
        $ps_sale_data = json_encode($sale_data, JSON_UNESCAPED_UNICODE);

        $updateData['ps_sale_data'] = $ps_sale_data;

        $result = ProductStockModel::where('ps_idx', $ps_idx)->update($updateData);

        return [
            'success' => (bool)$result,
            'message' => $message,
        ];


    }


    /**
     * 상품 세일 해제
     * 
     * @param array $requestData
     * @return array
     */
    public function unsetProductSale($data)
    {
        $ps_idx = $data['ps_idx'] ?? null;
        $mode = $data['mode'] ?? null;

        if( empty($ps_idx) || empty($mode) ){
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $query = ProductStockModel::find($ps_idx);
        
        if( empty($query) ){
            throw new Exception('상품 재고가 존재하지 않습니다.');
        }

        $productStock = $query->toArray();

        $updateData = [];
        if( $mode == 'monthly' ){
            $updateData['is_sale_month'] = 0;
        }

        if( $mode == 'special' ){
            $updateData['is_sale_special'] = 0;
        }

        $sale_data = json_decode($productStock['ps_sale_data'] ?? '{}', true);
        if (!is_array($sale_data)) {
            $sale_data = [];
        }

        if (!isset($sale_data[$mode]) || !is_array($sale_data[$mode])) {
            $sale_data[$mode] = [];
        }

        $sale_data[$mode]['off'] = [
            'date' => date('Y-m-d'),
            'reg' => AuthAdmin::getConnectionInfo()
        ];

        $updateData['ps_sale_data'] = json_encode($sale_data, JSON_UNESCAPED_UNICODE);

        $result = ProductStockModel::where('ps_idx', $ps_idx)->update($updateData);

        return [
            'success' => (bool)$result,
            'message' => '처리가 완료되었습니다.',
        ];
    }

}
