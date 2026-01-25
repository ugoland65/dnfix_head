<?php

namespace App\Services;

use Exception;
use App\Models\ProductStockUnitModel;
use App\Models\BrandModel;

class SaleService
{

    /**
     * 기간별 상품 판매 순위 조회
     *
     * @param array $criteria [
     *   'from'  => 'YYYY-MM-DD',
     *   'to'    => 'YYYY-MM-DD',
     *   'limit' => 100, // optional
     * ]
     * @return array
     */
    public function getSalesRankingByPeriod($criteria = [])
    {

        $s_date = $criteria['s_date'] ?? date('Y-m-01');
        $e_date = $criteria['e_date'] ?? date('Y-m-d');
        $limit = (int)($criteria['limit'] ?? 0);

        $query = ProductStockUnitModel::query()
            ->from('prd_stock_unit as u')
            ->join('prd_stock as ps', 'ps.ps_idx', '=', 'u.psu_stock_idx')
            ->join('COMPARISON_DB as cd', 'cd.CD_IDX', '=', 'ps.ps_prd_idx')
            ->selectRaw("
                ps.ps_idx AS ps_idx,
                ps.ps_prd_idx AS prd_idx,
                ps_in_date,
                ps_last_date,
                cd.CD_IDX AS cd_idx,
                cd.CD_IMG AS cd_img,
                cd.CD_KIND_CODE AS cd_kind_code,
                cd.CD_NAME AS prd_name,
                cd_memo2,
                cd.CD_BRAND_IDX AS brand_idx,
                cd.CD_BRAND2_IDX AS brand2_idx,
                SUM(u.psu_qry) AS sold_qty
            ")
            ->where('u.psu_mode', '=', 'minus')
            ->whereIn('u.psu_kind', ['판매', '판매 (엑셀)'])
            ->whereBetween('u.psu_day', [$s_date, $e_date])
            ->groupBy([
                'ps.ps_prd_idx',
                'cd.CD_NAME',
                'cd.CD_BRAND_IDX',
            ])
            ->orderByRaw('sold_qty DESC');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $result = $query->get()->toArray();

        $config_product = config('admin.product');
        $prdKindName = $config_product['prd_kind_name'] ?? [];


        $brandIds = [];
        foreach($result as $row){
            if (!empty($row['brand_idx'])) {
                $brandIds[] = $row['brand_idx'];
            }
            if (!empty($row['brand2_idx']) && $row['brand2_idx'] != 0) {
                $brandIds[] = $row['brand2_idx'];
            }
        }

        $brandIds = array_unique($brandIds); // 중복 제거

        $brands = BrandModel::query()
            ->select(['BD_IDX', 'BD_NAME'])
            ->whereIn('BD_IDX', $brandIds)
            ->get()
            ->toArray();
        
        // 3. BD_IDX를 키로 하는 배열로 변환
        $brandMap = [];
        foreach ($brands as $brand) {
            $brandMap[$brand['BD_IDX']] = $brand['BD_NAME'] ?? '';
        }


        foreach($result as &$row){
            $row['prd_kind_name'] = $prdKindName[$row['cd_kind_code']] ?? '미지정';
            $row['brand_name'] = $brandMap[$row['brand_idx']] ?? '';

            // 두 번째 브랜드명 (존재하고 0이 아닐 경우)
            if (!empty($row['brand2_idx']) && $row['brand2_idx'] != 0) {
                $row['brand_name2'] = $brandMap[$row['brand2_idx']] ?? '';
            } else {
                $row['brand_name2'] = '';
            }
        }
        unset($row);



        return $result;
    }
}
