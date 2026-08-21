<?php

namespace App\Services;

use App\Services\GodoApiService;
use App\Services\AdminActionLogService;
use App\Models\ProductModel;
use App\Models\ProductStockModel;

class ProductActionService
{

    /**
     * 고도몰 재입고 알림 요청 수를 수집해 상품에 저장한다.
     */
    public function syncGodoRestockAlertCount(array $payload): array
    {
        $prdIdx = trim((string)($payload['prd_idx'] ?? ''));
        $actionUrl = trim((string)($payload['action_url'] ?? ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? '')));

        if ($prdIdx === '' || !ctype_digit($prdIdx) || (int)$prdIdx <= 0) {
            throw new \Exception('유효한 상품번호가 없습니다.');
        }

        $product = ProductModel::find((int)$prdIdx);
        if (!$product) {
            throw new \Exception('상품을 찾을 수 없습니다.');
        }
        $productData = $product->toArray();
        $goodsNo = trim((string)($productData['cd_godo_code'] ?? ''));
        if ($goodsNo === '') {
            throw new \Exception('고도몰 상품코드가 없어 재입고 알림을 조회할 수 없습니다.');
        }

        $godoApiService = new GodoApiService();
        $apiResult = $godoApiService->getGodoGoodsRestockByGoodsNos($goodsNo, 'count');
        $restockQty = $this->resolveGodoRestockAlertCount($apiResult, $goodsNo);
        $collectedAt = date('Y-m-d H:i:s');

        $beforeData = [
            'CD_IDX' => (string)$prdIdx,
            'cd_godo_code' => $goodsNo,
            'cd_restock_alert_qty' => (int)($productData['cd_restock_alert_qty'] ?? 0),
            'cd_restock_alert_collected_at' => $productData['cd_restock_alert_collected_at'] ?? null,
        ];
        $afterData = $beforeData;
        $afterData['cd_restock_alert_qty'] = $restockQty;
        $afterData['cd_restock_alert_collected_at'] = $collectedAt;

        $updated = ProductModel::query()->update(
            [
                'cd_restock_alert_qty' => $restockQty,
                'cd_restock_alert_collected_at' => $collectedAt,
            ],
            ['CD_IDX' => (int)$prdIdx]
        );
        if (!$updated) {
            throw new \Exception('재입고 알림 수량 저장에 실패했습니다.');
        }

