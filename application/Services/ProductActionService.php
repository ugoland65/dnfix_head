<?php

namespace App\Services;

use App\Services\GodoApiService;
use App\Services\AdminActionLogService;
use App\Models\ProductModel;
use App\Models\ProductStockModel;

class ProductActionService
{

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

}