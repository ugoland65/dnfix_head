<?php
namespace App\Services;

use App\Classes\DB;
use App\Models\GodoOrderGoodsModel;
use App\Models\GodoOrderModel;
use App\Models\ProductPartnerModel;

class GodoOrderMobeMatchService
{
    /**
     * 모브 주문 API 데이터를 고도몰 주문상품에 매칭해 저장한다.
     *
     * 매칭 기준:
     * - godo_orders.receiver_name = API shipping.recipient_name
     * - godo_orders.receiver_cell_phone = API shipping.mobile_phone (하이픈 제외)
     * - godo_order_goods.product_partner_id → prd_partner.supplier_prd_pk = API items.mobe_product_seq
     *
     * @param array $data ordered_at_start 등 API 조회 조건
     * @return array
     */
    public function matchMobeOrders(array $data = []): array
    {
        $orderGoodsSnos = $this->normalizeOrderGoodsSnos($data['order_goods_snos'] ?? []);
        if (empty($orderGoodsSnos)) {
            throw new \InvalidArgumentException('저장된 주문상품 번호가 없습니다.');
        }

        $mobeQuery = $data;
        unset($mobeQuery['order_goods_snos']);
        $mobeResponse = (new ProductPartnerApiService())->getMobeOrders($mobeQuery);
        $mobeOrders = $mobeResponse['data']['orders'] ?? [];
        if (!is_array($mobeOrders)) {
            throw new \Exception('모브 구매내역 응답에 orders 데이터가 없습니다.');
        }

        $matchedCount = 0;
        $shippingCompletedCount = 0;
        $unmatchedCount = 0;
        $invalidCount = 0;
        $now = date('Y-m-d H:i:s');

        DB::transaction(function () use (
            $mobeOrders,
            $orderGoodsSnos,
            $now,
            &$matchedCount,
            &$shippingCompletedCount,
            &$unmatchedCount,
            &$invalidCount
        ) {
            foreach ($mobeOrders as $mobeOrder) {
                if (!is_array($mobeOrder)) {
                    $invalidCount++;
                    continue;
                }

                $shipping = is_array($mobeOrder['shipping'] ?? null) ? $mobeOrder['shipping'] : [];
                $recipientName = trim((string)($shipping['recipient_name'] ?? ''));
                $recipientCellPhone = $this->normalizePhone($shipping['mobile_phone'] ?? '');
                $items = $mobeOrder['items'] ?? [];
                if ($recipientName === '' || $recipientCellPhone === '' || !is_array($items)) {
                    $invalidCount++;
                    continue;
                }

                $godoOrders = GodoOrderModel::query()
                    ->select(['idx', 'receiver_cell_phone'])
                    ->where('receiver_name', $recipientName)
                    ->get()
                    ->toArray();

                $godoOrderIds = [];
                foreach ($godoOrders as $godoOrder) {
                    if ($this->normalizePhone($godoOrder['receiver_cell_phone'] ?? '') !== $recipientCellPhone) {
                        continue;
                    }
                    $godoOrderId = (int)($godoOrder['idx'] ?? 0);
                    if ($godoOrderId > 0) {
                        $godoOrderIds[$godoOrderId] = true;
                    }
                }

                if (empty($godoOrderIds)) {
                    $unmatchedCount += count($items);
                    continue;
                }

                foreach ($items as $mobeItem) {
                    if (!is_array($mobeItem)) {
                        $invalidCount++;
                        continue;
                    }

                    $mobeProductSeq = (int)($mobeItem['mobe_product_seq'] ?? 0);
                    if ($mobeProductSeq < 1) {
                        $invalidCount++;
                        continue;
                    }

                    // product_partner_id에는 prd_partner.idx가 저장되므로,
                    // 모브 상품번호는 prd_partner.supplier_prd_pk를 통해 변환한다.
                    $productPartnerRows = ProductPartnerModel::query()
                        ->select(['idx'])
                        ->where('supplier_prd_pk', $mobeProductSeq)
                        ->get()
                        ->toArray();
                    $productPartnerIds = [];
                    foreach ($productPartnerRows as $productPartnerRow) {
                        $productPartnerId = (int)($productPartnerRow['idx'] ?? 0);
                        if ($productPartnerId > 0) {
                            $productPartnerIds[] = $productPartnerId;
                        }
                    }
                    if (empty($productPartnerIds)) {
                        $unmatchedCount++;
                        continue;
                    }

                    $matchedGoodsRows = GodoOrderGoodsModel::query()
                        ->select(['idx'])
                        ->whereIn('godo_order_idx', array_keys($godoOrderIds))
                        ->whereIn('order_goods_sno', $orderGoodsSnos)
                        ->whereIn('product_partner_id', $productPartnerIds)
                        ->get()
                        ->toArray();

                    if (empty($matchedGoodsRows)) {
                        $unmatchedCount++;
                        continue;
                    }

                    $shippingStatus = trim((string)($mobeItem['shipping_status'] ?? ''));
                    $mobeOrderData = [
                        'mobe_order_seq' => $mobeOrder['mobe_order_seq'] ?? null,
                        'order_number' => $mobeOrder['order_number'] ?? null,
                        'ordered_at' => $mobeOrder['ordered_at'] ?? null,
                        'payment_method' => $mobeOrder['payment_method'] ?? null,
                        'product_subtotal' => $mobeOrder['product_subtotal'] ?? null,
                        'shipping_fee_total' => $mobeOrder['shipping_fee_total'] ?? null,
                        'payment_total' => $mobeOrder['payment_total'] ?? null,
                        'shipping_status' => $shippingStatus,
                        'carrier_name' => $mobeItem['carrier_name'] ?? null,
                        'tracking_number' => $mobeItem['tracking_number'] ?? null,
                        'matched_at' => $now,
                    ];

                    foreach ($matchedGoodsRows as $matchedGoods) {
                        $godoOrderGoodsId = (int)($matchedGoods['idx'] ?? 0);
                        if ($godoOrderGoodsId < 1) {
                            continue;
                        }

                        $updateData = [
                            'mobe_order_data' => json_encode($mobeOrderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                            'mobe_matched_at' => $now,
                        ];
                        if ($shippingStatus === '배송완료') {
                            $updateData['purchase_status'] = '공급사배송완료';
                            $shippingCompletedCount++;
                        }

                        GodoOrderGoodsModel::where('idx', $godoOrderGoodsId)->update($updateData);
                        $matchedCount++;
                    }
                }
            }
        });

        return [
            'mobe_order_count' => count($mobeOrders),
            'matched_count' => $matchedCount,
            'shipping_completed_count' => $shippingCompletedCount,
            'unmatched_count' => $unmatchedCount,
            'invalid_count' => $invalidCount,
        ];
    }

    private function normalizePhone($value): string
    {
        return str_replace('-', '', trim((string)$value));
    }

    private function normalizeOrderGoodsSnos($values): array
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $normalizedMap = [];
        foreach ($values as $value) {
            $value = trim((string)$value);
            if ($value === '' || !ctype_digit($value)) {
                continue;
            }
            $value = ltrim($value, '0');
            $normalizedMap[$value === '' ? '0' : $value] = true;
        }

        return array_keys($normalizedMap);
    }
}
