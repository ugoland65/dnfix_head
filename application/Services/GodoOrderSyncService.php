<?php
namespace App\Services;

use Exception;
use App\Classes\DB;
use App\Models\GodoOrderModel;
use App\Models\GodoOrderGoodsModel;

class GodoOrderSyncService
{
    /**
     * 고도몰 주문상품 API 결과를 주문/주문상품 테이블에 동기화한다.
     * - 신규 데이터만 저장 (기존 order_no / order_goods_sno 는 스킵)
     *
     * @param array $orderGoodsApiResponse GodoApiService::getOrderGoodsList() 응답
     * @return array
     * @throws Exception
     */
    public function syncOrderGoodsList(array $orderGoodsApiResponse): array
    {
        $rows = $orderGoodsApiResponse['orderData']['data'] ?? [];
        if (!is_array($rows) || empty($rows)) {
            return [
                'order_created_count' => 0,
                'order_skipped_count' => 0,
                'goods_created_count' => 0,
                'goods_skipped_count' => 0,
                'invalid_count' => 0,
                'message' => '동기화할 주문 데이터가 없습니다.',
            ];
        }

        $now = date('Y-m-d H:i:s');
        $orderRowsMap = [];
        $orderNos = [];
        $goodsSnos = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $orderNo = trim((string)($row['orderNo'] ?? ''));
            if ($orderNo !== '') {
                $orderNos[$orderNo] = true;
                if (!isset($orderRowsMap[$orderNo])) {
                    $orderRowsMap[$orderNo] = [];
                }
                $orderRowsMap[$orderNo][] = $row;
            }

            $goodsSno = $this->normalizeOrderGoodsSno($row['orderGoodsSno'] ?? null);
            if ($goodsSno !== null) {
                $goodsSnos[$goodsSno] = true;
            }
        }

        if (empty($orderNos)) {
            return [
                'order_created_count' => 0,
                'order_skipped_count' => 0,
                'goods_created_count' => 0,
                'goods_skipped_count' => 0,
                'invalid_count' => count($rows),
                'message' => '유효한 주문번호(orderNo)가 없어 동기화를 중단했습니다.',
            ];
        }

        $orderNoList = array_keys($orderNos);
        $goodsSnoList = array_keys($goodsSnos);

        $existingOrderMap = $this->getExistingOrderIdxMap($orderNoList);
        $existingGoodsSnoMap = $this->getExistingGoodsSnoMap($goodsSnoList);

        $orderCreatedCount = 0;
        $orderSkippedCount = 0;
        $goodsCreatedCount = 0;
        $goodsSkippedCount = 0;
        $invalidCount = 0;

