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
        $s_kind = trim((string)($criteria['s_kind'] ?? ''));
        $sKindSecond = trim((string)($criteria['s_kind_second'] ?? ''));
        $sKindThird = trim((string)($criteria['s_kind_third'] ?? ''));
        $sKindFourth = trim((string)($criteria['s_kind_fourth'] ?? ''));
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
                ps_sale_date,
                ps_sale_log,
                ps_stock,
                cd.CD_IDX AS cd_idx,
                cd.CD_IMG AS cd_img,
                cd.CD_KIND_CODE AS cd_kind_code,
                cd.CD_CATEGORY_CODE AS cd_category_code,
                cd.CD_NAME AS prd_name,
                cd_memo2,
                cd.CD_BRAND_IDX AS brand_idx,
                cd.CD_BRAND2_IDX AS brand2_idx,
                SUM(u.psu_qry) AS sold_qty,
                cd.cd_sale_price,
                cd.cd_cost_price
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

        if ($s_kind !== '') {
            $query->where('cd.CD_KIND_CODE', '=', $s_kind);
        }

        $filterCategoryCode = $sKindFourth !== '' ? $sKindFourth : ($sKindThird !== '' ? $sKindThird : $sKindSecond);
        if ($filterCategoryCode !== '') {
            $categoryCodes = $this->collectCategoryCodesForFilter($filterCategoryCode);
            if (!empty($categoryCodes)) {
                $query->whereIn('cd.CD_CATEGORY_CODE', $categoryCodes);
            } else {
                $query->where('cd.CD_CATEGORY_CODE', '=', $filterCategoryCode);
            }
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $result = $query->get()->toArray();

        $config_product = config('admin.product');
        $prdKindName = $config_product['prd_kind_name'] ?? [];
        $categories = (isset($config_product['categories']) && is_array($config_product['categories']))
            ? $config_product['categories']
            : [];


        $brandIds = [];
        foreach($result as $row){
            if (!empty($row['brand_idx'])) {
                $brandIds[] = $row['brand_idx'];
            }
            if (!empty($row['brand2_idx']) && $row['brand2_idx'] != 0) {
                $brandIds[] = $row['brand2_idx'];
            }
        }

        $brandIds = array_values(array_unique($brandIds)); // 중복 제거

        $brands = [];
        if (!empty($brandIds)) {
            $brands = BrandModel::query()
                ->select(['BD_IDX', 'BD_NAME'])
                ->whereIn('BD_IDX', $brandIds)
                ->get()
                ->toArray();
        }
        
        // 3. BD_IDX를 키로 하는 배열로 변환
        $brandMap = [];
        foreach ($brands as $brand) {
            $brandMap[$brand['BD_IDX']] = $brand['BD_NAME'] ?? '';
        }


        foreach($result as &$row){

            $row['prd_kind_name'] = $prdKindName[$row['cd_kind_code']] ?? '미지정';
            $row['prd_category_path'] = $this->resolveCategoryPathLabel(
                $categories,
                (string)($row['cd_category_code'] ?? ''),
                (string)$row['prd_kind_name']
            );
            $row['brand_name'] = $brandMap[$row['brand_idx']] ?? '';

            // 두 번째 브랜드명 (존재하고 0이 아닐 경우)
            if (!empty($row['brand2_idx']) && $row['brand2_idx'] != 0) {
                $row['brand_name2'] = $brandMap[$row['brand2_idx']] ?? '';
            } else {
                $row['brand_name2'] = '';
            }

            $row['margin_per'] = 0;
            $row['margin_grade'] = '';
    
            // 마진율 계산
            if ($row['cd_sale_price'] > 0 && $row['cd_cost_price'] > 0) {
                if ($row['cd_sale_price'] < 29999) {
                    $row['margin_per'] = round(($row['cd_sale_price'] - $row['cd_cost_price']) / $row['cd_sale_price'] * 100, 2);
                } else {
                    $row['margin_per'] = round(($row['cd_sale_price'] - ($row['cd_cost_price'] + 2500)) / $row['cd_sale_price'] * 100, 2);
                }
            }
    
            // 마진율 그룹
            if ($row['margin_per'] > 39) {
                $row['margin_grade'] = 'A';
            } elseif ($row['margin_per'] >= 35) {
                $row['margin_grade'] = 'B';
            } elseif ($row['margin_per'] >= 30) {
                $row['margin_grade'] = 'C';
            } elseif ($row['margin_per'] >= 25) {
                $row['margin_grade'] = 'D';
            } elseif ($row['margin_per'] >= 20) {
                $row['margin_grade'] = 'E';
            } elseif ($row['margin_per'] >= 15) {
                $row['margin_grade'] = 'F';
            } elseif ($row['margin_per'] >= 10) {
                $row['margin_grade'] = 'G';
            } elseif ($row['margin_per'] >= 5) {
                $row['margin_grade'] = 'H';
            } elseif ($row['margin_per'] > 0) {
                $row['margin_grade'] = 'I';
            }

            $row['ps_sale_log'] = json_decode($row['ps_sale_log'] ?? '[]', true);
            if (!is_array($row['ps_sale_log'])) {
                $row['ps_sale_log'] = [];
            }

            $row['last_sale'] = [
                'sale_date' => $row['ps_sale_date'] ?? '',
                'sale_count' => count($row['ps_sale_log']),
                'sale_subject' => $row['ps_sale_log'][0]['pg_subject'] ?? '',
                'sale_per' => $row['ps_sale_log'][0]['sale_per'] ?? 0,
            ];

        }
        unset($row);



        return $result;
    }

    /**
     * 선택한 카테고리와 하위 카테고리 코드를 모두 수집한다.
     *
     * @param string $categoryCode
     * @return array<int,string>
     */
    private function collectCategoryCodesForFilter(string $categoryCode): array
    {
        $categoryCode = trim($categoryCode);
        if ($categoryCode === '') {
            return [];
        }

        $configProduct = config('admin.product');
        $categories = $configProduct['categories'] ?? [];
        if (!is_array($categories)) {
            return [$categoryCode];
        }

        $node = $this->findCategoryNodeByCode($categories, $categoryCode);
        if (!is_array($node)) {
            return [$categoryCode];
        }

        $codes = $this->collectCategoryCodesFromNode($node);
        if (!in_array($categoryCode, $codes, true)) {
            array_unshift($codes, $categoryCode);
        }

        return array_values(array_unique($codes));
    }

    /**
     * @param array<int,mixed> $rows
     * @param string $categoryCode
     * @return array<string,mixed>|null
     */
    private function findCategoryNodeByCode(array $rows, string $categoryCode): ?array
    {
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (trim((string)($row['code'] ?? '')) === $categoryCode) {
                return $row;
            }
            $children = $row['children'] ?? [];
            if (is_array($children) && $children !== []) {
                $found = $this->findCategoryNodeByCode($children, $categoryCode);
                if (is_array($found)) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * @param array<string,mixed> $node
     * @return array<int,string>
     */
    private function collectCategoryCodesFromNode(array $node): array
    {
        $codes = [];
        $code = trim((string)($node['code'] ?? ''));
        if ($code !== '') {
            $codes[] = $code;
        }
        $children = $node['children'] ?? [];
        if (!is_array($children)) {
            return $codes;
        }
        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }
            $codes = array_merge($codes, $this->collectCategoryCodesFromNode($child));
        }

        return $codes;
    }

    /**
     * @param array<int,mixed> $rows
     * @param string $categoryCode
     * @param string $fallbackLabel
     * @return string
     */
    private function resolveCategoryPathLabel(array $rows, string $categoryCode, string $fallbackLabel): string
    {
        $categoryCode = trim($categoryCode);
        if ($categoryCode === '') {
            return $fallbackLabel;
        }

        $path = $this->findCategoryNamePath($rows, $categoryCode, []);
        if (empty($path)) {
            return $fallbackLabel;
        }

        return implode(' > ', $path);
    }

    /**
     * @param array<int,mixed> $rows
     * @param string $categoryCode
     * @param array<int,string> $currentPath
     * @return array<int,string>
     */
    private function findCategoryNamePath(array $rows, string $categoryCode, array $currentPath): array
    {
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $code = trim((string)($row['code'] ?? ''));
            $name = trim((string)($row['name'] ?? $row['key'] ?? $code));
            $nextPath = $currentPath;
            if ($name !== '') {
                $nextPath[] = $name;
            }
            if ($code === $categoryCode) {
                return $nextPath;
            }
            $children = $row['children'] ?? [];
            if (is_array($children) && $children !== []) {
                $found = $this->findCategoryNamePath($children, $categoryCode, $nextPath);
                if (!empty($found)) {
                    return $found;
                }
            }
        }

        return [];
    }
}
