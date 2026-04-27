<?php
namespace App\Services\Coupang;

use Exception;
use App\Models\CoupangProductModel;
use App\Models\ProductStockModel;
use App\Models\ProductModel;
use App\Utils\HttpClient;

class CoupangService
{

    /*
     * 쿠팡 동기화 상품 목록
     * 
     * @param array $criteria
     * @return array
     */
    public function getCoupangProductList($criteria)
    {

        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $query = CoupangProductModel::query()
            ->from('coupang_products as CP')
            ->leftJoin('prd_stock as PS', 'PS.ps_idx', '=', 'CP.ps_idx')
            ->leftJoin('COMPARISON_DB as PM', 'PM.CD_IDX', '=', 'PS.ps_prd_idx')
            ->select([
                'CP.*',
                'PS.*',
                'PM.*',
            ])
            ->orderBy('CP.idx', 'desc')
            ->get();

        $result = $query->paginate($perPage, $page);

        foreach($result['data'] as &$row){
            $row['raw_json'] = json_decode($row['raw_json'], true);

            //로켓상품 여부
            if( !empty($row['raw_json']['items'][0]['rocketGrowthItemData']) ){

                $isRocket = true;
                $row['isRocket'] = true;
                $row['rocket_price'] = $row['raw_json']['items'][0]['rocketGrowthItemData']['priceData']['salePrice'] ?? null;
                $row['marketplace_price'] = $row['raw_json']['items'][0]['marketplaceItemData']['priceData']['salePrice'] ?? null;

            }else{
                $isRocket = false;
                $row['isRocket'] = false;
                $row['rocket_price'] = null;
                $row['marketplace_price'] = $row['raw_json']['items'][0]['salePrice'] ?? null;
            }

            //재고 동기화 
            if( !empty($row['stock_json']) ){
                $row['stock_json'] = json_decode($row['stock_json'], true);
            }
            if( !empty($row['rocket_stock_json']) ){
                $row['rocket_stock_json'] = json_decode($row['rocket_stock_json'], true);
            }

        }
        unset($row);

        //dd($result);

        return $result;

    }

    /*
     * 쿠팡상품 - 재고코드 매칭
     *
     * @param array $payload
     * @return array
     */
    public function matchProductStock($payload)
    {
        $idx = (int)($payload['idx'] ?? 0);
        $psIdx = (int)($payload['ps_idx'] ?? 0);

        if ($idx < 1) {
            throw new Exception('쿠팡 상품 idx가 올바르지 않습니다.');
        }
        if ($psIdx < 1) {
            throw new Exception('재고코드(ps_idx)가 올바르지 않습니다.');
        }

        $coupangProduct = CoupangProductModel::where('idx', $idx)->first();
        if (!$coupangProduct) {
            throw new Exception('쿠팡 상품 데이터를 찾을 수 없습니다.');
        }

        $productStock = ProductStockModel::where('ps_idx', $psIdx)->first();
        if (!$productStock) {
            throw new Exception('재고코드(ps_idx)에 해당하는 재고 데이터를 찾을 수 없습니다.');
        }

        $sellerProductId = $coupangProduct->seller_product_id ?? null;
        if (empty($sellerProductId)) {
            throw new Exception('쿠팡 seller_product_id가 없어 매칭할 수 없습니다.');
        }

        $isRocket = strtoupper(trim((string)($coupangProduct->is_rocket ?? 'N')));
        if (!in_array($isRocket, ['Y', 'N'], true)) {
            $isRocket = 'N';
        }

        CoupangProductModel::update(
            ['idx' => $idx],
            ['ps_idx' => $psIdx]
        );

        ProductStockModel::update(
            ['ps_idx' => $psIdx],
            [
                'coupang_seller_product_id' => $sellerProductId,
                'is_rocket' => $isRocket,
            ]
        );

        return [
            'idx' => $idx,
            'ps_idx' => $psIdx,
            'seller_product_id' => $sellerProductId,
            'is_rocket' => $isRocket,
            'message' => '상품 매칭이 완료되었습니다. (ps_idx: ' . $psIdx . ')',
        ];
    }

    /*
     * 쿠팡상품 - 재고코드 매칭 해제
     *
     * @param array $payload
     * @return array
     */
    public function cancelProductStockMatch($payload)
    {
        $idx = (int)($payload['idx'] ?? 0);
        if ($idx < 1) {
            throw new Exception('쿠팡 상품 idx가 올바르지 않습니다.');
        }

        $coupangProduct = CoupangProductModel::where('idx', $idx)->first();
        if (!$coupangProduct) {
            throw new Exception('쿠팡 상품 데이터를 찾을 수 없습니다.');
        }

        $psIdx = (int)($coupangProduct->ps_idx ?? 0);
        if ($psIdx < 1) {
            throw new Exception('해제할 매칭 정보가 없습니다.');
        }

        ProductStockModel::update(
            ['ps_idx' => $psIdx],
            [
                'coupang_seller_product_id' => null,
                'is_rocket' => null,
            ]
        );

        CoupangProductModel::update(
            ['idx' => $idx],
            ['ps_idx' => null]
        );

        return [
            'idx' => $idx,
            'ps_idx' => $psIdx,
            'message' => '상품 매칭이 해제되었습니다. (ps_idx: ' . $psIdx . ')',
        ];
    }

}