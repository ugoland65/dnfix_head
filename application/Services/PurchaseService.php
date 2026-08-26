<?php

namespace App\Services;

use Exception;
use App\Classes\DB;
use App\Core\AuthAdmin;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\GodoOrderModel;
use App\Models\GodoOrderGoodsModel;
use App\Models\ProductModel;

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
                'payment_requested' => ['label' => '결제요청 등록 완료', 'count' => 0, 'amount' => 0],
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
            'payment_requested' => '결제요청 등록 완료',
            'downloaded' => '다운로드',
            'closed' => '종료',
        ];

        foreach ($result['data'] as &$row) {
            $rowStatus = trim((string)($row['status'] ?? ''));
            $row['status_text'] = $statusTextMap[$rowStatus] ?? ($rowStatus !== '' ? $rowStatus : '-');
        }
        unset($row);

        $purchaseOrderIdxs = array_values(array_filter(array_map(static function ($row) {
            return (int)($row['idx'] ?? 0);
        }, $result['data']), static function ($idx) {
            return $idx > 0;
        }));
        $orderNosByPurchaseOrderIdx = [];
        if (!empty($purchaseOrderIdxs)) {
            $orderNoRows = PurchaseOrderItemModel::query()
                ->select(['purchase_order_idx', 'order_no'])
                ->whereIn('purchase_order_idx', $purchaseOrderIdxs)
                ->get()
                ->toArray();
            foreach ($orderNoRows as $orderNoRow) {
                $purchaseOrderIdx = (int)($orderNoRow['purchase_order_idx'] ?? 0);
                $orderNo = trim((string)($orderNoRow['order_no'] ?? ''));
                if ($purchaseOrderIdx <= 0 || $orderNo === '') {
                    continue;
                }
                $orderNosByPurchaseOrderIdx[$purchaseOrderIdx][$orderNo] = $orderNo;
            }
        }
        foreach ($result['data'] as &$row) {
            $purchaseOrderIdx = (int)($row['idx'] ?? 0);
            $row['godo_order_nos'] = array_values($orderNosByPurchaseOrderIdx[$purchaseOrderIdx] ?? []);
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
        $statusTextMap = [
            'created' => '생성',
            'payment_requested' => '결제요청 등록 완료',
            'downloaded' => '다운로드',
            'closed' => '종료',
        ];
        $purchaseOrderStatus = trim((string)($purchaseOrder['status'] ?? ''));
        $purchaseOrder['status_text'] = $statusTextMap[$purchaseOrderStatus]
            ?? ($purchaseOrderStatus !== '' ? $purchaseOrderStatus : '-');

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
        $productIdByGoodsId = [];
        $productMap = [];
        if (!empty($goodsIdMap)) {
            $goodsRows = GodoOrderGoodsModel::query()
                ->select(['idx', 'intranet_goods_id', 'thumb_image_url'])
                ->whereIn('idx', array_keys($goodsIdMap))
                ->get()
                ->toArray();

            $productIdMap = [];
            foreach ($goodsRows as $goodsRow) {
                $goodsId = (int)($goodsRow['idx'] ?? 0);
                if ($goodsId < 1) {
                    continue;
                }
                $thumbMap[$goodsId] = trim((string)($goodsRow['thumb_image_url'] ?? ''));
                $productId = (int)($goodsRow['intranet_goods_id'] ?? 0);
                if ($productId > 0) {
                    $productIdByGoodsId[$goodsId] = $productId;
                    $productIdMap[$productId] = true;
                }
            }

            if (!empty($productIdMap)) {
                $productRows = ProductModel::query()
                    ->select(['CD_IDX', 'CD_NAME', 'CD_IMG', 'img_mode', 'cd_sale_price', 'cd_cost_price'])
                    ->whereIn('CD_IDX', array_keys($productIdMap))
                    ->get()
                    ->toArray();

                foreach ($productRows as $productRow) {
                    $productId = (int)($productRow['CD_IDX'] ?? 0);
                    if ($productId > 0) {
                        $productMap[$productId] = [
                            'CD_IDX' => $productId,
                            'CD_NAME' => (string)($productRow['CD_NAME'] ?? ''),
                            'CD_IMG' => (string)($productRow['CD_IMG'] ?? ''),
                            'img_mode' => (string)($productRow['img_mode'] ?? 'this'),
                            'cd_sale_price' => (float)($productRow['cd_sale_price'] ?? 0),
                            'cd_cost_price' => (float)($productRow['cd_cost_price'] ?? 0),
                        ];
                    }
                }
            }
        }

        $totalQuantity = 0;
        $totalAmount = 0.0;
        $totalCostAmount = 0.0;
        foreach ($items as &$item) {
            $goodsCount = (int)($item['goods_count'] ?? 0);
            $totalQuantity += $goodsCount;
            $item['option_info_text'] = $this->convertOptionInfoToText((string)($item['option_info'] ?? ''));
            $item['option_additional_price'] = $this->getOptionAdditionalPrice(
                (string)($item['option_info'] ?? '')
            );
            $item['goods_price_with_option'] = (float)($item['goods_price'] ?? 0)
                + (float)$item['option_additional_price'];
            $item['goods_total_price_with_option'] = (float)$item['goods_price_with_option'] * $goodsCount;
            $totalAmount += (float)$item['goods_total_price_with_option'];
            $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
            $item['thumb_image_url'] = $thumbMap[$goodsId] ?? '';
            $productId = $productIdByGoodsId[$goodsId] ?? 0;
            $item['Product'] = $productId > 0 ? ($productMap[$productId] ?? null) : null;
            if (is_array($item['Product'])) {
                $totalCostAmount += (float)($item['Product']['cd_cost_price'] ?? 0)
                    * (int)($item['goods_count'] ?? 0);
            }
        }
        unset($item);

        $purchaseBaseAmount = $purchaseOrder['purchase_base_amount'] ?? null;
        if ($purchaseBaseAmount === null || $purchaseBaseAmount === '') {
            $purchaseBaseAmount = $totalCostAmount;
        }
        $purchaseBaseAmount = (float)$purchaseBaseAmount;

        $additionalCosts = json_decode((string)($purchaseOrder['additional_costs_json'] ?? ''), true);
        if (!is_array($additionalCosts)) {
            $additionalCosts = [];
        }
        $normalizedAdditionalCosts = [];
        $additionalCostTotal = 0.0;
        foreach ($additionalCosts as $additionalCost) {
            if (!is_array($additionalCost)) {
                continue;
            }
            $reason = trim((string)($additionalCost['reason'] ?? ''));
            $amount = (float)($additionalCost['amount'] ?? 0);
            if ($reason === '' && $amount == 0.0) {
                continue;
            }
            $normalizedAdditionalCosts[] = [
                'reason' => $reason,
                'amount' => $amount,
            ];
            $additionalCostTotal += $amount;
        }
        $purchaseFinalAmount = $purchaseBaseAmount + $additionalCostTotal;

        return [
            'purchaseOrder' => $purchaseOrder,
            'purchaseOrderItems' => $items,
            'summary' => [
                'order_count' => count($items),
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
                'total_cost_amount' => $totalCostAmount,
            ],
            'amountCalculation' => [
                'base_amount' => $purchaseBaseAmount,
                'additional_costs' => $normalizedAdditionalCosts,
                'additional_cost_total' => $additionalCostTotal,
                'final_amount' => $purchaseFinalAmount,
            ],
        ];
    }

    /**
     * 주문번호를 동기화하고 발주서에 추가할 수 있는 주문상품을 조회한다.
     *
     * @param int $purchaseOrderIdx
     * @param string $orderNo
     * @return array
     * @throws Exception
     */
    public function getGodoOrderGoodsForPurchase(int $purchaseOrderIdx, string $orderNo): array
    {
        if ($purchaseOrderIdx < 0) {
            throw new Exception('발주서 번호가 올바르지 않습니다.');
        }

        $orderNo = trim($orderNo);
        if ($orderNo === '') {
            throw new Exception('주문번호를 입력해 주세요.');
        }

        if ($purchaseOrderIdx > 0) {
            $purchaseOrderExists = PurchaseOrderModel::query()
                ->where('idx', '=', $purchaseOrderIdx)
                ->first();
            if (!$purchaseOrderExists) {
                throw new Exception('발주서를 찾을 수 없습니다.');
            }
        }

        // 로컬 저장 여부와 관계없이 항상 고도몰 최신 주문정보를 조회해 동기화한다.
        $orderDetail = (new GodoApiService())->getGodoOrderInfo($orderNo);
        if (!is_array($orderDetail)) {
            throw new Exception('고도몰 주문 상세 응답이 올바르지 않습니다.');
        }

        (new GodoOrderSyncService())->syncOrderDetail($orderDetail);

        $godoOrder = GodoOrderModel::query()
            ->where('order_no', '=', $orderNo)
            ->first();

        if (!$godoOrder) {
            throw new Exception('주문 동기화 후 저장된 주문을 찾을 수 없습니다.');
        }

        $goodsRows = GodoOrderGoodsModel::query()
            ->where('godo_order_idx', '=', (int)$godoOrder->idx)
            ->orderBy('idx', 'asc')
            ->get()
            ->toArray();

        foreach ($goodsRows as &$goodsRow) {
            $assignedPurchaseOrderIdx = (int)($goodsRow['purchase_order_idx'] ?? 0);
            $goodsRow['option_info_text'] = $this->convertOptionInfoToText(
                (string)($goodsRow['option_info'] ?? '')
            );
            $goodsRow['option_additional_price'] = $this->getOptionAdditionalPrice(
                (string)($goodsRow['option_info'] ?? '')
            );
            $goodsRow['goods_price_with_option'] = (float)($goodsRow['goods_price'] ?? 0)
                + (float)$goodsRow['option_additional_price'];
            $goodsRow['selection_state'] = 'available';
            $goodsRow['selection_message'] = '';

            if ($purchaseOrderIdx > 0 && $assignedPurchaseOrderIdx === $purchaseOrderIdx) {
                $goodsRow['selection_state'] = 'added_current';
                $goodsRow['selection_message'] = '현재 발주서에 추가됨';
            } elseif ($assignedPurchaseOrderIdx > 0) {
                $goodsRow['selection_state'] = 'assigned_other';
                $goodsRow['selection_message'] = '다른 발주서에 추가됨';
            }
        }
        unset($goodsRow);

        return [
            'order_no' => $orderNo,
            'was_synced' => true,
            'goods' => $goodsRows,
        ];
    }

    /**
     * 선택한 고도몰 주문상품을 기존 발주서에 추가한다.
     *
     * @param int $purchaseOrderIdx
     * @param array $goodsIds
     * @return array
     * @throws Exception
     */
    public function addGodoOrderGoodsToPurchase(int $purchaseOrderIdx, array $goodsIds): array
    {
        if ($purchaseOrderIdx < 1) {
            throw new Exception('발주서 번호가 올바르지 않습니다.');
        }

        $normalizedGoodsIds = [];
        foreach ($goodsIds as $goodsId) {
            $goodsId = (int)$goodsId;
            if ($goodsId > 0) {
                $normalizedGoodsIds[$goodsId] = true;
            }
        }
        $normalizedGoodsIds = array_keys($normalizedGoodsIds);
        if (empty($normalizedGoodsIds)) {
            throw new Exception('추가할 주문상품을 선택해 주세요.');
        }

        $purchaseOrder = PurchaseOrderModel::query()
            ->where('idx', '=', $purchaseOrderIdx)
            ->first();
        if (!$purchaseOrder) {
            throw new Exception('발주서를 찾을 수 없습니다.');
        }

        $goodsRows = GodoOrderGoodsModel::query()
            ->whereIn('idx', $normalizedGoodsIds)
            ->get()
            ->toArray();
        if (count($goodsRows) !== count($normalizedGoodsIds)) {
            throw new Exception('선택한 주문상품 중 일부를 찾을 수 없습니다.');
        }

        $alreadyAddedRows = PurchaseOrderItemModel::query()
            ->select(['godo_order_goods_id', 'purchase_order_idx'])
            ->whereIn('godo_order_goods_id', $normalizedGoodsIds)
            ->get()
            ->toArray();
        if (!empty($alreadyAddedRows)) {
            throw new Exception('선택한 상품 중 이미 발주서에 추가된 상품이 있습니다. 새로고침 후 다시 선택해 주세요.');
        }

        $orderNos = [];
        foreach ($goodsRows as $goodsRow) {
            $assignedPurchaseOrderIdx = (int)($goodsRow['purchase_order_idx'] ?? 0);
            if ($assignedPurchaseOrderIdx > 0) {
                throw new Exception('선택한 상품 중 이미 다른 발주서에 추가된 상품이 있습니다.');
            }
            $orderNo = trim((string)($goodsRow['order_no'] ?? ''));
            if ($orderNo !== '') {
                $orderNos[$orderNo] = true;
            }
        }
        if (empty($orderNos)) {
            throw new Exception('선택한 상품의 주문번호를 확인할 수 없습니다.');
        }

        $orderRows = GodoOrderModel::query()
            ->whereIn('order_no', array_keys($orderNos))
            ->get()
            ->toArray();
        $orderMap = [];
        foreach ($orderRows as $orderRow) {
            $orderMap[(string)($orderRow['order_no'] ?? '')] = $orderRow;
        }

        $createdBy = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $createdName = trim((string)(AuthAdmin::getSession('sess_name') ?? ''));
        $now = date('Y-m-d H:i:s');

        DB::transaction(function () use (
            $purchaseOrderIdx,
            $goodsRows,
            $orderMap,
            $createdBy,
            $createdName,
            $now
        ) {
            foreach ($goodsRows as $goodsRow) {
                $orderNo = trim((string)($goodsRow['order_no'] ?? ''));
                $orderRow = $orderMap[$orderNo] ?? [];
                $goodsCount = max(1, (int)($goodsRow['goods_count'] ?? 1));
                $goodsPrice = (float)($goodsRow['goods_price'] ?? 0);
                $goodsTotalPrice = (float)($goodsRow['goods_total_price'] ?? 0);
                if ($goodsTotalPrice <= 0) {
                    $goodsTotalPrice = $goodsPrice * $goodsCount;
                }

                PurchaseOrderItemModel::create([
                    'purchase_order_idx' => $purchaseOrderIdx,
                    'godo_order_goods_id' => (int)($goodsRow['idx'] ?? 0),
                    'order_goods_sno' => (string)($goodsRow['order_goods_sno'] ?? ''),
                    'order_no' => $orderNo,
                    'goods_no' => (int)($goodsRow['goods_no'] ?? 0),
                    'goods_name' => (string)($goodsRow['goods_name'] ?? ''),
                    'option_info' => (string)($goodsRow['option_info'] ?? ''),
                    'scm_no' => (int)($goodsRow['scm_no'] ?? 0),
                    'scm_name' => (string)($goodsRow['scm_name'] ?? ''),
                    'goods_count' => $goodsCount,
                    'goods_price' => number_format($goodsPrice, 2, '.', ''),
                    'goods_total_price' => number_format($goodsTotalPrice, 2, '.', ''),
                    'receiver_name' => (string)($orderRow['receiver_name'] ?? ''),
                    'receiver_phone' => (string)($orderRow['receiver_phone'] ?? ''),
                    'receiver_cell_phone' => (string)($orderRow['receiver_cell_phone'] ?? ''),
                    'receiver_zonecode' => (string)($orderRow['receiver_zonecode'] ?? ''),
                    'receiver_address' => (string)($orderRow['receiver_address'] ?? ''),
                    'receiver_address_sub' => (string)($orderRow['receiver_address_sub'] ?? ''),
                    'order_memo' => (string)($orderRow['order_memo'] ?? ''),
                    'created_by' => $createdBy > 0 ? $createdBy : null,
                    'created_name' => $createdName !== '' ? $createdName : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                GodoOrderGoodsModel::update(
                    ['idx' => (int)($goodsRow['idx'] ?? 0)],
                    [
                        'purchase_status' => '발주서생성',
                        'purchase_order_idx' => $purchaseOrderIdx,
                        'purchase_order_date' => $now,
                        'purchase_order_admin' => $createdBy > 0 ? $createdBy : null,
                        'purchase_order_admin_name' => $createdName !== '' ? $createdName : null,
                        'updated_at' => $now,
                    ]
                );
            }

            $items = PurchaseOrderItemModel::query()
                ->select(['goods_count', 'goods_total_price'])
                ->where('purchase_order_idx', '=', $purchaseOrderIdx)
                ->get()
                ->toArray();
            $totalQuantity = 0;
            $totalAmount = 0.0;
            foreach ($items as $item) {
                $totalQuantity += (int)($item['goods_count'] ?? 0);
                $totalAmount += (float)($item['goods_total_price'] ?? 0);
            }

            PurchaseOrderModel::update(
                ['idx' => $purchaseOrderIdx],
                [
                    'item_count' => count($items),
                    'total_quantity' => $totalQuantity,
                    'total_amount' => number_format($totalAmount, 2, '.', ''),
                    'updated_at' => $now,
                ]
            );
        });

        return [
            'added_count' => count($goodsRows),
            'message' => count($goodsRows) . '개 상품을 발주서에 추가했습니다.',
        ];
    }

    /**
     * 기존 발주서에서 주문상품 한 건을 삭제하고 합계를 다시 계산한다.
     *
     * @param int $purchaseOrderIdx
     * @param int $purchaseOrderItemIdx
     * @return array
     * @throws Exception
     */
    public function deletePurchaseOrderItem(int $purchaseOrderIdx, int $purchaseOrderItemIdx): array
    {
        if ($purchaseOrderIdx < 1 || $purchaseOrderItemIdx < 1) {
            throw new Exception('삭제할 발주상품 정보가 올바르지 않습니다.');
        }

        $purchaseOrder = PurchaseOrderModel::query()
            ->where('idx', '=', $purchaseOrderIdx)
            ->first();
        if (!$purchaseOrder) {
            throw new Exception('발주서를 찾을 수 없습니다.');
        }

        $item = PurchaseOrderItemModel::query()
            ->where('idx', '=', $purchaseOrderItemIdx)
            ->where('purchase_order_idx', '=', $purchaseOrderIdx)
            ->first();
        $item = $item ? $item->toArray() : null;
        if (empty($item)) {
            throw new Exception('삭제할 발주상품을 찾을 수 없습니다.');
        }

        $godoOrderGoodsId = (int)($item['godo_order_goods_id'] ?? 0);
        $now = date('Y-m-d H:i:s');

        DB::transaction(function () use (
            $purchaseOrderIdx,
            $purchaseOrderItemIdx,
            $godoOrderGoodsId,
            $now
        ) {
            PurchaseOrderItemModel::where('idx', $purchaseOrderItemIdx)->delete();

            if ($godoOrderGoodsId > 0) {
                GodoOrderGoodsModel::update(
                    [
                        'idx' => $godoOrderGoodsId,
                        'purchase_order_idx' => $purchaseOrderIdx,
                    ],
                    [
                        'purchase_status' => '발주서삭제',
                        'purchase_order_idx' => null,
                        'purchase_order_date' => null,
                        'purchase_order_admin' => null,
                        'purchase_order_admin_name' => null,
                        'updated_at' => $now,
                    ]
                );
            }

            $remainingItems = PurchaseOrderItemModel::query()
                ->select(['goods_count', 'goods_total_price'])
                ->where('purchase_order_idx', '=', $purchaseOrderIdx)
                ->get()
                ->toArray();

            $totalQuantity = 0;
            $totalAmount = 0.0;
            foreach ($remainingItems as $remainingItem) {
                $totalQuantity += (int)($remainingItem['goods_count'] ?? 0);
                $totalAmount += (float)($remainingItem['goods_total_price'] ?? 0);
            }

            PurchaseOrderModel::update(
                ['idx' => $purchaseOrderIdx],
                [
                    'item_count' => count($remainingItems),
                    'total_quantity' => $totalQuantity,
                    'total_amount' => number_format($totalAmount, 2, '.', ''),
                    'updated_at' => $now,
                ]
            );
        });

        return [
            'deleted_item_idx' => $purchaseOrderItemIdx,
            'message' => '발주상품을 삭제했습니다. 해당 상품은 다시 추가할 수 있습니다.',
        ];
    }

    /**
     * 발주 기본금액과 추가비용을 저장한다.
     *
     * @param int $purchaseOrderIdx
     * @param mixed $baseAmount
     * @param mixed $additionalCosts
     * @return array
     * @throws Exception
     */
    public function savePurchaseOrderAmountCalculation(
        int $purchaseOrderIdx,
        $baseAmount,
        $additionalCosts
    ): array {
        if ($purchaseOrderIdx < 1) {
            throw new Exception('발주서 번호가 올바르지 않습니다.');
        }

        $purchaseOrder = PurchaseOrderModel::query()
            ->where('idx', '=', $purchaseOrderIdx)
            ->first();
        if (!$purchaseOrder) {
            throw new Exception('발주서를 찾을 수 없습니다.');
        }

        $baseAmount = str_replace(',', '', trim((string)$baseAmount));
        if ($baseAmount === '' || !is_numeric($baseAmount)) {
            throw new Exception('발주금액을 올바르게 입력해 주세요.');
        }
        $baseAmount = (float)$baseAmount;
        if ($baseAmount < 0) {
            throw new Exception('발주금액은 0원 이상이어야 합니다.');
        }

        if (is_string($additionalCosts)) {
            $additionalCosts = html_entity_decode(
                $additionalCosts,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );
            $decodedAdditionalCosts = json_decode($additionalCosts, true);
            if (!is_array($decodedAdditionalCosts)) {
                throw new Exception('추가비용 데이터를 확인할 수 없습니다.');
            }
            $additionalCosts = $decodedAdditionalCosts;
        }
        if (!is_array($additionalCosts)) {
            $additionalCosts = [];
        }

        $normalizedAdditionalCosts = [];
        $additionalCostTotal = 0.0;
        foreach ($additionalCosts as $additionalCost) {
            if (!is_array($additionalCost)) {
                continue;
            }
            $reason = trim((string)($additionalCost['reason'] ?? ''));
            $amountRaw = str_replace(',', '', trim((string)($additionalCost['amount'] ?? '')));
            if ($reason === '' && ($amountRaw === '' || (float)$amountRaw == 0.0)) {
                continue;
            }
            if ($reason === '') {
                throw new Exception('추가비용 사유를 입력해 주세요.');
            }
            if ($amountRaw === '' || !is_numeric($amountRaw)) {
                throw new Exception('추가비용 금액을 올바르게 입력해 주세요.');
            }
            $amount = (float)$amountRaw;
            if ($amount < 0) {
                throw new Exception('추가비용은 0원 이상이어야 합니다.');
            }

            $normalizedAdditionalCosts[] = [
                'reason' => $reason,
                'amount' => $amount,
            ];
            $additionalCostTotal += $amount;
        }

        $finalAmount = $baseAmount + $additionalCostTotal;
        $encodedAdditionalCosts = json_encode(
            $normalizedAdditionalCosts,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($encodedAdditionalCosts === false) {
            throw new Exception('추가비용 데이터를 저장할 수 없습니다.');
        }

        PurchaseOrderModel::update(
            ['idx' => $purchaseOrderIdx],
            [
                'purchase_base_amount' => number_format($baseAmount, 2, '.', ''),
                'additional_costs_json' => $encodedAdditionalCosts,
                'purchase_final_amount' => number_format($finalAmount, 2, '.', ''),
                'total_amount' => number_format($finalAmount, 2, '.', ''),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        return [
            'base_amount' => $baseAmount,
            'additional_cost_total' => $additionalCostTotal,
            'final_amount' => $finalAmount,
            'additional_costs' => $normalizedAdditionalCosts,
            'message' => '발주서 금액을 저장했습니다.',
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
        if (is_array($decoded) && empty($decoded)) {
            return '';
        }
        if (!is_array($decoded)) {
            return $optionInfoJson;
        }

        $parts = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (isset($row[0]) && isset($row[1])) {
                $optionText = trim((string)$row[0]) . ':' . trim((string)$row[1]);
                $additionalPrice = $row[3] ?? null;
                if ($additionalPrice !== null && $additionalPrice !== '' && is_numeric($additionalPrice)) {
                    $additionalPrice = (float)$additionalPrice;
                    if ($additionalPrice !== 0.0) {
                        $sign = $additionalPrice > 0 ? '+' : '-';
                        $optionText .= '<br>(추가금액 ' . $sign . number_format(abs($additionalPrice)) . '원)';
                    }
                }
                $parts[] = $optionText;
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

    /**
     * option_info(JSON)의 옵션 추가금액 합계
     *
     * @param string $optionInfoJson
     * @return float
     */
    private function getOptionAdditionalPrice(string $optionInfoJson): float
    {
        $decoded = json_decode(trim($optionInfoJson), true);
        if (!is_array($decoded)) {
            return 0.0;
        }

        $additionalPrice = 0.0;
        foreach ($decoded as $row) {
            if (!is_array($row) || !isset($row[3]) || !is_numeric($row[3])) {
                continue;
            }
            $additionalPrice += (float)$row[3];
        }

        return $additionalPrice;
    }
}

