<?php

namespace App\Services;

use Exception;
use App\Classes\DB;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\GodoOrderGoodsModel;

class PurchaseService
{
    /**
     * 구매대행 발주서 목록 조회
     *
     * @param array $criteria
     * @return array
     */
    public function getPurchaseOrderList(array $criteria): array
    {
        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }

        $perPage = (int)($criteria['per_page'] ?? 50);
        if ($perPage < 1) {
            $perPage = 50;
        }

        $status = trim((string)($criteria['status'] ?? 'all'));
        $supplierName = trim((string)($criteria['supplier_name'] ?? ''));
        $searchValue = trim((string)($criteria['search_value'] ?? ''));

        $query = PurchaseOrderModel::query()
            ->from('purchase_orders as A')
            ->orderBy('A.idx', 'desc');

        if ($status !== '' && $status !== 'all') {
            $query->where('A.status', '=', $status);
        }

        if ($supplierName !== '') {
            $supplierEscaped = addslashes($supplierName);
            $query->whereRaw("INSTR(A.supplier_name, '{$supplierEscaped}') > 0");
        }

        if ($searchValue !== '') {
            $searchEscaped = addslashes($searchValue);
            $query->whereRaw(
                "(INSTR(A.order_name, '{$searchEscaped}') > 0
                OR INSTR(A.po_code, '{$searchEscaped}') > 0
                OR EXISTS (
                    SELECT 1
                    FROM purchase_order_items PI
                    WHERE PI.purchase_order_idx = A.idx
                    AND INSTR(PI.order_no, '{$searchEscaped}') > 0
                ))"
            );
        }

        $summaryRows = PurchaseOrderModel::query()
            ->selectRaw("status, COUNT(*) AS order_count, COALESCE(SUM(total_amount), 0) AS total_amount_sum")
            ->groupBy('status')
            ->get()
            ->toArray();

        $summary = [
            'total' => ['count' => 0, 'amount' => 0],
            'status' => [
                'created' => ['label' => '생성', 'count' => 0, 'amount' => 0],
                'downloaded' => ['label' => '다운로드', 'count' => 0, 'amount' => 0],
                'closed' => ['label' => '종료', 'count' => 0, 'amount' => 0],
            ],
        ];

        foreach ($summaryRows as $summaryRow) {
            $rowStatus = trim((string)($summaryRow['status'] ?? ''));
            $count = (int)($summaryRow['order_count'] ?? 0);
            $amount = (float)($summaryRow['total_amount_sum'] ?? 0);

            $summary['total']['count'] += $count;
            $summary['total']['amount'] += $amount;

            if (!isset($summary['status'][$rowStatus])) {
                $summary['status'][$rowStatus] = [
                    'label' => $rowStatus !== '' ? $rowStatus : '미지정',
                    'count' => 0,
                    'amount' => 0,
                ];
            }
            $summary['status'][$rowStatus]['count'] += $count;
            $summary['status'][$rowStatus]['amount'] += $amount;
        }

        $result = $query->paginate($perPage, $page);

        $statusTextMap = [
            'created' => '생성',
            'downloaded' => '다운로드',
            'closed' => '종료',
        ];

        foreach ($result['data'] as &$row) {
            $rowStatus = trim((string)($row['status'] ?? ''));
            $row['status_text'] = $statusTextMap[$rowStatus] ?? ($rowStatus !== '' ? $rowStatus : '-');
        }
        unset($row);

        $result['summary'] = $summary;
        return $result;
    }

    /**
     * 구매대행 발주서 상세 조회
     *
     * @param int $purchaseOrderIdx
     * @return array
     * @throws Exception
     */
    public function getPurchaseOrderDetail(int $purchaseOrderIdx): array
    {
        if ($purchaseOrderIdx < 1) {
            throw new Exception('발주서 번호가 올바르지 않습니다.');
        }

        $purchaseOrder = PurchaseOrderModel::query()
            ->where('idx', '=', $purchaseOrderIdx)
            ->first();
        $purchaseOrder = $purchaseOrder ? $purchaseOrder->toArray() : null;
        if (empty($purchaseOrder)) {
            throw new Exception('발주서를 찾을 수 없습니다.');
        }

        $items = PurchaseOrderItemModel::query()
            ->where('purchase_order_idx', '=', $purchaseOrderIdx)
            ->orderBy('idx', 'asc')
            ->get()
            ->toArray();

        $goodsIdMap = [];
        foreach ($items as $item) {
            $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
            if ($goodsId > 0) {
                $goodsIdMap[$goodsId] = true;
            }
        }

        $thumbMap = [];
        if (!empty($goodsIdMap)) {
            $goodsRows = GodoOrderGoodsModel::query()
                ->select(['idx', 'thumb_image_url'])
                ->whereIn('idx', array_keys($goodsIdMap))
                ->get()
                ->toArray();

            foreach ($goodsRows as $goodsRow) {
                $goodsId = (int)($goodsRow['idx'] ?? 0);
                if ($goodsId < 1) {
                    continue;
                }
                $thumbMap[$goodsId] = trim((string)($goodsRow['thumb_image_url'] ?? ''));
            }
        }

        $totalQuantity = 0;
        $totalAmount = 0.0;
        foreach ($items as &$item) {
            $totalQuantity += (int)($item['goods_count'] ?? 0);
            $totalAmount += (float)($item['goods_total_price'] ?? 0);
            $item['option_info_text'] = $this->convertOptionInfoToText((string)($item['option_info'] ?? ''));
            $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
            $item['thumb_image_url'] = $thumbMap[$goodsId] ?? '';
        }
        unset($item);

        return [
            'purchaseOrder' => $purchaseOrder,
            'purchaseOrderItems' => $items,
            'summary' => [
                'order_count' => count($items),
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
            ],
        ];
    }

    /**
     * 구매대행 발주서를 삭제하고 연관 주문상품 상태를 복구 가능 상태로 변경
     *
     * @param int $purchaseOrderIdx
     * @return array
     * @throws Exception
     */
    public function deletePurchaseOrder(int $purchaseOrderIdx): array
    {
        if ($purchaseOrderIdx < 1) {
            throw new Exception('삭제할 발주서 번호가 올바르지 않습니다.');
        }

        $purchaseOrder = PurchaseOrderModel::query()
            ->where('idx', '=', $purchaseOrderIdx)
            ->first();
        $purchaseOrder = $purchaseOrder ? $purchaseOrder->toArray() : null;
        if (empty($purchaseOrder)) {
            throw new Exception('삭제할 발주서를 찾을 수 없습니다.');
        }

        $items = PurchaseOrderItemModel::query()
            ->select(['godo_order_goods_id'])
            ->where('purchase_order_idx', '=', $purchaseOrderIdx)
            ->get()
            ->toArray();

        $goodsIds = [];
        foreach ($items as $item) {
            $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
            if ($goodsId > 0) {
                $goodsIds[$goodsId] = true;
            }
        }
        $goodsIds = array_keys($goodsIds);

        DB::transaction(function () use ($purchaseOrderIdx, $goodsIds) {
            foreach ($goodsIds as $goodsId) {
                GodoOrderGoodsModel::update(
                    ['idx' => (int)$goodsId],
                    [
                        'purchase_status' => '발주서삭제',
                        'purchase_order_idx' => null,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );
            }

            PurchaseOrderModel::where('idx', $purchaseOrderIdx)->delete();
        });

        return [
            'deleted_idx' => $purchaseOrderIdx,
            'message' => '발주서가 삭제되었습니다. 삭제된 상품은 다시 담기가 가능합니다.',
        ];
    }

    /**
     * 구매대행 발주서를 병합한다.
     * - 같은 공급사 발주서만 병합 가능
     * - 가장 작은 idx를 기준 발주서로 유지
     * - 연관 상품(godo_order_goods)의 purchase_order_idx를 기준 발주서로 갱신
     *
     * @param array $purchaseOrderIdxs
     * @return array
     * @throws Exception
     */
    public function mergePurchaseOrders(array $purchaseOrderIdxs): array
    {
        $normalizedIdxs = [];
        foreach ($purchaseOrderIdxs as $idx) {
            $idx = (int)$idx;
            if ($idx > 0) {
                $normalizedIdxs[$idx] = true;
            }
        }
        $normalizedIdxs = array_keys($normalizedIdxs);
        sort($normalizedIdxs);

        if (count($normalizedIdxs) < 2) {
            throw new Exception('병합할 발주서를 2건 이상 선택해 주세요.');
        }

        $orders = PurchaseOrderModel::query()
            ->select(['idx', 'supplier_name'])
            ->whereIn('idx', $normalizedIdxs)
            ->get()
            ->toArray();

        if (count($orders) !== count($normalizedIdxs)) {
            throw new Exception('선택한 발주서 중 일부를 찾을 수 없습니다.');
        }

        $supplierMap = [];
        foreach ($orders as $order) {
            $supplierName = trim((string)($order['supplier_name'] ?? ''));
            $supplierName = $supplierName !== '' ? $supplierName : '(공급사 미지정)';
            $supplierMap[$supplierName] = true;
        }
        if (count($supplierMap) > 1) {
            throw new Exception('같은 공급사 발주서만 병합할 수 있습니다.');
        }

        $targetIdx = (int)$normalizedIdxs[0];
        $sourceIdxs = array_slice($normalizedIdxs, 1);
        $now = date('Y-m-d H:i:s');

        DB::transaction(function () use ($targetIdx, $sourceIdxs, $now) {
            $sourceItems = PurchaseOrderItemModel::query()
                ->select(['idx', 'godo_order_goods_id'])
                ->whereIn('purchase_order_idx', $sourceIdxs)
                ->get()
                ->toArray();

            $goodsIds = [];
            foreach ($sourceItems as $item) {
                $itemIdx = (int)($item['idx'] ?? 0);
                if ($itemIdx < 1) {
                    continue;
                }
                PurchaseOrderItemModel::update(
                    ['idx' => $itemIdx],
                    [
                        'purchase_order_idx' => $targetIdx,
                        'updated_at' => $now,
                    ]
                );

                $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
                if ($goodsId > 0) {
                    $goodsIds[$goodsId] = true;
                }
            }

            foreach (array_keys($goodsIds) as $goodsId) {
                GodoOrderGoodsModel::update(
                    ['idx' => (int)$goodsId],
                    [
                        'purchase_status' => '발주서생성',
                        'purchase_order_idx' => $targetIdx,
                        'updated_at' => $now,
                    ]
                );
            }

            foreach ($sourceIdxs as $sourceIdx) {
                PurchaseOrderModel::where('idx', (int)$sourceIdx)->delete();
            }

            $targetItems = PurchaseOrderItemModel::query()
                ->select(['goods_count', 'goods_total_price'])
                ->where('purchase_order_idx', '=', $targetIdx)
                ->get()
                ->toArray();

            $itemCount = count($targetItems);
            $totalQuantity = 0;
            $totalAmount = 0.0;
            foreach ($targetItems as $targetItem) {
                $totalQuantity += (int)($targetItem['goods_count'] ?? 0);
                $totalAmount += (float)($targetItem['goods_total_price'] ?? 0);
            }

            PurchaseOrderModel::update(
                ['idx' => $targetIdx],
                [
                    'item_count' => $itemCount,
                    'total_quantity' => $totalQuantity,
                    'total_amount' => number_format($totalAmount, 2, '.', ''),
                    'updated_at' => $now,
                ]
            );
        });

        return [
            'target_idx' => $targetIdx,
            'message' => '발주서 병합이 완료되었습니다.',
        ];
    }

    /**
     * option_info(JSON) 를 사람이 읽을 수 있는 텍스트로 변환
     *
     * @param string $optionInfoJson
     * @return string
     */
    private function convertOptionInfoToText(string $optionInfoJson): string
    {
        $optionInfoJson = trim($optionInfoJson);
        if ($optionInfoJson === '') {
            return '';
        }

        $decoded = json_decode($optionInfoJson, true);
        if (!is_array($decoded) || empty($decoded)) {
            return $optionInfoJson;
        }

        $parts = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (isset($row[0]) && isset($row[1])) {
                $parts[] = trim((string)$row[0]) . ':' . trim((string)$row[1]);
                continue;
            }
            $flat = [];
            foreach ($row as $value) {
                $flat[] = trim((string)$value);
            }
            $flat = array_values(array_filter($flat, function ($value) {
                return $value !== '';
            }));
            if (!empty($flat)) {
                $parts[] = implode('/', $flat);
            }
        }

        return implode(' | ', $parts);
    }
}