        DB::transaction(function () use (
            $orderRowsMap,
            $rows,
            $now,
            &$existingOrderMap,
            &$existingGoodsSnoMap,
            &$orderCreatedCount,
            &$orderSkippedCount,
            &$goodsCreatedCount,
            &$goodsSkippedCount,
            &$invalidCount
        ) {
            // 1) 주문 저장 (신규만)
            foreach ($orderRowsMap as $orderNo => $groupRows) {
                if (isset($existingOrderMap[$orderNo])) {
                    $orderSkippedCount++;
                    continue;
                }

                $orderPayload = $this->buildOrderInsertPayload($orderNo, $groupRows, $now);
                $createdOrder = GodoOrderModel::create($orderPayload);
                $createdOrderId = (int)($createdOrder->idx ?? 0);
                if ($createdOrderId < 1) {
                    throw new Exception('주문 저장 후 PK를 확인할 수 없습니다. order_no: ' . $orderNo);
                }

                $existingOrderMap[$orderNo] = $createdOrderId;
                $orderCreatedCount++;
            }

            // 2) 주문상품 저장 (신규만)
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    $invalidCount++;
                    continue;
                }

                $orderNo = trim((string)($row['orderNo'] ?? ''));
                $orderGoodsSno = $this->normalizeOrderGoodsSno($row['orderGoodsSno'] ?? null);
                if ($orderNo === '' || $orderGoodsSno === null) {
                    $invalidCount++;
                    continue;
                }

                $godoOrderId = (int)($existingOrderMap[$orderNo] ?? 0);
                if ($godoOrderId < 1) {
                    // 안전장치: 주문 FK 누락 시 해당 상품은 저장하지 않고 스킵
                    $invalidCount++;
                    continue;
                }

                $orderGoodsSnoKey = $orderGoodsSno;
                if (isset($existingGoodsSnoMap[$orderGoodsSnoKey])) {
                    $updatePayload = $this->buildOrderGoodsUpdatePayload($godoOrderId, $orderNo, $orderGoodsSno, $row, $now);
                    GodoOrderGoodsModel::update(
                        ['order_goods_sno' => $orderGoodsSno],
                        $updatePayload
                    );
                    $goodsSkippedCount++;
                    continue;
                }

                $goodsPayload = $this->buildOrderGoodsInsertPayload($godoOrderId, $orderNo, $orderGoodsSno, $row, $now);
                GodoOrderGoodsModel::create($goodsPayload);

                $existingGoodsSnoMap[$orderGoodsSnoKey] = true;
                $goodsCreatedCount++;
            }
        });

        return [
            'order_created_count' => $orderCreatedCount,
            'order_skipped_count' => $orderSkippedCount,
            'goods_created_count' => $goodsCreatedCount,
            'goods_skipped_count' => $goodsSkippedCount,
            'invalid_count' => $invalidCount,
            'message' => '고도몰 주문 동기화 완료 (주문 신규 ' . $orderCreatedCount . '건, 주문상품 신규 ' . $goodsCreatedCount . '건)',
        ];
    }

    /**
     * 기존 주문번호 맵 조회
     * @param array $orderNoList
     * @return array<string,int>
     */
    private function getExistingOrderIdxMap(array $orderNoList): array
    {
        if (empty($orderNoList)) {
            return [];
        }

        $rows = GodoOrderModel::query()
            ->select(['idx', 'order_no'])
            ->whereIn('order_no', $orderNoList)
            ->get()
            ->toArray();

        $map = [];
        foreach ($rows as $row) {
            $orderNo = trim((string)($row['order_no'] ?? ''));
            $idx = (int)($row['idx'] ?? 0);
            if ($orderNo !== '' && $idx > 0) {
                $map[$orderNo] = $idx;
            }
        }

        return $map;
    }

    /**
     * 기존 주문상품번호 맵 조회
     * @param array $goodsSnoList
     * @return array<string,bool>
     */
    private function getExistingGoodsSnoMap(array $goodsSnoList): array
    {
        if (empty($goodsSnoList)) {
            return [];
        }

        $rows = GodoOrderGoodsModel::query()
            ->select(['order_goods_sno'])
            ->whereIn('order_goods_sno', $goodsSnoList)
            ->get()
            ->toArray();

        $map = [];
        foreach ($rows as $row) {
            $sno = (string)($row['order_goods_sno'] ?? '');
            if ($sno !== '') {
                $map[$sno] = true;
            }
        }

        return $map;
    }

    /**
     * 주문 insert payload 생성
     * @param string $orderNo
     * @param array $groupRows
     * @param string $now
     * @return array
     */
    private function buildOrderInsertPayload(string $orderNo, array $groupRows, string $now): array
    {
        $first = $groupRows[0] ?? [];
        $totalGoodsCount = 0;
        $goodsKinds = 0;
        $isRefunded = false;
        $isCancelled = false;
        $latestGodoUpdatedAt = null;

        $goodsNoSet = [];
        foreach ($groupRows as $row) {
            $goodsCnt = (int)($row['goodsCnt'] ?? 0);
            $totalGoodsCount += max(0, $goodsCnt);

            $goodsNo = trim((string)($row['goodsNo'] ?? ''));
            $orderGoodsSno = trim((string)($row['orderGoodsSno'] ?? ''));
            $goodsKindKey = $goodsNo !== '' ? 'goodsNo:' . $goodsNo : 'sno:' . $orderGoodsSno;
            if ($goodsKindKey !== 'sno:') {
                $goodsNoSet[$goodsKindKey] = true;
            }

            $refundPrice = (float)($row['refundPrice'] ?? 0);
            if ($refundPrice > 0) {
                $isRefunded = true;
            }

            $status = strtolower(trim((string)($row['orderStatus'] ?? '')));
            if ($status !== '' && strpos($status, 'c') === 0) {
                $isCancelled = true;
            }

            $candidateUpdatedAt = $this->normalizeDateTime(
                $row['orderPaymentDt'] ?? ($row['paymentDt'] ?? ($row['regDt'] ?? null))
            );
            if ($candidateUpdatedAt !== null && ($latestGodoUpdatedAt === null || $candidateUpdatedAt > $latestGodoUpdatedAt)) {
                $latestGodoUpdatedAt = $candidateUpdatedAt;
            }
        }
        $goodsKinds = count($goodsNoSet);

        $settlePrice = $this->normalizeDecimal($first['settlePrice'] ?? 0);
        $refundPriceTotal = $this->normalizeNullableDecimal($first['refundPrice'] ?? null);
        $memberNo = (int)($first['memNo'] ?? 0);
        $memberId = trim((string)($first['memId'] ?? ''));

        return [
            'order_no' => $orderNo,
            'order_status' => $this->toNullableString($first['orderStatus'] ?? null),
            'settle_kind' => $this->toNullableString($first['settleKind'] ?? null),
            'settle_price' => $settlePrice,
            'payment_dt' => $this->normalizeDateTime($first['paymentDt'] ?? null),
            'order_payment_dt' => $this->normalizeDateTime($first['orderPaymentDt'] ?? null),
            'order_reg_dt' => $this->normalizeDateTime($first['regDt'] ?? null),
            'order_name' => $this->toNullableString($first['orderName'] ?? null),
            'order_phone' => $this->toNullableString($first['phone'] ?? null),
            'order_cell_phone' => $this->toNullableString($first['cellPhone'] ?? null),
            'receiver_name' => $this->toNullableString($first['receiverName'] ?? null),
            'receiver_phone' => $this->toNullableString($first['receiverPhone'] ?? null),
            'receiver_cell_phone' => $this->toNullableString($first['receiverCellPhone'] ?? null),
            'receiver_zipcode' => $this->toNullableString($first['receiverZipcode'] ?? null),
            'receiver_zonecode' => $this->toNullableString($first['receiverZonecode'] ?? null),
            'receiver_address' => $this->toNullableString($first['receiverAddress'] ?? null),
            'receiver_address_sub' => $this->toNullableString($first['receiverAddressSub'] ?? null),
            'order_memo' => $this->toNullableString($first['orderMemo'] ?? null),
            'member_no' => $memberNo > 0 ? $memberNo : null,
            'member_id' => $memberId !== '' ? $memberId : null,
            'member_name' => $this->toNullableString($first['memNm'] ?? null),
            'member_group_name' => $this->toNullableString($first['groupNm'] ?? null),
            'refund_price' => $refundPriceTotal,
            'goods_count' => $goodsKinds,
            'total_goods_count' => $totalGoodsCount,
            'is_member' => ($memberNo > 0 || $memberId !== '') ? 1 : 0,
            'is_refunded' => $isRefunded ? 1 : 0,
            'is_cancelled' => $isCancelled ? 1 : 0,
            'api_sync_status' => 'success',
            'api_sync_message' => null,
            'api_synced_at' => $now,
            'godo_updated_at' => $latestGodoUpdatedAt,
            'raw_data' => json_encode([
                'orderNo' => $orderNo,
                'items' => $groupRows,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }

    /**
     * 주문상품 insert payload 생성
     * @param int $godoOrderId
     * @param string $orderNo
     * @param array $row
     * @param string $now
     * @return array
     */
    private function buildOrderGoodsInsertPayload(int $godoOrderId, string $orderNo, string $orderGoodsSno, array $row, string $now): array
    {
        $goodsCount = max(1, (int)($row['goodsCnt'] ?? 1));
        $goodsPrice = $this->normalizeDecimal($row['goodsPrice'] ?? 0);
        $goodsTotalPrice = number_format(((float)$goodsPrice * (float)$goodsCount), 2, '.', '');
        $refundPrice = $this->normalizeNullableDecimal($row['refundPrice'] ?? null);
        $status = strtolower(trim((string)($row['orderStatus'] ?? '')));

        $productPartnerId = null;
        if (isset($row['ProductPartner']) && is_array($row['ProductPartner'])) {
            $productPartnerId = (int)($row['ProductPartner']['idx'] ?? 0);
            if ($productPartnerId < 1) {
                $productPartnerId = null;
            }
        }

        $intranetGoodsId = null;
        if (isset($row['Product']) && is_array($row['Product'])) {
            $intranetGoodsId = (int)($row['Product']['CD_IDX'] ?? 0);
            if ($intranetGoodsId < 1) {
                $intranetGoodsId = null;
            }
        }

        return [
            'godo_order_idx' => $godoOrderId,
            'order_no' => $orderNo,
            'order_goods_sno' => $orderGoodsSno,
            'goods_no' => $this->normalizeNullableInt($row['goodsNo'] ?? null),
            'goods_cd' => $this->toNullableString($row['goodsCd'] ?? null),
            'goods_name' => (string)($row['goodsNm'] ?? ''),
            'order_status' => $this->toNullableString($row['orderStatus'] ?? null),
            'goods_count' => $goodsCount,
            'goods_price' => $goodsPrice,
            'goods_total_price' => $goodsTotalPrice,
            'option_price' => '0.00',
            'discount_price' => '0.00',
            'refund_price' => $refundPrice,
            'option_info' => json_encode($row['optionInfo'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'thumb_image_url' => $this->toNullableString($row['thumbImageUrl'] ?? null),
            'scm_no' => $this->normalizeNullableInt($row['scmNo'] ?? null),
            'scm_name' => $this->toNullableString($row['scmName'] ?? null),
            'product_partner_id' => $productPartnerId,
            'intranet_goods_id' => $intranetGoodsId,
            'user_handle_mode' => $this->toNullableString($row['userHandleMode'] ?? null),
            'user_handle_fl' => $this->toNullableString($row['userHandleFl'] ?? null),
            'is_refunded' => ((float)($row['refundPrice'] ?? 0) > 0) ? 1 : 0,
            'is_cancelled' => ($status !== '' && strpos($status, 'c') === 0) ? 1 : 0,
            'api_sync_status' => 'success',
            'api_sync_message' => null,
            'api_synced_at' => $now,
            'godo_updated_at' => $this->normalizeDateTime(
                $row['orderPaymentDt'] ?? ($row['paymentDt'] ?? ($row['regDt'] ?? null))
            ),
            'raw_data' => json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }

    /**
     * 기존 주문상품 업데이트 payload 생성
     * - 주문 동기화 시 변경 가능한 값만 갱신
     *
     * @param int $godoOrderId
     * @param string $orderNo
     * @param array $row
     * @param string $now
     * @return array
     */
    private function buildOrderGoodsUpdatePayload(int $godoOrderId, string $orderNo, string $orderGoodsSno, array $row, string $now): array
    {
        $payload = $this->buildOrderGoodsInsertPayload($godoOrderId, $orderNo, $orderGoodsSno, $row, $now);
        unset($payload['order_goods_sno']);
        unset($payload['created_at']);
        return $payload;
    }

    /**
     * orderGoodsSno를 조회/저장 키로 사용할 수 있게 정규화한다.
     * - 숫자만 허용
     * - 빈값/비정상값은 null 반환
     *
     * @param mixed $value
     * @return string|null
     */
    private function normalizeOrderGoodsSno($value)
    {
        $value = trim((string)$value);
        if ($value === '' || !ctype_digit($value)) {
            return null;
        }
        $normalized = ltrim($value, '0');
        return $normalized === '' ? '0' : $normalized;
    }

    /**
     * nullable 정수 변환
     * @param mixed $value
     * @return int|null
     */
    private function normalizeNullableInt($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_numeric($value)) {
            return null;
        }
        return (int)$value;
    }

    /**
     * decimal(15,2) 문자열 변환
     * @param mixed $value
     * @return string
     */
    private function normalizeDecimal($value): string
    {
        if ($value === null || $value === '') {
            return '0.00';
        }
        return number_format((float)$value, 2, '.', '');
    }

    /**
     * nullable decimal(15,2) 문자열 변환
     * @param mixed $value
     * @return string|null
     */
    private function normalizeNullableDecimal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        return number_format((float)$value, 2, '.', '');
    }

    /**
     * 빈 문자열을 null로 정규화
     * @param mixed $value
     * @return string|null
     */
    private function toNullableString($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    /**
     * 일시 문자열 정규화 (Y-m-d H:i:s)
     * @param mixed $value
     * @return string|null
     */
    private function normalizeDateTime($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $time = strtotime((string)$value);
        if ($time === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $time);
    }
}
