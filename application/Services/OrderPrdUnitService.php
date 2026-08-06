<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;
use App\Models\OrderSheetModel;
use App\Models\OrderPrdUnitModel;
use App\Models\OrderPrdUnitInspectionModel;
use App\Models\ProductModel;

class OrderPrdUnitService
{
    /**
     * 입금완료 시점의 주문 상품 JSON을 order_prd_unit 스냅샷으로 생성한다.
     *
     * 이미 한 건이라도 생성된 주문서는 재생성하지 않는다. 이후 입고 검사
     * 데이터가 수정될 수 있으므로 기존 행을 oo_json 값으로 덮어쓰지 않는다.
     */
    public function createUnitsForPaidOrder(int $orderIdx): array
    {
        if ($orderIdx <= 0) {
            throw new InvalidArgumentException('주문서 번호가 필요합니다.');
        }

        $existingRows = OrderPrdUnitModel::query()
            ->select(['idx'])
            ->where('order_idx', '=', $orderIdx)
            ->get()
            ->toArray();

        if (!empty($existingRows)) {
            return [
                'created' => false,
                'count' => count($existingRows),
            ];
        }

        $orderSheet = OrderSheetModel::query()
            ->select(['oo_idx', 'oo_json', 'oo_false'])
            ->where('oo_idx', '=', $orderIdx)
            ->first();
        $orderSheet = $orderSheet ? $orderSheet->toArray() : [];

        if (empty($orderSheet)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }

        $orderJson = json_decode((string)($orderSheet['oo_json'] ?? '[]'), true);
        if (!is_array($orderJson)) {
            throw new Exception('주문 상품 데이터를 읽을 수 없습니다.');
        }

        $failedPidxMap = $this->getFailedPidxMap((string)($orderSheet['oo_false'] ?? ''));
        $createdCount = 0;

        foreach ($orderJson as $groupRow) {
            if (!is_array($groupRow)) {
                continue;
            }

            $bidx = (int)($groupRow['bidx'] ?? 0);
            if ($bidx <= 0) {
                throw new Exception('주문서 그룹 PK(bidx)가 없는 상품이 있습니다.');
            }

            $selectedProducts = $groupRow['selpd'] ?? [];
            if (!is_array($selectedProducts)) {
                continue;
            }

            foreach ($selectedProducts as $productRow) {
                if (!is_array($productRow)) {
                    continue;
                }

                $pidx = (int)($productRow['pidx'] ?? 0);
                if ($pidx <= 0) {
                    continue;
                }

                OrderPrdUnitModel::create([
                    'order_idx' => $orderIdx,
                    'bidx' => $bidx,
                    'pidx' => $pidx,
                    'order_unit_price' => $this->toFloat($productRow['price'] ?? 0),
                    'order_qty' => max(0, (int)($productRow['qty'] ?? 0)),
                    'is_order_failed' => $this->toBool($productRow['false'] ?? false)
                        || isset($failedPidxMap[(string)$pidx]),
                    'stock_inspection_data' => [],
                    'stock_inspection_memo' => '',
                ]);
                $createdCount++;
            }
        }

        return [
            'created' => true,
            'count' => $createdCount,
        ];
    }

