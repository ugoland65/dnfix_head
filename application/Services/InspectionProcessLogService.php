<?php

namespace App\Services;

use App\Auth\AdminAuth;
use App\Models\InspectionProcessLogModel;

class InspectionProcessLogService
{
    public const LOCATION_ORDER_SHEET_ALL_STOCK = 'order_sheet_all_stock';
    public const LOCATION_PRODUCT_SINGLE_GODO_INSPECTION = 'product_single_godo_inspection';

    /**
     * 재고 일괄등록 로그 저장
     *
     * @param array $payload
     * @return int
     */
    public function logOrderSheetAllStock(array $payload): int
    {
        return $this->write(array_merge($payload, [
            'location_code' => self::LOCATION_ORDER_SHEET_ALL_STOCK,
            'relation_pk' => (int)($payload['relation_pk'] ?? 0),
        ]));
    }

    /**
     * 상품 개별 고도몰 검수 로그 저장
     *
     * @param array $payload
     * @return int
     */
    public function logProductSingleGodoInspection(array $payload): int
    {
        $processContent = (isset($payload['process_content']) && is_array($payload['process_content']))
            ? $payload['process_content']
            : [];
        $beforeValuesRaw = (isset($payload['before_values']) && is_array($payload['before_values']))
            ? $payload['before_values']
            : [];
        $afterValuesRaw = (isset($payload['after_values']) && is_array($payload['after_values']))
            ? $payload['after_values']
            : [];
        $changedValues = $this->buildChangedValues($beforeValuesRaw, $afterValuesRaw);

        // 저장 용량/가독성을 위해 변경된 키만 before/after에 남긴다.
        $beforeValues = [];
        $afterValues = [];
        foreach ($changedValues as $changedKey => $changedRow) {
            if (!is_array($changedRow)) {
                continue;
            }
            $beforeValues[$changedKey] = $changedRow['before'] ?? null;
            $afterValues[$changedKey] = $changedRow['after'] ?? null;
        }

        if (!empty($beforeValues)) {
            $processContent['before_values'] = $beforeValues;
        }
        if (!empty($afterValues)) {
            $processContent['after_values'] = $afterValues;
        }
        if (!empty($changedValues)) {
            $processContent['changed_values'] = $changedValues;
        }

        return $this->write(array_merge($payload, [
            'location_code' => self::LOCATION_PRODUCT_SINGLE_GODO_INSPECTION,
            'prd_idx' => (int)($payload['prd_idx'] ?? 0),
            'ps_idx' => (int)($payload['ps_idx'] ?? 0),
            'godo_goods_no' => trim((string)($payload['godo_goods_no'] ?? '')),
            'process_content' => $processContent,
        ]));
    }

    /**
     * 상품 검수 처리 히스토리 조회 (prd_idx 기준 최근순)
     *
     * @param int $prdIdx
     * @param int $limit
     * @return array
     */
    public function getHistoryByPrdIdx(int $prdIdx, int $limit = 30): array
    {
        if ($prdIdx <= 0) {
            return [];
        }
        if ($limit < 1) {
            $limit = 30;
        }

        $rows = InspectionProcessLogModel::query()
            ->select([
                'ipl_idx',
                'inspection_version',
                'location_code',
                'relation_pk',
                'prd_idx',
                'ps_idx',
                'godo_goods_no',
                'process_content',
                'result_content',
                'executor_admin_idx',
                'executor_admin_id',
                'executor_admin_name',
                'executed_at',
                'created_at',
            ])
            ->where('prd_idx', '=', $prdIdx)
            ->orderBy('ipl_idx', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();

        foreach ($rows as &$row) {
            $row['process_content'] = $this->jsonDecode($row['process_content'] ?? '');
            $row['result_content'] = $this->jsonDecode($row['result_content'] ?? '');
        }
        unset($row);

        return $rows;
    }

    /**
     * 로그 insert 공통 처리
     *
     * @param array $payload
     * @return int
     */
    public function write(array $payload): int
    {
        $now = date('Y-m-d H:i:s');
        $adminUser = AdminAuth::user();
        if (!is_array($adminUser)) {
            $adminUser = [];
        }

        $executorAdminIdx = (int)($payload['executor_admin_idx'] ?? ($adminUser['sess_idx'] ?? 0));
        $executorAdminId = (string)($payload['executor_admin_id'] ?? ($adminUser['sess_id'] ?? ''));
        $executorAdminName = (string)($payload['executor_admin_name'] ?? ($adminUser['sess_name'] ?? ''));
        $executedAt = trim((string)($payload['executed_at'] ?? ''));
        if ($executedAt === '') {
            $executedAt = $now;
        }

        $insertData = [
            'inspection_version' => trim((string)($payload['inspection_version'] ?? '')),
            'location_code' => trim((string)($payload['location_code'] ?? '')),
            'relation_pk' => (int)($payload['relation_pk'] ?? 0),
            'prd_idx' => (int)($payload['prd_idx'] ?? 0),
            'ps_idx' => (int)($payload['ps_idx'] ?? 0),
            'godo_goods_no' => trim((string)($payload['godo_goods_no'] ?? '')),
            'process_content' => $this->jsonEncode($payload['process_content'] ?? []),
            'result_content' => $this->jsonEncode($payload['result_content'] ?? []),
            'executor_admin_idx' => $executorAdminIdx,
            'executor_admin_id' => $executorAdminId,
            'executor_admin_name' => $executorAdminName,
            'executed_at' => $executedAt,
            'created_at' => $now,
        ];

        if ($insertData['relation_pk'] <= 0) {
            $insertData['relation_pk'] = null;
        }
        if ($insertData['inspection_version'] === '') {
            $insertData['inspection_version'] = null;
        }
        if ($insertData['prd_idx'] <= 0) {
            $insertData['prd_idx'] = null;
        }
        if ($insertData['ps_idx'] <= 0) {
            $insertData['ps_idx'] = null;
        }
        if ($insertData['godo_goods_no'] === '') {
            $insertData['godo_goods_no'] = null;
        }
        if ($insertData['executor_admin_idx'] <= 0) {
            $insertData['executor_admin_idx'] = null;
        }
        if ($insertData['executor_admin_id'] === '') {
            $insertData['executor_admin_id'] = null;
        }
        if ($insertData['executor_admin_name'] === '') {
            $insertData['executor_admin_name'] = null;
        }

        return (int)InspectionProcessLogModel::query()->insert($insertData);
    }

    /**
     * 배열/객체를 JSON 문자열로 변환
     *
     * @param mixed $value
     * @return string
     */
    private function jsonEncode($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * JSON 문자열을 배열로 변환
     *
     * @param mixed $value
     * @return array
     */
    private function jsonDecode($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * before/after를 비교해 변경된 필드만 추출
     *
     * @param array $beforeValues
     * @param array $afterValues
     * @return array
     */
    private function buildChangedValues(array $beforeValues, array $afterValues): array
    {
        $changed = [];
        $keys = array_unique(array_merge(array_keys($beforeValues), array_keys($afterValues)));
        foreach ($keys as $key) {
            $before = $beforeValues[$key] ?? null;
            $after = $afterValues[$key] ?? null;
            if ((string)$before === (string)$after) {
                continue;
            }
            $changed[$key] = [
                'before' => $before,
                'after' => $after,
            ];
        }
        return $changed;
    }
}
