<?php

namespace App\Services;

use App\Models\ProductModel;
use App\Models\ProductStockModel;
use App\Models\ProductStockSaleLogModel;
use App\Models\ProductStockUnitModel;
use App\Services\AdminActionLogService;

class ProductStockSaleLogService
{
    /**
     * 할인 로그를 신규 로그 테이블(prd_stock_sale_log)에 저장한다.
     * 기존 ps_sale_log와 동일하게 grouping_idx + sale_per 조합은 중복 저장하지 않는다.
     *
     * @param array $payload
     * @return bool true: 저장됨, false: 중복/스킵
     */
    public function createSaleLog(array $payload): bool
    {
        $psIdx = (int)($payload['ps_idx'] ?? 0);
        $groupingIdx = (int)($payload['grouping_idx'] ?? 0);
        $salePer = (string)($payload['sale_per'] ?? '0');

        if ($psIdx <= 0 || $groupingIdx <= 0) {
            return false;
        }

        $exists = ProductStockSaleLogModel::query()
            ->where('ps_idx', $psIdx)
            ->where('grouping_idx', $groupingIdx)
            ->where('sale_per', $salePer)
            ->first();

        if (!empty($exists)) {
            return false;
        }

        $regInfo = $payload['d'] ?? [];
        if (!is_array($regInfo)) {
            $regInfo = [];
        }

        $insertData = [
            'ps_idx' => $psIdx,
            'prd_mode' => (string)($payload['prd_mode'] ?? 'prdDB'),
            'sale_mode' => (string)($payload['sale_mode'] ?? ''),
            'grouping_idx' => $groupingIdx,
            'pg_subject' => (string)($payload['pg_subject'] ?? ''),
            'pg_sday' => (string)($payload['pg_sday'] ?? ''),
            'pg_day' => (string)($payload['pg_day'] ?? ''),
            'sale_per' => $salePer,
            'original_price' => (int)($payload['original_price'] ?? 0),
            'sale_price' => (int)($payload['sale_price'] ?? 0),
            'margin_price' => (int)($payload['margin_price'] ?? 0),
            'margin_per' => (string)($payload['margin_per'] ?? '0'),
            'reg_date' => (string)($regInfo['date'] ?? date('Y-m-d H:i:s')),
            'reg_id' => (string)($regInfo['id'] ?? ''),
            'reg_name' => (string)($regInfo['name'] ?? ''),
            'reg_ip' => (string)($regInfo['ip'] ?? ''),
            'reg_domain' => (string)($regInfo['domain'] ?? ''),
            'raw_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        ProductStockSaleLogModel::create($insertData);

        return true;
    }

    
    /**
     * 상품 할인 로그 화면용 데이터 조회
     *
     * @param int|string $prdIdx
     * @return array
     */
    public function getSaleLogPageData($prdIdx): array
    {
        $prdIdx = (int)$prdIdx;
        if ($prdIdx <= 0) {
            throw new \InvalidArgumentException('상품 고유번호가 비어있습니다.');
        }

        $product = ProductModel::query()
            ->select(['CD_IDX', 'cd_sale_price', 'cd_cost_price'])
            ->where('CD_IDX', $prdIdx)
            ->first();
        $product = $product ? $product->toArray() : [
            'CD_IDX' => $prdIdx,
            'cd_sale_price' => 0,
            'cd_cost_price' => 0,
        ];

        $stock = ProductStockModel::query()
            ->select(['ps_idx'])
            ->where('ps_prd_idx', $prdIdx)
            ->first();
        $psIdx = (int)($stock['ps_idx'] ?? 0);

        $saleLogs = ProductStockSaleLogModel::query()
            ->where('ps_idx', $psIdx)
            ->orderBy('pg_day', 'desc')
            ->orderBy('seq', 'desc')
            ->get()
            ->toArray();

        $rows = [];
        foreach ($saleLogs as $log) {
            $saleMode = (string)($log['sale_mode'] ?? '');
            $pgSday = (string)($log['pg_sday'] ?? '');
            $pgDay = (string)($log['pg_day'] ?? '');

            if ($saleMode === 'period') {
                $saleModeText = '기간할인';
                $displayDay = $pgSday . ' ~<br>' . $pgDay;
                $qtyDayText = '';
                $qtySum = 0;

                if ($pgSday !== '' && $pgDay !== '' && $psIdx > 0) {
                    $dayStart = date('Y-m-d', strtotime($pgSday . ' +1 days'));
                    $dayEnd = date('Y-m-d', strtotime($pgDay . ' +1 days'));
                    $qtyDayText = $dayStart . ' ~<br>' . $dayEnd;
                    $qtySum = $this->getSoldQtyInPeriod($psIdx, $dayStart, $dayEnd);
                }
            } else {
                $saleModeText = '일일할인';
                $displayDay = $pgDay;
                $qtyDayText = '';
                $qtySum = 0;

                if ($pgDay !== '' && $psIdx > 0) {
                    $targetDay = date('Y-m-d', strtotime($pgDay . ' +1 days'));
                    $qtyDayText = $targetDay;
                    $qtySum = $this->getSoldQtyInDay($psIdx, $targetDay);
                }
            }

            $originalPrice = (int)($log['original_price'] ?? 0);
            if ($originalPrice <= 0) {
                $originalPrice = (int)($product['cd_sale_price'] ?? 0);
            }

            $costPrice = (int)($product['cd_cost_price'] ?? 0);
            $marginPre = 0;
            if ($originalPrice > 0) {
                $marginPre = round((($originalPrice - $costPrice) / $originalPrice) * 100, 2);
            }

            $salePrice = (int)($log['sale_price'] ?? 0);
            $marginPrice = (int)($log['margin_price'] ?? 0);

            $rows[] = [
                'sale_mode_text' => $saleModeText,
                'display_day' => $displayDay,
                'original_price' => $originalPrice,
                'cost_price' => $costPrice,
                'margin_pre' => $marginPre,
                'sale_per' => (string)($log['sale_per'] ?? '0'),
                'sale_price' => $salePrice,
                'margin_price' => $marginPrice,
                'margin_per' => (string)($log['margin_per'] ?? '0'),
                'qty_day_text' => $qtyDayText,
                'qty_sum' => (int)$qtySum,
                'sale_amount' => $salePrice * (int)$qtySum,
                'profit_amount' => $marginPrice * (int)$qtySum,
            ];
        }

        return [
            'prd_idx' => $prdIdx,
            'ps_idx' => $psIdx,
            'rows' => $rows,
        ];
    }

    /**
     * 상품의 최근 할인일(ps_sale_date) 조회
     *
     * @param int|string $prdIdx
     * @return array
     */
    public function getRecentSaleLogByPrdIdx($prdIdx): array
    {
        $prdIdx = (int)$prdIdx;
        if ($prdIdx <= 0) {
            return [
                'ps_idx' => 0,
                'ps_sale_date' => '',
            ];
        }

        $row = ProductStockModel::query()
            ->select(['ps_idx', 'ps_sale_date'])
            ->where('ps_prd_idx', $prdIdx)
            ->first();

        return [
            'ps_idx' => (int)($row['ps_idx'] ?? 0),
            'ps_sale_date' => (string)($row['ps_sale_date'] ?? ''),
        ];
    }

    /**
     * 최근 할인일 수정 저장 + 액션 로그 기록
     *
     * @param array $payload
     * @return array
     */
    public function updateRecentSaleDate(array $payload): array
    {
        $prdIdx = (int)($payload['prd_idx'] ?? 0);
        $newSaleDate = trim((string)($payload['ps_sale_date'] ?? ''));

        if ($prdIdx <= 0) {
            throw new \InvalidArgumentException('상품 고유번호가 비어있습니다.');
        }

        if ($newSaleDate === '') {
            throw new \InvalidArgumentException('최근 할인일을 입력해주세요.');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $newSaleDate)) {
            throw new \InvalidArgumentException('최근 할인일 형식이 올바르지 않습니다.');
        }

        $stockModel = ProductStockModel::query()
            ->select(['ps_idx', 'ps_prd_idx', 'ps_sale_date'])
            ->where('ps_prd_idx', $prdIdx)
            ->first();

        if (empty($stockModel)) {
            throw new \RuntimeException('재고 정보를 찾을 수 없습니다.');
        }

        $psIdx = (int)($stockModel['ps_idx'] ?? 0);
        $beforeSaleDate = (string)($stockModel['ps_sale_date'] ?? '');

        ProductStockModel::where('ps_idx', $psIdx)->update([
            'ps_sale_date' => $newSaleDate,
        ]);

        $adminActionLogService = new AdminActionLogService();
        $before = [
            'ps_sale_date' => $beforeSaleDate,
        ];
        $after = [
            'ps_sale_date' => $newSaleDate,
        ];
        $diff = $adminActionLogService->buildDiff($before, $after);

        $adminActionLogService->log([
            'target_type' => 'product',
            'target_table' => 'prd_stock',
            'target_pk' => (string)$prdIdx,
            'action_mode' => 'update',
            'action_summary' => '최근 할인일 수정',
            'before_json' => $before,
            'after_json' => $after,
            'diff_json' => $diff,
        ]);

        return [
            'success' => true,
            'message' => '최근 할인일이 저장되었습니다.',
            'ps_idx' => $psIdx,
            'ps_sale_date' => $newSaleDate,
        ];
    }