    /**
     * 모바일 입고수량 검수용 주문상품 목록을 조회한다.
     *
     * order_prd_unit 생성 여부와 무관하게 ona_order.oo_json을 기준으로 하며,
     * 고도몰 API나 검수 로직을 호출하지 않는다.
     */
    public function getStockInspectionUnits(int $orderIdx): array
    {
        if ($orderIdx <= 0) {
            return [
                'units' => [],
                'total_count' => 0,
                'failed_count' => 0,
            ];
        }

        $orderSheet = OrderSheetModel::query()
            ->select(['oo_json', 'oo_false'])
            ->where('oo_idx', '=', $orderIdx)
            ->first();
        $orderSheet = $orderSheet ? $orderSheet->toArray() : [];
        if (empty($orderSheet)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }

        $orderJson = json_decode((string)($orderSheet['oo_json'] ?? '[]'), true);
        if (!is_array($orderJson)) {
            throw new Exception('주문 상품 데이터를 읽을 수 없습니다.');
        }

        $failedPidxMap = $this->getFailedPidxMap((string)($orderSheet['oo_false'] ?? ''));
        $orderProducts = [];
        $totalCount = 0;
        $failedCount = 0;

        foreach ($orderJson as $groupRow) {
            $selectedProducts = is_array($groupRow) ? ($groupRow['selpd'] ?? []) : [];
            if (!is_array($selectedProducts)) {
                continue;
            }

            foreach ($selectedProducts as $productRow) {
                if (!is_array($productRow)) {
                    continue;
                }

                $pidx = (int)($productRow['pidx'] ?? 0);
                if ($pidx <= 0) {
                    continue;
                }

                $totalCount++;
                $isOrderFailed = $this->toBool($productRow['false'] ?? false)
                    || isset($failedPidxMap[(string)$pidx]);
                if ($isOrderFailed) {
                    $failedCount++;
                    continue;
                }

                $orderProducts[] = [
                    'bidx' => (int)($groupRow['bidx'] ?? 0),
                    'pidx' => $pidx,
                    'order_qty' => max(0, (int)($productRow['qty'] ?? 0)),
                    'order_unit_price' => $this->toFloat($productRow['price'] ?? 0),
                ];
            }
        }

        $pidxList = array_values(array_unique(array_column($orderProducts, 'pidx')));
        if (empty($pidxList)) {
            return [
                'units' => [],
                'total_count' => $totalCount,
                'failed_count' => $failedCount,
            ];
        }

        $productRows = ProductModel::query()
            ->from('COMPARISON_DB as P')
            ->leftJoin('prd_stock as S', 'S.ps_prd_idx', '=', 'P.CD_IDX')
            ->leftJoin('BRAND_DB as B', 'B.BD_IDX', '=', 'P.CD_BRAND_IDX')
            ->select([
                'P.CD_IDX as pidx',
                'P.CD_NAME as product_name',
                'P.CD_IMG as product_image',
                'P.CD_CODE as barcode',
                'S.ps_idx',
                'S.ps_stock as stock_qty',
                'S.ps_rack_code',
                'B.BD_NAME as brand_name',
            ])
            ->whereIn('P.CD_IDX', $pidxList)
            ->get()
            ->toArray();

        $productMap = [];
        foreach ($productRows as $productRow) {
            $productMap[(int)($productRow['pidx'] ?? 0)] = $productRow;
        }

        $inspectionRows = OrderPrdUnitModel::query()
            ->select(['idx', 'bidx', 'pidx', 'stock_inspection_data', 'stock_inspection_memo'])
            ->where('order_idx', '=', $orderIdx)
            ->get()
            ->toArray();
        $inspectionDataMap = [];
        $inspectionMemoMap = [];
        $orderPrdUnitIdxMap = [];
        foreach ($inspectionRows as $inspectionRow) {
            $unitKey = (int)($inspectionRow['bidx'] ?? 0) . ':' . (int)($inspectionRow['pidx'] ?? 0);
            $inspectionData = json_decode((string)($inspectionRow['stock_inspection_data'] ?? ''), true);
            if (!is_array($inspectionData)) {
                $inspectionData = [];
            }
            $inspectionDataMap[$unitKey] = $inspectionData;
            $inspectionMemoMap[$unitKey] = (string)($inspectionRow['stock_inspection_memo'] ?? '');
            $orderPrdUnitIdxMap[$unitKey] = (int)($inspectionRow['idx'] ?? 0);
        }

        $checkedTotalByUnitIdx = [];
        $orderPrdUnitIdxList = array_values(array_filter(array_unique($orderPrdUnitIdxMap)));
        if (!empty($orderPrdUnitIdxList)) {
            $inspectionLogRows = OrderPrdUnitInspectionModel::query()
                ->select(['order_prd_unit_idx', 'checked_qty'])
                ->whereIn('order_prd_unit_idx', $orderPrdUnitIdxList)
                ->get()
                ->toArray();

            foreach ($inspectionLogRows as $inspectionLogRow) {
                $unitIdx = (int)($inspectionLogRow['order_prd_unit_idx'] ?? 0);
                $checkedTotalByUnitIdx[$unitIdx] = ($checkedTotalByUnitIdx[$unitIdx] ?? 0)
                    + (int)($inspectionLogRow['checked_qty'] ?? 0);
            }
        }

        $units = [];
        foreach ($orderProducts as $orderProduct) {
            $product = $productMap[$orderProduct['pidx']] ?? [];
            $image = trim((string)($product['product_image'] ?? ''));
            if ($image !== '' && strpos($image, '/') !== 0) {
                $image = '/data/comparion/' . $image;
            }
            $unitKey = $orderProduct['bidx'] . ':' . $orderProduct['pidx'];
            $checkedTotalQty = $checkedTotalByUnitIdx[$orderPrdUnitIdxMap[$unitKey] ?? 0] ?? 0;

            $units[] = [
                'idx' => 0,
                'bidx' => $orderProduct['bidx'],
                'pidx' => $orderProduct['pidx'],
                'ps_idx' => (int)($product['ps_idx'] ?? 0),
                'brand_name' => (string)($product['brand_name'] ?? ''),
                'product_name' => (string)($product['product_name'] ?? ''),
                'product_image' => $image,
                'barcode' => (string)($product['barcode'] ?? ''),
                'order_qty' => $orderProduct['order_qty'],
                'order_unit_price' => $orderProduct['order_unit_price'],
                'stock_qty' => (int)($product['stock_qty'] ?? 0),
                'ps_rack_code' => (string)($product['ps_rack_code'] ?? ''),
                'stock_inspection_data' => $inspectionDataMap[$unitKey] ?? [],
                'stock_inspection_memo' => $inspectionMemoMap[$unitKey] ?? '',
                'checked_total_qty' => $checkedTotalQty,
                'is_check_complete' => $checkedTotalQty === (int)$orderProduct['order_qty'],
            ];
        }

        return [
            'units' => $units,
            'total_count' => $totalCount,
            'failed_count' => $failedCount,
        ];
    }