        $adminActionLogService = new AdminActionLogService();
        try {
            $adminActionLogService->log([
                'target_type' => 'product',
                'target_table' => 'COMPARISON_DB',
                'target_pk' => (string)$prdIdx,
                'action_mode' => 'sync_godo_restock_alert_count',
                'action_summary' => '고도몰 재입고 알림 요청 수량 수집',
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $adminActionLogService->buildDiff($beforeData, $afterData),
                'action_url' => $actionUrl !== '' ? $actionUrl : null,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 수집 결과 저장에 영향을 주지 않는다.
        }

        return [
            'restock_alert_qty' => $restockQty,
            'restock_alert_collected_at' => $collectedAt,
            'message' => '재입고 알림 요청 수량 ' . number_format($restockQty) . '건을 저장했습니다.',
        ];
    }

    /**
     * 여러 상품의 고도몰 재입고 알림 요청 수를 일괄 수집해 저장한다.
     */
    public function syncGodoRestockAlertCounts(array $prdIdxs, string $actionUrl = ''): array
    {
        $prdIdxs = array_values(array_unique(array_filter(array_map('intval', $prdIdxs), static function ($prdIdx) {
            return $prdIdx > 0;
        })));
        if (empty($prdIdxs)) {
            throw new \Exception('수집할 상품이 없습니다.');
        }

        $products = ProductModel::query()
            ->select([
                'CD_IDX',
                'cd_godo_code',
                'cd_restock_alert_qty',
                'cd_restock_alert_collected_at',
            ])
            ->whereIn('CD_IDX', $prdIdxs)
            ->get()
            ->toArray();

        $productsByGoodsNo = [];
        foreach ($products as $product) {
            $goodsNo = trim((string)($product['cd_godo_code'] ?? ''));
            if ($goodsNo === '') {
                continue;
            }
            $productsByGoodsNo[$goodsNo][] = $product;
        }

        $restockQtyByGoodsNo = [];
        $godoApiService = new GodoApiService();
        foreach (array_chunk(array_keys($productsByGoodsNo), 100) as $goodsNoChunk) {
            $apiResult = $godoApiService->getGodoGoodsRestockByGoodsNos(implode(',', $goodsNoChunk), 'count');
            foreach ($goodsNoChunk as $goodsNo) {
                $restockQtyByGoodsNo[$goodsNo] = $this->resolveGodoRestockAlertCount($apiResult, $goodsNo);
            }
        }

        $collectedAt = date('Y-m-d H:i:s');
        $updatedCount = 0;
        $adminActionLogService = new AdminActionLogService();

        foreach ($productsByGoodsNo as $goodsNo => $matchedProducts) {
            $restockQty = $restockQtyByGoodsNo[$goodsNo] ?? 0;
            foreach ($matchedProducts as $product) {
                $prdIdx = (int)($product['CD_IDX'] ?? 0);
                if ($prdIdx <= 0) {
                    continue;
                }

                $beforeData = [
                    'CD_IDX' => (string)$prdIdx,
                    'cd_godo_code' => $goodsNo,
                    'cd_restock_alert_qty' => (int)($product['cd_restock_alert_qty'] ?? 0),
                    'cd_restock_alert_collected_at' => $product['cd_restock_alert_collected_at'] ?? null,
                ];
                $afterData = $beforeData;
                $afterData['cd_restock_alert_qty'] = $restockQty;
                $afterData['cd_restock_alert_collected_at'] = $collectedAt;

                $updated = ProductModel::query()->update(
                    [
                        'cd_restock_alert_qty' => $restockQty,
                        'cd_restock_alert_collected_at' => $collectedAt,
                    ],
                    ['CD_IDX' => $prdIdx]
                );
                if (!$updated) {
                    throw new \Exception('재입고 알림 수량 저장에 실패했습니다. (상품번호: ' . $prdIdx . ')');
                }
                $updatedCount++;

                try {
                    $adminActionLogService->log([
                        'target_type' => 'product',
                        'target_table' => 'COMPARISON_DB',
                        'target_pk' => (string)$prdIdx,
                        'action_mode' => 'sync_godo_restock_alert_count',
                        'action_summary' => '폼그룹에서 고도몰 재입고 알림 요청 수량 일괄 수집',
                        'before_json' => $beforeData,
                        'after_json' => $afterData,
                        'diff_json' => $adminActionLogService->buildDiff($beforeData, $afterData),
                        'action_url' => $actionUrl !== '' ? $actionUrl : null,
                    ]);
                } catch (\Throwable $e) {
                    // 로그 저장 실패는 수집 결과 저장에 영향을 주지 않는다.
                }
            }
        }

        return [
            'requested_count' => count($prdIdxs),
            'updated_count' => $updatedCount,
            'skipped_count' => count($prdIdxs) - $updatedCount,
            'restock_alert_collected_at' => $collectedAt,
            'message' => '재입고 알림 요청 수량을 ' . number_format($updatedCount) . '개 상품에 저장했습니다.',
        ];
    }

    /**
     * 월간할인 해제
     */
    public function prdReleaseMonthlyDiscount($payload)
    {
        $goodsNo = trim((string)($payload['goodsNo'] ?? ''));
        $prdIdx = trim((string)($payload['prdIdx'] ?? ''));
        $prdStockIdx = trim((string)($payload['prdStockIdx'] ?? ''));
        $fixedPrice = $this->toInt($payload['fixedPrice'] ?? 0);
        $goodsPrice = $this->toInt($payload['goodsPrice'] ?? 0);
        $actionSource = trim((string)($payload['actionSource'] ?? ''));
        $actionSummary = trim((string)($payload['actionSummary'] ?? ''));
        $actionUrl = trim((string)($payload['actionUrl'] ?? ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? '')));

        if ($goodsNo === '') {
            throw new \Exception('상품번호가 없습니다.');
        }
        if ($prdIdx === '' || !ctype_digit($prdIdx)) {
            throw new \Exception('상품번호가 없습니다.');
        }
        if ($prdStockIdx === '' || !ctype_digit($prdStockIdx)) {
            throw new \Exception('재고코드가 없습니다.');
        }
        if ($fixedPrice <= 0 || $goodsPrice <= 0) {
            throw new \Exception('정가 또는 판매가가 없습니다.');
        }

        $productStock = ProductStockModel::find((int)$prdStockIdx);
        if (!$productStock) {
            throw new \Exception('재고코드가 없습니다.');
        }
        $productStockData = $productStock->toArray();

        $product = ProductModel::find((int)$prdIdx);
        if (!$product) {
            throw new \Exception('상품이 없습니다.');
        }
        $productData = $product->toArray();
        $beforeData = [
            'CD_IDX' => (string)($productData['CD_IDX'] ?? $prdIdx),
            'cd_sale_price' => (string)($productData['cd_sale_price'] ?? ''),
            'prd_stock' => [
                'ps_idx' => (string)($productStockData['ps_idx'] ?? $prdStockIdx),
                'is_sale_month' => (string)($productStockData['is_sale_month'] ?? ''),
            ],
        ];

        $stockPrdIdx = (int)($productStockData['ps_prd_idx'] ?? 0);
        if ($stockPrdIdx > 0 && $stockPrdIdx !== (int)$prdIdx) {
            throw new \Exception('상품/재고 정보가 일치하지 않습니다.');
        }

        $godoApiService = new GodoApiService();
        $result = $godoApiService->releaseGodoMonthlyDiscount($goodsNo);
        if (!is_array($result)) {
            throw new \Exception('고도몰 응답 형식이 올바르지 않습니다.');
        }
        $status = strtolower(trim((string)($result['status'] ?? '')));
        if ($status !== '' && $status !== 'success') {
            $message = trim((string)($result['message'] ?? ''));
            throw new \Exception($message !== '' ? $message : '할인해제 처리에 실패했습니다.');
        }

        // 월간할인 표시 제거
        if (!empty($productStockData['is_sale_month'])) {
            $updated = ProductStockModel::query()->update(
                ['is_sale_month' => 0],
                ['ps_idx' => (int)$prdStockIdx]
            );
            if (!$updated) {
                throw new \Exception('월간할인 상태 해제 저장에 실패했습니다.');
            }
        }

        // 인트라넷 판매가를 정가로 복원
        $currentSalePrice = (int)($productData['cd_sale_price'] ?? 0);
        if ($currentSalePrice !== $fixedPrice) {
            $updated = ProductModel::query()->update(
                ['cd_sale_price' => $fixedPrice],
                ['CD_IDX' => (int)$prdIdx]
            );
            if (!$updated) {
                throw new \Exception('상품 판매가 복원 저장에 실패했습니다.');
            }
        }

        $afterData = $beforeData;
        $afterData['cd_sale_price'] = (string)$fixedPrice;
        $afterData['prd_stock']['is_sale_month'] = '0';

        $resolvedActionSummary = $this->resolveReleaseMonthlyDiscountActionSummary($actionSource, $actionSummary);
        $adminActionLogService = new AdminActionLogService();
        $diff = $adminActionLogService->buildDiff($beforeData, $afterData);
        try {
            $adminActionLogService->log([
                'target_type' => 'product',
                'target_table' => 'COMPARISON_DB',
                'target_pk' => (string)$prdIdx,
                'action_mode' => 'update',
                'action_summary' => $resolvedActionSummary,
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $diff,
                'action_url' => $actionUrl !== '' ? $actionUrl : null,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 핵심 처리 성공/실패에 영향을 주지 않도록 분리한다.
        }

        return $result;
    }

    private function resolveReleaseMonthlyDiscountActionSummary(string $actionSource, string $actionSummary): string
    {
        if ($actionSummary !== '') {
            return $actionSummary;
        }
        if ($actionSource === 'monthly_discount_management') {
            return '월간할인관리 페이지에서 할인해제 (고도몰 반영완료)';
        }
        return '상품기본정보 페이지에서 할인해제 (고도몰 반영완료)';
    }

    private function toInt($value): int
    {
        if (is_int($value)) {
            return $value;
        }
        $normalized = preg_replace('/[^0-9\\-]/', '', (string)$value);
        if ($normalized === '' || $normalized === '-') {
            return 0;
        }
        return (int)$normalized;
    }

    private function resolveGodoRestockAlertCount(array $apiResult, string $goodsNo): int
    {
        $status = strtolower(trim((string)($apiResult['status'] ?? '')));
        if ($status !== '' && !in_array($status, ['success', 'ok'], true)) {
            $message = trim((string)($apiResult['message'] ?? ''));
            throw new \Exception($message !== '' ? $message : '고도몰 재입고 알림 조회에 실패했습니다.');
        }

        $countFields = ['count', 'cnt', 'total', 'totalCount', 'request_count', 'restock_count', 'reInquiryCnt', 'alarmCnt', 'member_count'];
        $rows = $apiResult['goodsCounts'] ?? $apiResult;
        if (!is_array($rows)) {
            throw new \Exception('고도몰 재입고 알림 응답 형식이 올바르지 않습니다.');
        }

        foreach ($rows as $rowKey => $row) {
            if (is_numeric($row) && (string)$rowKey === $goodsNo) {
                return max(0, (int)$row);
            }
            if (!is_array($row)) {
                continue;
            }

            $responseGoodsNo = trim((string)($row['goodsNo'] ?? ($row['goods_no'] ?? ($row['goodsno'] ?? ''))));
            if ($responseGoodsNo !== $goodsNo) {
                continue;
            }
            foreach ($countFields as $field) {
                if (isset($row[$field]) && is_numeric($row[$field])) {
                    return max(0, (int)$row[$field]);
                }
            }
            return 0;
        }

        if (isset($apiResult['totalCount']) && is_numeric($apiResult['totalCount'])) {
            return max(0, (int)$apiResult['totalCount']);
        }

        return 0;
    }

}