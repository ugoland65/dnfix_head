<?php
namespace App\Services;

use Exception;
use App\Classes\DB;
use App\Core\AuthAdmin;
use App\Models\GodoOrderModel;
use App\Models\GodoOrderGoodsModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GodoPurchaseOrderService
{
    /**
     * 선택한 고도 주문상품으로 구매대행 발주서를 생성한다.
     *
     * @param array $orderGoodsSnos
     * @param string $requestedOrderName
     * @return array
     * @throws Exception
     */
    public function createPurchaseOrderSheet(array $orderGoodsSnos, string $requestedOrderName = ''): array
    {
        $orderGoodsSnos = $this->normalizeOrderGoodsSnos($orderGoodsSnos);
        if (empty($orderGoodsSnos)) {
            throw new Exception('선택된 주문상품이 없습니다.');
        }

        $alreadyOrderedItems = $this->findAlreadyOrderedItems($orderGoodsSnos);
        if (!empty($alreadyOrderedItems)) {
            $duplicateSnos = array_column($alreadyOrderedItems, 'order_goods_sno');
            $duplicateSnos = array_values(array_unique(array_filter(array_map('strval', $duplicateSnos), function ($value) {
                return trim($value) !== '';
            })));
            $preview = implode(', ', array_slice($duplicateSnos, 0, 5));
            if (count($duplicateSnos) > 5) {
                $preview .= ' 외 ' . (count($duplicateSnos) - 5) . '건';
            }

            throw new Exception('이미 발주서에 포함된 주문상품이 있습니다. 주문상품번호: ' . $preview);
        }

        $goodsRows = GodoOrderGoodsModel::query()
            ->whereIn('order_goods_sno', $orderGoodsSnos)
            ->get()
            ->toArray();

        if (count($goodsRows) !== count($orderGoodsSnos)) {
            throw new Exception('선택한 주문상품 중 일부를 찾을 수 없습니다.');
        }

        $supplierMap = [];
        $orderNoMap = [];
        foreach ($goodsRows as $goodsRow) {
            $scmName = trim((string)($goodsRow['scm_name'] ?? ''));
            $supplierKey = ($scmName !== '') ? $scmName : '(공급사 미지정)';
            $supplierMap[$supplierKey] = true;

            $orderNo = trim((string)($goodsRow['order_no'] ?? ''));
            if ($orderNo !== '') {
                $orderNoMap[$orderNo] = true;
            }
        }

        if (count($supplierMap) > 1) {
            throw new Exception('같은 공급사 상품만 발주서가 생성이 가능합니다');
        }

        $orderRows = [];
        if (!empty($orderNoMap)) {
            $orderRows = GodoOrderModel::query()
                ->whereIn('order_no', array_keys($orderNoMap))
                ->get()
                ->toArray();
        }
        $orderByOrderNo = [];
        foreach ($orderRows as $orderRow) {
            $orderNo = trim((string)($orderRow['order_no'] ?? ''));
            if ($orderNo !== '') {
                $orderByOrderNo[$orderNo] = $orderRow;
            }
        }

        $supplierName = (string)array_key_first($supplierMap);
        $supplierNameForFile = preg_replace('/[^a-zA-Z0-9가-힣_-]/u', '_', $supplierName);
        $supplierNameForFile = trim((string)$supplierNameForFile, '_');
        if ($supplierNameForFile === '') {
            $supplierNameForFile = 'supplier';
        }

        $createdBy = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $createdName = trim((string)(AuthAdmin::getSession('sess_name') ?? ''));
        $now = date('Y-m-d H:i:s');

        $orderName = $this->resolveOrderName($requestedOrderName, $supplierName, $supplierNameForFile, $now);
        $poCode = 'PO-' . date('Ymd-His');

        $purchaseOrderIdx = 0;
        $itemCount = 0;
        $totalQuantity = 0;
        $totalAmount = 0.0;

        foreach ($goodsRows as $goodsRow) {
            $goodsCount = (int)($goodsRow['goods_count'] ?? 0);
            if ($goodsCount < 1) {
                $goodsCount = 1;
            }
            $goodsPrice = (float)($goodsRow['goods_price'] ?? 0);
            $goodsTotalPrice = (float)($goodsRow['goods_total_price'] ?? 0);
            if ($goodsTotalPrice <= 0) {
                $goodsTotalPrice = $goodsPrice * $goodsCount;
            }

            $itemCount++;
            $totalQuantity += $goodsCount;
            $totalAmount += $goodsTotalPrice;
        }

        try {
            DB::transaction(function () use (
            $orderName,
            $poCode,
            $supplierName,
            $goodsRows,
            $orderByOrderNo,
            $itemCount,
            $totalQuantity,
            $totalAmount,
            $createdBy,
            $createdName,
            $now,
            &$purchaseOrderIdx
            ) {
                $supplierNo = (int)($goodsRows[0]['scm_no'] ?? 0);

                $purchaseOrder = PurchaseOrderModel::create([
                    'order_name' => $orderName,
                    'po_code' => $poCode,
                    'supplier_no' => ($supplierNo > 0 ? $supplierNo : null),
                    'supplier_name' => $supplierName,
                    'item_count' => $itemCount,
                    'total_quantity' => $totalQuantity,
                    'total_amount' => number_format($totalAmount, 2, '.', ''),
                    'status' => 'created',
                    'memo' => '고도몰 구매대행 주문에서 자동 생성된 발주서',
                    'created_by' => ($createdBy > 0 ? $createdBy : null),
                    'created_name' => ($createdName !== '' ? $createdName : null),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $purchaseOrderIdx = (int)($purchaseOrder->idx ?? 0);
                if ($purchaseOrderIdx < 1) {
                    throw new Exception('구매대행 발주서 생성에 실패했습니다.');
                }

                foreach ($goodsRows as $goodsRow) {
                    $orderNo = trim((string)($goodsRow['order_no'] ?? ''));
                    $orderRow = $orderByOrderNo[$orderNo] ?? [];

                    $goodsCount = (int)($goodsRow['goods_count'] ?? 0);
                    if ($goodsCount < 1) {
                        $goodsCount = 1;
                    }
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
                        'created_by' => ($createdBy > 0 ? $createdBy : null),
                        'created_name' => ($createdName !== '' ? $createdName : null),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $goodsIdx = (int)($goodsRow['idx'] ?? 0);
                    if ($goodsIdx > 0) {
                        $this->updatePurchaseStatus($goodsIdx, [
                            'purchase_status' => '발주서생성',
                            'purchase_order_idx' => $purchaseOrderIdx,
                            'purchase_order_date' => $now,
                            'purchase_order_admin' => ($createdBy > 0 ? $createdBy : null),
                            'purchase_order_admin_name' => ($createdName !== '' ? $createdName : null),
                            'updated_at' => $now,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            if (strpos((string)$e->getMessage(), 'uniq_order_goods_sno') !== false || strpos((string)$e->getMessage(), '1062') !== false) {
                throw new Exception('이미 발주서에 포함된 주문상품이 있습니다. 새로고침 후 다시 선택해 주세요.');
            }
            throw $e;
        }

        if ($purchaseOrderIdx < 1) {
            throw new Exception('발주서 생성 결과를 확인할 수 없습니다.');
        }

        return [
            'purchase_order_idx' => $purchaseOrderIdx,
            'download_url' => '/admin/order/godo_order_purchase/excel?purchase_order_idx=' . $purchaseOrderIdx,
            'message' => '발주서가 생성되었습니다.',
        ];
    }

    /**
     * 입력된 발주서명을 규칙에 맞게 확정한다.
     * - 빈값: 공급사명-YYYYMMDD- 기본값 사용
     * - '-'로 끝나는 경우: 같은 날짜/공급사 기준 순번을 뒤에 붙인다.
     *
     * @param string $requestedOrderName
     * @param string $supplierName
     * @param string $supplierNameForFile
     * @param string $now
     * @return string
     */
    private function resolveOrderName(string $requestedOrderName, string $supplierName, string $supplierNameForFile, string $now): string
    {
        $requestedOrderName = trim($requestedOrderName);
        if ($requestedOrderName === '') {
            $requestedOrderName = $supplierNameForFile . '-' . date('Ymd', strtotime($now)) . '-';
        }

        if (substr($requestedOrderName, -1) === '-') {
            $sequence = $this->getNextDailySupplierSequence($supplierName, $now);
            return $requestedOrderName . $sequence;
        }

        return $requestedOrderName;
    }

    /**
     * 같은 날짜/공급사 기준 다음 순번을 반환한다.
     * 예: 오늘 동일 공급사 발주가 3건이면 4 반환
     *
     * @param string $supplierName
     * @param string $now
     * @return int
     */
    private function getNextDailySupplierSequence(string $supplierName, string $now): int
    {
        $today = date('Y-m-d', strtotime($now));
        $rows = PurchaseOrderModel::query()
            ->select(['order_name'])
            ->where('supplier_name', '=', $supplierName)
            ->whereRaw("DATE(created_at) = '" . addslashes($today) . "'")
            ->get()
            ->toArray();

        $maxSuffix = 0;
        foreach ($rows as $row) {
            $orderName = trim((string)($row['order_name'] ?? ''));
            if ($orderName === '') {
                continue;
            }

            if (preg_match('/(\d+)\s*$/', $orderName, $matches)) {
                $suffix = (int)($matches[1] ?? 0);
                if ($suffix > $maxSuffix) {
                    $maxSuffix = $suffix;
                }
            }
        }

        $existingCount = count($rows);
        $base = max($maxSuffix, $existingCount);
        return $base + 1;
    }

    /**
     * 이미 발주서에 포함된 order_goods_sno 목록 조회
     *
     * @param array $orderGoodsSnos
     * @return array
     */
    private function findAlreadyOrderedItems(array $orderGoodsSnos): array
    {
        if (empty($orderGoodsSnos)) {
            return [];
        }

        return PurchaseOrderItemModel::query()
            ->select(['order_goods_sno'])
            ->whereIn('order_goods_sno', $orderGoodsSnos)
            ->get()
            ->toArray();
    }

    /**
     * 발주서 번호 기준 XLSX 데이터를 생성한다.
     *
     * @param int $purchaseOrderIdx
     * @return array
     * @throws Exception
     */
    public function buildPurchaseOrderCsv(int $purchaseOrderIdx): array
    {
        if ($purchaseOrderIdx < 1) {
            throw new Exception('발주서 번호가 올바르지 않습니다.');
        }

        $purchaseOrder = PurchaseOrderModel::query()
            ->select(['idx', 'order_name'])
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

        if (empty($items)) {
            throw new Exception('다운로드할 발주서 상품이 없습니다.');
        }

        $goodsIdList = [];
        foreach ($items as $item) {
            $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
            if ($goodsId > 0) {
                $goodsIdList[$goodsId] = true;
            }
        }

        $thumbMap = [];
        if (!empty($goodsIdList)) {
            $goodsRows = GodoOrderGoodsModel::query()
                ->select(['idx', 'thumb_image_url'])
                ->whereIn('idx', array_keys($goodsIdList))
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('발주서');

        $headers = [
            '상품이미지',
            '상품명',
            '옵션정보',
            '수량',
            '수령자명',
            '수령자전화',
            '수령자휴대폰',
            '우편번호',
            '주소',
            '상세주소',
            '배송메모',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:K1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(42);
        $sheet->getColumnDimension('J')->setWidth(32);
        $sheet->getColumnDimension('K')->setWidth(42);

        $tempImagePaths = [];
        $rowNumber = 2;
        foreach ($items as $item) {
            $optionText = $this->convertOptionInfoToText((string)($item['option_info'] ?? ''));
            $goodsId = (int)($item['godo_order_goods_id'] ?? 0);
            $thumbImageUrl = $thumbMap[$goodsId] ?? '';

            $sheet->setCellValue('B' . $rowNumber, (string)($item['goods_name'] ?? ''));
            $sheet->setCellValue('C' . $rowNumber, $optionText);
            $sheet->setCellValue('D' . $rowNumber, (string)($item['goods_count'] ?? '0'));
            $sheet->setCellValue('E' . $rowNumber, (string)($item['receiver_name'] ?? ''));
            $sheet->setCellValue('F' . $rowNumber, (string)($item['receiver_phone'] ?? ''));
            $sheet->setCellValue('G' . $rowNumber, (string)($item['receiver_cell_phone'] ?? ''));
            $sheet->setCellValue('H' . $rowNumber, (string)($item['receiver_zonecode'] ?? ''));
            $sheet->setCellValue('I' . $rowNumber, (string)($item['receiver_address'] ?? ''));
            $sheet->setCellValue('J' . $rowNumber, (string)($item['receiver_address_sub'] ?? ''));
            $sheet->setCellValue('K' . $rowNumber, (string)($item['order_memo'] ?? ''));

            $sheet->getStyle('A' . $rowNumber . ':K' . $rowNumber)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('C' . $rowNumber . ':K' . $rowNumber)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($rowNumber)->setRowHeight(64);

            $tempImagePath = $this->downloadImageToTempFile($thumbImageUrl);
            if ($tempImagePath !== null) {
                $tempImagePaths[] = $tempImagePath;

                $drawing = new Drawing();
                $drawing->setName('상품이미지');
                $drawing->setDescription('상품이미지');
                $drawing->setPath($tempImagePath);
                $drawing->setCoordinates('A' . $rowNumber);
                $drawing->setOffsetX(6);
                $drawing->setOffsetY(4);
                $drawing->setHeight(56);
                $drawing->setWorksheet($sheet);
            } else {
                $sheet->setCellValue('A' . $rowNumber, $thumbImageUrl !== '' ? $thumbImageUrl : '이미지없음');
            }

            $rowNumber++;
        }

        $xlsxBinary = '';
        $tempXlsxFile = tempnam(sys_get_temp_dir(), 'po_xlsx_');
        if ($tempXlsxFile === false) {
            throw new Exception('엑셀 임시파일 생성에 실패했습니다.');
        }

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempXlsxFile);
            $xlsxBinary = (string)file_get_contents($tempXlsxFile);
        } finally {
            if (is_file($tempXlsxFile)) {
                @unlink($tempXlsxFile);
            }
            foreach ($tempImagePaths as $tempImagePath) {
                if (is_file($tempImagePath)) {
                    @unlink($tempImagePath);
                }
            }
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        if ($xlsxBinary === '') {
            throw new Exception('엑셀 데이터 생성에 실패했습니다.');
        }

        $fileBase = trim((string)($purchaseOrder['order_name'] ?? 'purchase_order'));
        $fileBase = preg_replace('/[^a-zA-Z0-9가-힣_-]/u', '_', $fileBase);
        $fileBase = trim((string)$fileBase, '_');
        if ($fileBase === '') {
            $fileBase = 'purchase_order_' . $purchaseOrderIdx;
        }

        return [
            'filename' => $fileBase . '.xlsx',
            'content' => $xlsxBinary,
        ];
    }

    /**
     * 원격 이미지 URL을 엑셀 삽입용 임시 파일로 저장한다.
     *
     * @param string $imageUrl
     * @return string|null
     */
    private function downloadImageToTempFile(string $imageUrl): ?string
    {
        $imageUrl = trim($imageUrl);
        if ($imageUrl === '') {
            return null;
        }

        $binary = false;
        $context = stream_context_create([
            'http' => [
                'timeout' => 8,
                'follow_location' => 1,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $binary = @file_get_contents($imageUrl, false, $context);

        if ($binary === false && function_exists('curl_init')) {
            $ch = curl_init($imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $binary = curl_exec($ch);
            curl_close($ch);
        }

        if ($binary === false || $binary === '') {
            return null;
        }

        $imageInfo = @getimagesizefromstring($binary);
        if (!is_array($imageInfo) || !isset($imageInfo[2])) {
            return null;
        }

        $extension = image_type_to_extension((int)$imageInfo[2], false);
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        if (!in_array($extension, $allowedExt, true)) {
            $extension = 'jpg';
        }

        $tempBase = tempnam(sys_get_temp_dir(), 'po_img_');
        if ($tempBase === false) {
            return null;
        }

        $tempPath = $tempBase . '.' . $extension;
        @unlink($tempBase);

        $writtenBytes = @file_put_contents($tempPath, $binary);
        if ($writtenBytes === false || $writtenBytes < 1) {
            if (is_file($tempPath)) {
                @unlink($tempPath);
            }
            return null;
        }

        return $tempPath;
    }

    /**
     * @param array $orderGoodsSnos
     * @return array
     */
    private function normalizeOrderGoodsSnos(array $orderGoodsSnos): array
    {
        $normalized = [];
        foreach ($orderGoodsSnos as $sno) {
            $sno = trim((string)$sno);
            if ($sno === '' || !ctype_digit($sno)) {
                continue;
            }
            $sno = ltrim($sno, '0');
            if ($sno === '') {
                $sno = '0';
            }
            if ($sno === '0') {
                continue;
            }
            $normalized[$sno] = true;
        }
        return array_keys($normalized);
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

    /**
     * 발주 생성 후 주문상품 상태/연결정보를 저장한다.
     *
     * @param int $goodsIdx
     * @param array $payload
     * @return void
     */
    private function updatePurchaseStatus(int $goodsIdx, array $payload): void
    {
        GodoOrderGoodsModel::update(['idx' => $goodsIdx], $payload);
    }
}