    /**
     * 주문 JSON 기준으로 개별 검수 대상 상품을 조회한다.
     */
    public function getStockInspectionUnit(int $orderIdx, int $bidx, int $pidx): array
    {
        foreach ($this->getStockInspectionUnits($orderIdx)['units'] as $unit) {
            if ((int)$unit['bidx'] === $bidx && (int)$unit['pidx'] === $pidx) {
                return $unit;
            }
        }

        throw new Exception('검수할 주문상품을 찾을 수 없습니다.');
    }

    /**
     * 개별 상품의 누적 검수 현황과 등록 이력을 조회한다.
     */
    public function getInspectionHistory(int $orderIdx, int $bidx, int $pidx, int $currentAdminIdx = 0): array
    {
        $unit = $this->getStockInspectionUnit($orderIdx, $bidx, $pidx);
        $orderPrdUnit = $this->findOrderPrdUnit($orderIdx, $bidx, $pidx);
        $records = [];

        if ($orderPrdUnit) {
            $records = OrderPrdUnitInspectionModel::query()
                ->where('order_prd_unit_idx', '=', (int)($orderPrdUnit['idx'] ?? 0))
                ->orderBy('idx', 'desc')
                ->get()
                ->toArray();
        }

        $checkedTotalQty = 0;
        foreach ($records as &$record) {
            $record['idx'] = (int)($record['idx'] ?? 0);
            $record['checked_qty'] = (int)($record['checked_qty'] ?? 0);
            $record['is_owner'] = $currentAdminIdx > 0
                && $currentAdminIdx === (int)($record['inspector_admin_idx'] ?? 0);
            $checkedTotalQty += $record['checked_qty'];
        }
        unset($record);

        return [
            'unit' => $unit,
            'records' => $records,
            'checked_total_qty' => $checkedTotalQty,
            'remaining_qty' => (int)($unit['order_qty'] ?? 0) - $checkedTotalQty,
        ];
    }

    /**
     * 이번에 센 수량을 독립적인 검수 이력으로 추가한다.
     */
    public function addInspectionRecord(int $orderIdx, int $bidx, int $pidx, int $checkedQty, array $actor): void
    {
        if ($checkedQty <= 0) {
            throw new InvalidArgumentException('등록 수량은 1개 이상이어야 합니다.');
        }

        $orderPrdUnit = $this->getOrCreateOrderPrdUnit($orderIdx, $bidx, $pidx);
        $actorIdx = (int)($actor['idx'] ?? 0);
        if ($actorIdx <= 0) {
            throw new InvalidArgumentException('검수자 정보가 없습니다.');
        }

        OrderPrdUnitInspectionModel::create([
            'order_prd_unit_idx' => (int)($orderPrdUnit['idx'] ?? 0),
            'checked_qty' => $checkedQty,
            'inspector_admin_idx' => $actorIdx,
            'inspector_admin_id' => (string)($actor['id'] ?? ''),
            'inspector_admin_name' => (string)($actor['name'] ?? ''),
            'inspection_memo' => '',
        ]);
    }

    /**
     * 주문수량까지 남은 수량을 한 번에 검수 완료 처리한다.
     */
    public function completeInspection(int $orderIdx, int $bidx, int $pidx, array $actor): void
    {
        $history = $this->getInspectionHistory($orderIdx, $bidx, $pidx);
        $remainingQty = (int)($history['remaining_qty'] ?? 0);

        if ($remainingQty < 0) {
            throw new Exception('체크수량이 주문수량을 초과하여 완료 처리할 수 없습니다.');
        }
        if ($remainingQty === 0) {
            throw new Exception('이미 수량체크가 완료된 상품입니다.');
        }

        $this->addInspectionRecord($orderIdx, $bidx, $pidx, $remainingQty, $actor);
    }

    /**
     * 본인이 등록한 검수 이력만 수정한다.
     */
    public function updateInspectionRecord(int $inspectionIdx, int $checkedQty, int $currentAdminIdx): bool
    {
        if ($checkedQty <= 0) {
            throw new InvalidArgumentException('등록 수량은 1개 이상이어야 합니다.');
        }

        $record = OrderPrdUnitInspectionModel::find($inspectionIdx);
        $record = $record ? $record->toArray() : [];
        $this->assertRecordOwner($record, $currentAdminIdx);

        return OrderPrdUnitInspectionModel::update(
            ['idx' => $inspectionIdx],
            ['checked_qty' => $checkedQty]
        );
    }