    private function getSoldQtyInDay(int $psIdx, string $day): int
    {
        if ($psIdx <= 0 || $day === '') {
            return 0;
        }

        $row = ProductStockUnitModel::query()
            ->from('prd_stock_unit')
            ->selectRaw('SUM(psu_qry) AS qty_sum')
            ->where('psu_stock_idx', '=', $psIdx)
            ->where('psu_day', '=', $day)
            ->where('psu_mode', '=', 'minus')
            ->whereRaw("INSTR(psu_kind, '판매')")
            ->first();

        return (int)($row['qty_sum'] ?? 0);
    }

    private function getSoldQtyInPeriod(int $psIdx, string $dayStart, string $dayEnd): int
    {
        if ($psIdx <= 0 || $dayStart === '' || $dayEnd === '') {
            return 0;
        }

        $row = ProductStockUnitModel::query()
            ->from('prd_stock_unit')
            ->selectRaw('SUM(psu_qry) AS qty_sum')
            ->where('psu_stock_idx', '=', $psIdx)
            ->whereBetween('psu_day', [$dayStart, $dayEnd])
            ->where('psu_mode', '=', 'minus')
            ->whereRaw("INSTR(psu_kind, '판매')")
            ->first();

        return (int)($row['qty_sum'] ?? 0);
    }
}