    /**
     * 본인이 등록한 검수 이력만 삭제한다.
     */
    public function deleteInspectionRecord(int $inspectionIdx, int $currentAdminIdx): bool
    {
        $record = OrderPrdUnitInspectionModel::find($inspectionIdx);
        $record = $record ? $record->toArray() : [];
        $this->assertRecordOwner($record, $currentAdminIdx);

        return OrderPrdUnitInspectionModel::query()
            ->where('idx', '=', $inspectionIdx)
            ->delete();
    }

    /**
     * 본인이 등록한 검수 이력의 메모를 저장한다.
     */
    public function saveInspectionRecordMemo(int $inspectionIdx, string $memo, int $currentAdminIdx): string
    {
        $memo = trim($memo);
        if (function_exists('mb_strlen') && mb_strlen($memo, 'UTF-8') > 1000) {
            throw new InvalidArgumentException('메모는 1,000자 이하로 입력해주세요.');
        }

        $record = OrderPrdUnitInspectionModel::find($inspectionIdx);
        $record = $record ? $record->toArray() : [];
        $this->assertRecordOwner($record, $currentAdminIdx);

        OrderPrdUnitInspectionModel::update(
            ['idx' => $inspectionIdx],
            ['inspection_memo' => $memo]
        );

        return $memo;
    }

    /**
     * 주문상품 단위의 입고 검수 메모를 저장한다.
     */
    public function saveInspectionMemo(int $orderIdx, int $bidx, int $pidx, string $memo): string
    {
        $memo = trim($memo);
        if (function_exists('mb_strlen') && mb_strlen($memo, 'UTF-8') > 1000) {
            throw new InvalidArgumentException('메모는 1,000자 이하로 입력해주세요.');
        }

        $orderPrdUnit = $this->getOrCreateOrderPrdUnit($orderIdx, $bidx, $pidx);
        OrderPrdUnitModel::update(
            ['idx' => (int)($orderPrdUnit['idx'] ?? 0)],
            ['stock_inspection_memo' => $memo]
        );

        return $memo;
    }

    private function getOrCreateOrderPrdUnit(int $orderIdx, int $bidx, int $pidx): array
    {
        $existingUnit = $this->findOrderPrdUnit($orderIdx, $bidx, $pidx);
        if ($existingUnit) {
            return $existingUnit;
        }

        $unit = $this->getStockInspectionUnit($orderIdx, $bidx, $pidx);
        $createdUnit = OrderPrdUnitModel::updateOrCreate([
            'order_idx' => $orderIdx,
            'bidx' => $bidx,
            'pidx' => $pidx,
        ], [
            'order_unit_price' => $unit['order_unit_price'],
            'order_qty' => $unit['order_qty'],
            'is_order_failed' => false,
            'stock_inspection_data' => [],
            'stock_inspection_memo' => '',
        ]);

        return $createdUnit->toArray();
    }

    private function findOrderPrdUnit(int $orderIdx, int $bidx, int $pidx): array
    {
        $unit = OrderPrdUnitModel::query()
            ->where('order_idx', '=', $orderIdx)
            ->where('bidx', '=', $bidx)
            ->where('pidx', '=', $pidx)
            ->first();

        return $unit ? $unit->toArray() : [];
    }

    private function assertRecordOwner(array $record, int $currentAdminIdx): void
    {
        if (empty($record)) {
            throw new Exception('검수 이력을 찾을 수 없습니다.');
        }
        if ($currentAdminIdx <= 0 || $currentAdminIdx !== (int)($record['inspector_admin_idx'] ?? 0)) {
            throw new Exception('본인이 등록한 검수 이력만 수정하거나 삭제할 수 있습니다.');
        }
    }

    /**
     * oo_false의 레거시 JSON 형식을 포함해 실패 상품 PK 목록을 만든다.
     */
    private function getFailedPidxMap(string $ooFalse): array
    {
        $raw = trim($ooFalse);
        if ($raw === '') {
            return [];
        }

        $rows = json_decode($raw, true);
        if (!is_array($rows)) {
            $rows = json_decode('[' . $raw . ']', true);
        }
        if (!is_array($rows)) {
            return [];
        }

        $map = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $pidx = (int)($row['pidx'] ?? 0);
            if ($pidx > 0) {
                $map[(string)$pidx] = true;
            }
        }

        return $map;
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value !== 0;
        }

        return in_array(strtolower(trim((string)$value)), ['1', 'true', 'y', 'yes', 'on'], true);
    }

    private function toFloat($value): float
    {
        return (float)str_replace(',', '', (string)$value);
    }
}
